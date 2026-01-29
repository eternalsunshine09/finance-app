<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\Asset;
use App\Models\Portfolio;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    // =========================================================================
    // 1. FITUR BELI ASET (BUY)
    // =========================================================================
    
    /**
     * Menampilkan formulir pembelian aset.
     */
    public function showBuyForm(Request $request)
        {
            $user = Auth::user();
            
            // Ambil daftar aset untuk dropdown
            $assets = Asset::orderBy('name')->get();
            
            // Ambil semua dompet user
            $wallets = Wallet::where('user_id', $user->id)->get();

            // Cek jika ada parameter pre-filled dari halaman valas
            $selectedAsset = $request->query('asset');
            $prefilledAmount = $request->query('amount');

            return view('transactions.buy', compact('assets', 'wallets', 'selectedAsset', 'prefilledAmount'));
        }

        public function processBuy(Request $request)
        {
            $request->validate([
                'wallet_id'    => 'required|exists:wallets,id',
                'asset_symbol' => 'required|exists:assets,symbol',
                'amount'       => 'required|numeric|min:0.00000001', 
                'buy_price'    => 'required|numeric|min:0',          
                'fee'          => 'nullable|numeric|min:0',          
            ]);

            $user = Auth::user();
            
            DB::transaction(function () use ($request, $user) {
                
                // 1. Ambil Wallet
                $wallet = Wallet::where('id', $request->wallet_id)
                                ->where('user_id', $user->id)
                                ->lockForUpdate()
                                ->firstOrFail();
                                
                // 2. Hitung Total Biaya
                $feeAmount = $request->fee ?? 0;
                $subtotal  = $request->amount * $request->buy_price;
                $totalCost = $subtotal + $feeAmount;

                // 3. Cek Kecukupan Saldo
                if ($wallet->balance < $totalCost) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'amount' => "Saldo tidak cukup. Total tagihan: " . number_format($totalCost)
                    ]);
                }

                // 4. Potong Saldo Wallet
                $wallet->decrement('balance', $totalCost);

                // 5. Update Portfolio
                $portfolio = Portfolio::firstOrCreate(
                    ['user_id' => $user->id, 'asset_symbol' => $request->asset_symbol],
                    ['quantity' => 0, 'average_buy_price' => 0]
                );

                // Hitung Average Down
                $oldTotalVal = $portfolio->quantity * $portfolio->average_buy_price;
                $newTotalVal = $oldTotalVal + $subtotal; 
                $newQuantity = $portfolio->quantity + $request->amount;
                
                $newAvgPrice = $newQuantity > 0 ? $newTotalVal / $newQuantity : 0;

                $portfolio->update([
                    'quantity'          => $newQuantity,
                    'average_buy_price' => $newAvgPrice
                ]);

                // 6. Catat Transaksi
                Transaction::create([
                    'user_id'        => $user->id,
                    'wallet_id'      => $wallet->id,
                    'type'           => 'BUY',
                    'status'         => 'approved', 
                    'asset_symbol'   => $request->asset_symbol,
                    'amount'         => $request->amount,
                    'price_per_unit' => $request->buy_price,
                    'amount_cash'    => -$totalCost, 
                    'description'    => "Beli " . $request->asset_symbol . " (Fee: " . number_format($feeAmount) . ")",
                    'date'           => now(),
                ]);
            });

            return redirect()->route('portfolio.index')->with('success', 'Pembelian Aset Berhasil! Cek Portfolio Anda.');
        }

    // =========================================================================
    // 2. FITUR JUAL ASET (SELL)
    // =========================================================================

    /**
     * Menampilkan formulir penjualan aset.
     */
    public function sell($symbol = null)
    {
        $user = Auth::user();
        $myPortfolio = Portfolio::where('user_id', $user->id)->where('quantity', '>', 0)->get();
        $wallets = Wallet::where('user_id', $user->id)->get();
        $assets = Asset::all();
        return view('transactions.sell', compact('myPortfolio', 'wallets', 'assets', 'symbol'));
    }

    /**
     * Memproses penjualan aset.
     * Mengurangi unit portfolio -> Menambah saldo wallet -> Mencatat Realized Profit.
     */
    public function processSell(Request $request)
        {
            $request->validate([
                'wallet_id'    => 'required|exists:wallets,id',
                'asset_symbol' => 'required',
                'amount'       => 'required|numeric|min:0.00000001',
                'sell_price'   => 'required|numeric|min:0',
                'fee'          => 'nullable|numeric|min:0',
            ]);

            $user = Auth::user();

            return DB::transaction(function () use ($request, $user) {
                $portfolio = Portfolio::where('user_id', $user->id)->where('asset_symbol', $request->asset_symbol)->first();

                if (!$portfolio || $portfolio->quantity < $request->amount) {
                    return back()->withErrors(['amount' => 'Unit aset tidak mencukupi untuk dijual.'])->withInput();
                }

                $subtotal    = $request->amount * $request->sell_price;
                $feeAmount   = $request->fee ?? 0;
                $netProceeds = $subtotal - $feeAmount;

                $capitalCost = $portfolio->average_buy_price * $request->amount;
                $profit      = ($subtotal - $capitalCost) - $feeAmount;

                $wallet = Wallet::where('id', $request->wallet_id)->where('user_id', $user->id)->firstOrFail();
                $wallet->increment('balance', $netProceeds);
                $portfolio->decrement('quantity', $request->amount);

                Transaction::create([
                    'user_id'        => $user->id,
                    'wallet_id'      => $wallet->id,
                    'type'           => 'SELL',
                    'status'         => 'approved',
                    'asset_symbol'   => $request->asset_symbol,
                    'amount'         => $request->amount,
                    'price_per_unit' => $request->sell_price,
                    'amount_cash'    => $netProceeds,
                    'profit_amount'  => $profit,
                    'description'    => "Jual " . $request->asset_symbol . " (Fee: " . number_format($feeAmount) . ")",
                    'date'           => now(),
                ]);

                return redirect()->route('portfolio.index')->with('success', 'Penjualan berhasil!');
            });
        }

    // =========================================================================
    // 3. FITUR TOP UP (DEPOSIT)
    // =========================================================================

    public function showTopUpForm()
    {
        $user = Auth::user();
        $wallets = Wallet::where('user_id', $user->id)->get();
        return view('transactions.topup', compact('wallets'));
    } 
    
    public function topUp(Request $request)
    {
        $request->validate([
            'wallet_id'     => 'required|exists:wallets,id',
            'amount'        => 'required|numeric|min:1',
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        DB::transaction(function () use ($request) {
            $user = Auth::user();
            
            // 1. Upload Bukti (Opsional)
            $proofPath = null;
            if ($request->hasFile('payment_proof')) {
                $proofPath = $request->file('payment_proof')->store('receipts', 'public');
            }

            // 2. Update Saldo Wallet Langsung
            $wallet = Wallet::where('id', $request->wallet_id)
                            ->where('user_id', $user->id)
                            ->firstOrFail();

            $wallet->increment('balance', $request->amount);

            $transactionDate = $request->created_at ? $request->created_at : now();

            // 3. Catat Transaksi
            Transaction::create([
                'user_id'        => $user->id,
                'wallet_id'      => $wallet->id,
                'type'           => 'TOPUP',
                'amount_cash'    => $request->amount,
                'amount'         => 0, // 0 karena bukan aset
                'price_per_unit' => 1, // Dummy agar tidak error
                'asset_symbol'   => null,
                'date'           => $transactionDate, 
                'created_at'     => $transactionDate,
                'status'         => 'approved',
                'description'    => 'Setor Tunai / Top Up Manual',
                'payment_proof'  => $proofPath 
            ]);
        });

        return redirect()->route('wallet.index')->with('success', 'Saldo berhasil ditambahkan!');
    }

    // =========================================================================
    // 4. FITUR WITHDRAW (TARIK DANA)
    // =========================================================================

    public function showWithdrawForm()
    {
        return view('transactions.withdraw');
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'amount'   => 'required|numeric|min:10000',
            'currency' => 'required|in:IDR,USD',
        ]);

        DB::transaction(function () use ($request) {
            $user = Auth::user();

            // 1. Cari Wallet yang sesuai mata uangnya
            $wallet = Wallet::where('user_id', $user->id)
                            ->where('currency', $request->currency)
                            ->first();

            // 2. Cek Saldo
            if (!$wallet || $wallet->balance < $request->amount) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => 'Saldo tidak cukup untuk penarikan ini!',
                ]);
            }

            // 3. Kurangi Saldo
            $wallet->decrement('balance', $request->amount);

            // 4. Catat Transaksi (Status Pending)
            Transaction::create([
                'user_id'        => $user->id,
                'wallet_id'      => $wallet->id,
                'type'           => 'WITHDRAW',
                'amount_cash'    => -$request->amount, // Negatif karena uang keluar
                'amount'         => 0,
                'created_at'     => $request->custom_date ?? now(),
                'date'           => $request->custom_date ?? now(),
                'status'         => 'pending', // Menunggu persetujuan admin/transfer manual
                'description'    => 'Penarikan Dana ' . $request->currency
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Permintaan penarikan berhasil dibuat.');
    }

    // =========================================================================
    // 5. RIWAYAT TRANSAKSI & EDIT
    // =========================================================================

    public function history(Request $request)
    {
        $wallets = Wallet::where('user_id', Auth::id())->get();

        $query = Transaction::where('user_id', Auth::id())->with('wallet');

        // Filter berdasarkan Wallet ID jika ada
        if ($request->has('wallet_id') && $request->wallet_id != '') {
            $query->where('wallet_id', $request->wallet_id);
        }

        $transactions = $query->latest()->paginate(10);

        return view('transactions.history', compact('transactions', 'wallets'));
    }

    public function edit($id)
    {
        $transaction = Transaction::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
        return view('transactions.edit', compact('transaction'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'amount_cash' => 'required|numeric|min:1',
            'date'        => 'required|date',
            'description' => 'nullable|string|max:255'
        ]);

        DB::transaction(function () use ($request, $id) {
            $transaction = Transaction::where('id', $id)->where('user_id', Auth::id())->lockForUpdate()->firstOrFail();
            $wallet = Wallet::where('id', $transaction->wallet_id)->lockForUpdate()->firstOrFail();
            
            // Hitung selisih nominal baru vs lama
            $oldCash = abs($transaction->amount_cash);
            $newCash = $request->amount_cash;
            $diff = $newCash - $oldCash;

            // Update Saldo Wallet
            if ($transaction->type == 'TOPUP' || $transaction->type == 'SELL') {
                $wallet->balance += $diff; // Pemasukan nambah = saldo nambah
            } else {
                // Pengeluaran nambah = saldo berkurang
                if ($diff > 0 && $wallet->balance < $diff) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['amount_cash' => 'Saldo tidak cukup untuk koreksi ini.']);
                }
                $wallet->balance -= $diff;
            }
            $wallet->save();

            // Update Transaksi
            $finalAmount = ($transaction->type == 'WITHDRAW' || $transaction->type == 'BUY') ? -$newCash : $newCash;
            
            $transaction->update([
                'amount_cash' => $finalAmount,
                'created_at'  => $request->date,
                'description' => $request->description
            ]);
        });

        return redirect()->route('history')->with('success', 'Transaksi berhasil diperbarui.');
    }

    /**
     * Menghapus transaksi dan mengembalikan saldo/aset (Rollback).
     */
    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $transaction = Transaction::where('id', $id)->where('user_id', Auth::id())->lockForUpdate()->firstOrFail();
            $wallet = Wallet::where('id', $transaction->wallet_id)->lockForUpdate()->firstOrFail();

            // 1. KEMBALIKAN SALDO DOMPET
            // Rumus: Saldo Baru = Saldo Sekarang - (Nominal Transaksi)
            // Contoh: Hapus Topup (+100) -> 500 - 100 = 400 (Benar)
            // Contoh: Hapus Beli (-100) -> 400 - (-100) = 500 (Benar, uang balik)
            $newBalance = $wallet->balance - $transaction->amount_cash;

            if ($newBalance < 0) {
                throw \Illuminate\Validation\ValidationException::withMessages(['error' => 'Gagal hapus! Saldo menjadi negatif.']);
            }
            $wallet->balance = $newBalance;
            $wallet->save();

            // 2. KEMBALIKAN UNIT ASET (Jika Transaksi Aset)
            if ($transaction->asset_symbol && in_array($transaction->type, ['BUY', 'SELL'])) {
                $portfolio = Portfolio::where('user_id', Auth::id())
                                      ->where('asset_symbol', $transaction->asset_symbol)
                                      ->first();

                if ($portfolio) {
                    if ($transaction->type == 'BUY') {
                        // Hapus Pembelian -> Kurangi Unit
                        if ($portfolio->quantity < $transaction->amount) {
                            throw \Illuminate\Validation\ValidationException::withMessages(['error' => 'Unit aset tidak cukup untuk rollback.']);
                        }
                        $portfolio->decrement('quantity', $transaction->amount);
                    } elseif ($transaction->type == 'SELL') {
                        // Hapus Penjualan -> Kembalikan Unit
                        $portfolio->increment('quantity', $transaction->amount);
                    }
                }
            }

            $transaction->delete();
        });

        return back()->with('success', 'Transaksi dihapus & saldo dikembalikan.');
    }

    // =========================================================================
    // 6. CORPORATE ACTIONS (DIVIDEN, SPLIT, RIGHT ISSUE)
    // =========================================================================

    public function formDividendCash() {
        $portfolios = Portfolio::with('asset')->where('user_id', Auth::id())->where('quantity', '>', 0)->get();
        $wallets = Wallet::where('user_id', Auth::id())->get();
        return view('transactions.corporate.dividend_cash', compact('wallets', 'portfolios'));
    }

    public function formDividendUnit() {
        $portfolios = Portfolio::where('user_id', Auth::id())->where('quantity', '>', 0)->get();
        return view('transactions.corporate.dividend_unit', compact('portfolios'));
    }

    public function formStockSplit() {
        $portfolios = Portfolio::where('user_id', Auth::id())->where('quantity', '>', 0)->get();
        return view('transactions.corporate.stock_split', compact('portfolios'));
    }

    public function formRightIssue() {
        $wallets = Wallet::where('user_id', Auth::id())->get();
        $portfolios = Portfolio::where('user_id', Auth::id())->where('quantity', '>', 0)->get();
        return view('transactions.corporate.right_issue', compact('wallets', 'portfolios'));
    }

    public function formBonus() {
        return $this->formDividendUnit(); 
    }

    /**
     * Memproses Aksi Korporasi (Dividen, Split, dll)
     */
    public function processCorporateAction(Request $request)
    {
        $user = Auth::user();
        $type = $request->action_type; 

        return DB::transaction(function () use ($request, $user, $type) {
            $portfolio = Portfolio::where('user_id', $user->id)
                                  ->where('asset_symbol', $request->asset_symbol)
                                  ->firstOrFail();

            $wallet = null;
            $cashFlow = 0;
            $desc = "";

            // A. DIVIDEN TUNAI
            if ($type == 'DIV_CASH') {
                $request->validate(['amount_received' => 'required|numeric|min:1', 'wallet_id' => 'required']);
                
                $wallet = Wallet::where('id', $request->wallet_id)->where('user_id', $user->id)->firstOrFail();
                $wallet->increment('balance', $request->amount_received);

                $desc = "Dividen Tunai: " . $request->asset_symbol;
                $cashFlow = $request->amount_received;
            } 
            
            // B. DIVIDEN SAHAM / BONUS
            elseif ($type == 'DIV_UNIT' || $type == 'BONUS') {
                $request->validate(['quantity_received' => 'required|numeric|min:1']);

                // Average Price TURUN karena jumlah unit bertambah tapi modal tetap
                $oldTotalValue = $portfolio->quantity * $portfolio->average_buy_price;
                $newQuantity = $portfolio->quantity + $request->quantity_received;
                $newAvgPrice = $oldTotalValue / $newQuantity;

                $portfolio->update([
                    'quantity' => $newQuantity,
                    'average_buy_price' => $newAvgPrice
                ]);

                $desc = "Dividen Saham/Bonus: +" . $request->quantity_received . " unit " . $request->asset_symbol;
            }
            
            // C. STOCK SPLIT
            elseif ($type == 'SPLIT') {
                $request->validate(['split_ratio' => 'required|numeric|min:0.1']);
                $ratio = $request->split_ratio;
                
                $portfolio->quantity = $portfolio->quantity * $ratio;
                $portfolio->average_buy_price = $portfolio->average_buy_price / $ratio;
                $portfolio->save();

                $desc = "Stock Split " . $request->asset_symbol . " Rasio 1:" . $ratio;
            }
            
            // D. RIGHT ISSUE (Tebus Saham)
            elseif ($type == 'RIGHT_ISSUE') {
                $request->validate(['quantity' => 'required|numeric', 'exercise_price' => 'required|numeric', 'wallet_id' => 'required']);

                $wallet = Wallet::where('id', $request->wallet_id)->where('user_id', $user->id)->firstOrFail();
                $cost = $request->quantity * $request->exercise_price;

                if ($wallet->balance < $cost) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['error' => 'Saldo tidak cukup.']);
                }
                $wallet->decrement('balance', $cost);

                // Update Portfolio (Average Down)
                $oldVal = $portfolio->quantity * $portfolio->average_buy_price;
                $newVal = $oldVal + $cost;
                $newQty = $portfolio->quantity + $request->quantity;
                
                $portfolio->update([
                    'quantity' => $newQty,
                    'average_buy_price' => $newVal / $newQty
                ]);

                $desc = "Right Issue " . $request->asset_symbol . " @ " . number_format($request->exercise_price);
                $cashFlow = -$cost;
            }

            // Catat Transaksi
            Transaction::create([
                'user_id'        => $user->id,
                'wallet_id'      => $wallet ? $wallet->id : null,
                'type'           => $type,
                'status'         => 'approved',
                'asset_symbol'   => $request->asset_symbol,
                'amount'         => $request->quantity ?? $request->quantity_received ?? 0,
                'price_per_unit' => $request->exercise_price ?? 0,
                'amount_cash'    => $cashFlow,
                'description'    => $desc,
                'date'           => $request->date ?? now(),
                // Profit tidak dihitung di corporate action (biasanya dianggap return investment)
                'profit_amount'  => 0 
            ]);

            return redirect()->route('portfolio.index')->with('success', 'Corporate Action berhasil diproses!');
        });
    }

    // API Helper untuk AJAX
    public function getPrice($symbol)
    {
        $asset = Asset::where('symbol', $symbol)->first();
        return response()->json(['price' => $asset ? $asset->current_price : 0]);
    }
}