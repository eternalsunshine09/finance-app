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
    // ===========================
    // 1. FITUR BELI ASET (FIXED)
    // ===========================
    
    public function showBuyForm()
    {
        $user = Auth::user();
        $assets = Asset::orderBy('name')->get();
        
        // Ambil SEMUA wallet (walau saldo 0) agar user bisa lihat opsinya
        $wallets = Wallet::where('user_id', $user->id)->get();

        return view('transactions.buy', compact('assets', 'wallets'));
    }

    public function processBuy(Request $request)
{
    $request->validate([
        'wallet_id' => 'required|exists:wallets,id',
        'asset_symbol' => 'required|exists:assets,symbol',
        'amount' => 'required|numeric|min:0.00000001',
        'buy_price' => 'required|numeric|min:0',
        'fee' => 'nullable|numeric|min:0', // Validasi input fee manual
    ]);

    $user = Auth::user();
    
    DB::transaction(function () use ($request, $user) {
        
        $wallet = Wallet::where('id', $request->wallet_id)
                        ->where('user_id', $user->id)
                        ->lockForUpdate()
                        ->firstOrFail();
                        
        $asset = Asset::where('symbol', $request->asset_symbol)->firstOrFail();

        // 1. Ambil Fee dari Input Manual (Default 0 jika kosong)
        $feeAmount = $request->fee ?? 0;
        
        // 2. Hitung Total Biaya
        $subtotal = $request->amount * $request->buy_price;
        $totalCost = $subtotal + $feeAmount;

        // 3. Cek Saldo
        if ($wallet->balance < $totalCost) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'amount' => "Saldo tidak cukup untuk membayar tagihan + fee broker."
            ]);
        }

        // 4. Potong Saldo
        $wallet->decrement('balance', $totalCost);

        // 5. Update/Buat Portfolio
        $portfolio = Portfolio::firstOrCreate(
            ['user_id' => $user->id, 'asset_symbol' => $request->asset_symbol],
            ['quantity' => 0, 'average_buy_price' => 0]
        );

        // Average Down Logic
        // Catatan: Fee biasanya TIDAK dimasukkan ke average price aset, 
        // tapi dicatat sebagai pengeluaran terpisah. 
        // Jika Anda ingin Fee masuk ke harga modal, ubah:
        // $newTotalVal = $oldTotalVal + $subtotal + $feeAmount;
        
        $oldTotalVal = $portfolio->quantity * $portfolio->average_buy_price;
        $newTotalVal = $oldTotalVal + $subtotal; // Fee tidak masuk avg price
        $newQuantity = $portfolio->quantity + $request->amount;
        $newAvgPrice = $newQuantity > 0 ? $newTotalVal / $newQuantity : 0;

        $portfolio->update([
            'quantity' => $newQuantity,
            'average_buy_price' => $newAvgPrice
        ]);

        // 6. Catat Transaksi
        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'BUY',
            'status' => 'approved',
            'asset_symbol' => $request->asset_symbol,
            'amount' => $request->amount,
            'price_per_unit' => $request->buy_price,
            'amount_cash' => -$totalCost, // Cash keluar (termasuk fee)
            'description' => "Beli " . $request->asset_symbol . " (Fee: " . number_format($feeAmount, 2) . ")",
            'date' => now(),
        ]);
    });

    return redirect()->route('wallet.index')->with('success', 'Pembelian Aset Berhasil!');
}
    public function sell($symbol = null)
    {
        $user = Auth::user();
        // Hanya ambil aset yang benar-benar dimiliki user
        $myPortfolio = Portfolio::where('user_id', $user->id)
                                ->where('quantity', '>', 0)
                                ->get();
                                
        $wallets = Wallet::where('user_id', $user->id)->get();
        
        // Ambil data harga live untuk semua aset (untuk autofill harga jual)
        $assets = Asset::all();

        return view('transactions.sell', compact('myPortfolio', 'wallets', 'assets', 'symbol'));
    }

public function processSell(Request $request)
{
    $request->validate([
        'wallet_id' => 'required|exists:wallets,id',
        'asset_symbol' => 'required',
        'amount' => 'required|numeric|min:0.00000001',
        'sell_price' => 'required|numeric|min:0',
        'fee' => 'nullable|numeric|min:0',
    ]);

    $user = Auth::user();

    return DB::transaction(function () use ($request, $user) {
        // 1. Cek Kepemilikan Aset
        $portfolio = Portfolio::where('user_id', $user->id)
                                ->where('asset_symbol', $request->asset_symbol)
                                ->first();

        if (!$portfolio || $portfolio->quantity < $request->amount) {
            return back()->withErrors(['amount' => 'Unit aset tidak mencukupi untuk dijual.'])->withInput();
        }

        // 2. Hitung Hasil Penjualan
        $subtotal = $request->amount * $request->sell_price;
        $feeAmount = $request->fee ?? 0;
        $netProceeds = $subtotal - $feeAmount; // Hasil bersih (dikurangi fee)

        // 3. Update Dompet (Tambah Saldo)
        $wallet = Wallet::where('id', $request->wallet_id)->where('user_id', $user->id)->firstOrFail();
        $wallet->increment('balance', $netProceeds);

        // 4. Update Portfolio (Kurangi Unit)
        $portfolio->decrement('quantity', $request->amount);

        // 5. Catat Transaksi
        Transaction::create([
            'user_id' => $user->id,
            'wallet_id' => $wallet->id,
            'type' => 'SELL',
            'status' => 'approved',
            'asset_symbol' => $request->asset_symbol,
            'amount' => $request->amount,
            'price_per_unit' => $request->sell_price,
            'amount_cash' => $netProceeds, // Nilai positif karena uang masuk
            'description' => "Jual " . $request->asset_symbol . " (Fee: " . number_format($feeAmount, 2) . ")",
            'date' => now(),
        ]);

        return redirect()->route('portfolio.index')->with('success', 'Penjualan aset berhasil diproses!');
    });
}

    // ===========================
    // 2. API HELPER (PENTING BUAT JS)
    // ===========================
    public function getPrice($symbol)
    {
        $asset = Asset::where('symbol', $symbol)->first();
        return response()->json([
            'price' => $asset ? $asset->current_price : 0
        ]);
    }

    public function sellAsset(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'asset_symbol' => 'required',
            'quantity' => 'required|numeric|min:0.00000001',
            'price_per_unit' => 'required|numeric',
        ]);

        DB::transaction(function () use ($request) {
            $portfolio = Portfolio::where('user_id', $request->user_id)
                                  ->where('asset_symbol', $request->asset_symbol)
                                  ->first();

            if (!$portfolio || $portfolio->quantity < $request->quantity) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantity' => 'Jumlah aset yang dimiliki tidak cukup!',
                ]);
            }

            $totalRevenue = $request->quantity * $request->price_per_unit;
            $wallet = Wallet::where('user_id', $request->user_id)->where('currency', 'IDR')->firstOrFail();

            Transaction::create([
                'user_id' => $request->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'SELL',
                'asset_symbol' => $request->asset_symbol,
                'amount' => -$request->quantity, // Sesuaikan nama kolom dengan database (amount)
                'amount_asset' => -$request->quantity,
                'price_per_unit' => $request->price_per_unit,
                'date' => now(),
            ]);

            $portfolio->decrement('quantity', $request->quantity);
            $wallet->increment('balance', $totalRevenue);
        });

        return redirect()->route('dashboard')
            ->with('success', 'Berhasil menjual '.$request->quantity.' '.$request->asset_symbol);
    }

    // ===========================
    // 3. FITUR TOP UP (UPDATED)
    // ===========================
    public function showTopUpForm()
        {
            $user = Auth::user();
            
            // Ambil semua dompet (termasuk yang saldo 0)
            $wallets = Wallet::where('user_id', $user->id)->get();

            return view('transactions.topup', compact('wallets'));
        } 
    
        public function topUp(Request $request)
    {
        $request->validate([
            'wallet_id'     => 'required|exists:wallets,id',
            'amount'        => 'required|numeric|min:1',
            // Gambar boleh dibuat nullable (opsional) kalau cuma pencatatan
            'payment_proof' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        DB::transaction(function () use ($request) {
            $user = Auth::user();
            
            // 1. Cek Upload Bukti (Opsional)
            $proofPath = null;
            if ($request->hasFile('payment_proof')) {
                $proofPath = $request->file('payment_proof')->store('receipts', 'public');
            }

            // 2. Ambil Dompet Tujuan
            $wallet = Wallet::where('id', $request->wallet_id)
                            ->where('user_id', $user->id)
                            ->firstOrFail();

            // 3. 🔥 UPDATE: LANGSUNG TAMBAH SALDO 🔥
            $wallet->increment('balance', $request->amount);

            // 4. Catat Transaksi (Status Langsung 'approved' / 'success')
            Transaction::create([
                'user_id'       => $user->id,
                'wallet_id'     => $wallet->id,
                'type'          => 'TOPUP',
                'amount_cash'   => $request->amount,
                
                // 🔥 TAMBAHKAN BARIS INI AGAR TIDAK ERROR:
                'amount'        => 0, // Karena ini uang tunai, jumlah unit asetnya 0
                'price_per_unit'=> 1, // 🔥 TAMBAHKAN INI (Nilai dummy agar tidak error)
                'asset_symbol'  => null,
                
                'created_at'    => $request->custom_date ?? now(),
                'date'          => $request->custom_date ?? now(),
                'status'        => 'approved',
                'description'   => 'Setor Tunai / Top Up Manual',
                'payment_proof' => $proofPath 
            ]);
        });

        return redirect()->route('wallet.index')->with('success', 'Saldo berhasil ditambahkan ke dompet!');
    }

    // ===========================
    // 4. FITUR WITHDRAW (TARIK DANA)
    // ===========================
    public function showWithdrawForm()
    {
        return view('transactions.withdraw');
    }

    public function withdraw(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:10000',
            'currency' => 'required|in:IDR,USD',
        ]);

        DB::transaction(function () use ($request) {
            $wallet = Wallet::where('user_id', $request->user_id)
                            ->where('currency', $request->currency)
                            ->first();

            if (!$wallet || $wallet->balance < $request->amount) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => 'Saldo tidak cukup untuk penarikan ini!',
                ]);
            }

            Transaction::create([
                'user_id'     => Auth::id(), // Gunakan Auth::id() agar lebih aman
                'wallet_id'   => $wallet->id,
                'type'        => 'WITHDRAW',
                'amount_cash' => -$request->amount,
                
                // 🔥 TAMBAHKAN BARIS INI:
                'amount'      => 0, // Isi 0 karena ini transaksi uang, bukan aset
                
                'created_at'  => $request->custom_date ?? now(),
                'date'        => $request->custom_date ?? now(),
                'status'      => 'pending',
                'description' => 'Penarikan Dana ' . $request->currency // Tambahkan deskripsi agar rapi
            ]);

            $wallet->decrement('balance', $request->amount);
        });

        return redirect()->route('dashboard')->with('success', 'Permintaan Penarikan Berhasil! Saldo diamankan menunggu transfer Admin.');
    }

    // ===========================
    // 5. FITUR RIWAYAT TRANSAKSI
    // ===========================
    public function history()
    {
        $userId = Auth::id();
        
        $transactions = Transaction::where('user_id', $userId)
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(10);

        return view('transactions.history', compact('transactions'));
    }
    // Tambahkan di App/Http/Controllers/TransactionController.php

    public function edit($id)
{
    $transaction = Transaction::where('id', $id)->where('user_id', Auth::id())->firstOrFail();
    
    // 🔥 PERUBAHAN: Hapus pembatasan tipe. Semua tipe sekarang boleh masuk halaman edit.
    // if (!in_array($transaction->type, ['TOPUP', 'WITHDRAW'])) { ... } <--- HAPUS ATAU KOMENTARI INI

    return view('transactions.edit', compact('transaction'));
}

public function update(Request $request, $id)
{
    $request->validate([
        'amount_cash' => 'required|numeric|min:1',
        'date'        => 'required|date',
        'description' => 'nullable|string'
    ]);

    DB::transaction(function () use ($request, $id) {
        $transaction = Transaction::where('id', $id)->where('user_id', Auth::id())->lockForUpdate()->firstOrFail();
        
        // Cek apakah ini transaksi Konversi (BUY/SELL selain Topup/Withdraw)
        $isExchange = in_array($transaction->type, ['BUY', 'SELL']) && $transaction->asset_symbol == null;

        // JIKA BUKAN KONVERSI (TOPUP/WITHDRAW), BOLEH EDIT NOMINAL
        if (!$isExchange) {
            $wallet = Wallet::where('id', $transaction->wallet_id)->lockForUpdate()->firstOrFail();

            // Hitung Selisih
            $oldAmount = abs($transaction->amount_cash); 
            $newAmount = $request->amount_cash;
            $difference = $newAmount - $oldAmount; 

            // Update Saldo Wallet
            if ($transaction->type == 'TOPUP') {
                $wallet->balance += $difference;
                $finalAmountRecord = $newAmount; 
            } 
            elseif ($transaction->type == 'WITHDRAW') {
                $wallet->balance -= $difference; 
                $finalAmountRecord = -$newAmount;
            }
            // Logic tambahan untuk BUY/SELL Aset (Saham) jika diperlukan nanti
            else {
                $finalAmountRecord = $transaction->amount_cash; // Default tidak berubah
            }

            if ($wallet->balance < 0) {
                throw \Illuminate\Validation\ValidationException::withMessages(['amount_cash' => 'Saldo dompet tidak mencukupi untuk perubahan ini.']);
            }

            $wallet->save();
            
            // Update nominal di transaksi
            $transaction->amount_cash = $finalAmountRecord;
        }
        
        // Update Data Umum (Tanggal & Deskripsi) - Berlaku untuk semua tipe
        $transaction->date = $request->date;
        $transaction->created_at = $request->date; // Update created_at juga agar urutan di history berubah
        $transaction->description = $request->description;
        
        $transaction->save();
    });

    return redirect()->route('history')->with('success', 'Transaksi berhasil diperbarui.');
}

        public function destroy($id)
        {
            DB::transaction(function () use ($id) {
                $transaction = Transaction::where('id', $id)->where('user_id', Auth::id())->lockForUpdate()->firstOrFail();
                $wallet = Wallet::where('id', $transaction->wallet_id)->lockForUpdate()->firstOrFail();

                // LOGIKA PENGEMBALIAN SALDO:
                // 1. Jika Hapus "Uang Masuk" (Positif) -> Saldo Wallet akan DIKURANGI.
                // 2. Jika Hapus "Uang Keluar" (Negatif) -> Saldo Wallet akan DITAMBAH (Refund).
                // Rumus: Saldo Baru = Saldo Lama - Nominal Transaksi
                
                $newBalance = $wallet->balance - $transaction->amount_cash;

                // Cek jika saldo tidak cukup (Misal mau hapus TopUp tapi uangnya udah dipake)
                if ($newBalance < 0) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'error' => 'Gagal hapus! Saldo dompet tidak cukup untuk membatalkan transaksi ini.'
                    ]);
                }

                $wallet->balance = $newBalance;
                $wallet->save();

                $transaction->delete();
            });

            return back()->with('success', 'Transaksi berhasil dihapus dan saldo dikembalikan.');
        }
        // ===========================
    // 6. FORM CORPORATE ACTIONS
    // ===========================
    
// Di dalam TransactionController.php

    public function formDividendCash() {
    // Ambil data portfolio user yang quantity-nya > 0
    // Gunakan 'with' untuk mengambil data detail Asset (Nama, Logo, dll)
        $portfolios = \App\Models\Portfolio::with('asset')
            ->where('user_id', \Illuminate\Support\Facades\Auth::id())
            ->where('quantity', '>', 0)
            ->get();

    // Ambil wallet untuk tujuan transfer
    $wallets = \App\Models\Wallet::where('user_id', \Illuminate\Support\Facades\Auth::id())->get();

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
        // Saham bonus mirip dividen saham, hanya beda istilah akuntansi
        return $this->formDividendUnit(); 
    }

    // ===========================
    // 7. PROSES LOGIKA CORPORATE ACTIONS
    // ===========================
    
    public function processCorporateAction(Request $request)
    {
        $user = Auth::user();
        $type = $request->action_type; // 'DIV_CASH', 'DIV_UNIT', 'SPLIT', 'RIGHT_ISSUE'

        return DB::transaction(function () use ($request, $user, $type) {
            
            // Ambil Portfolio yang terkena dampak
            $portfolio = Portfolio::where('user_id', $user->id)
                                  ->where('asset_symbol', $request->asset_symbol)
                                  ->firstOrFail();

            if ($type == 'DIV_CASH') {
                // DIVIDEN TUNAI: Tambah Saldo Wallet, Portfolio tetap
                $request->validate(['amount_received' => 'required|numeric|min:1', 'wallet_id' => 'required']);
                
                $wallet = Wallet::where('id', $request->wallet_id)->where('user_id', $user->id)->firstOrFail();
                $wallet->increment('balance', $request->amount_received);

                $desc = "Dividen Tunai dari " . $request->asset_symbol;
                $cashFlow = $request->amount_received;
                
            } 
            elseif ($type == 'DIV_UNIT' || $type == 'BONUS') {
                // DIVIDEN SAHAM / BONUS: Tambah Unit, Harga Average Turun
                // Rumus: Total Modal Tetap, dibagi (Qty Lama + Qty Baru)
                $request->validate(['quantity_received' => 'required|numeric|min:1']);

                $oldTotalValue = $portfolio->quantity * $portfolio->average_buy_price;
                $newQuantity = $portfolio->quantity + $request->quantity_received;
                
                // Harga rata-rata baru menjadi lebih murah
                $newAvgPrice = $oldTotalValue / $newQuantity;

                $portfolio->update([
                    'quantity' => $newQuantity,
                    'average_buy_price' => $newAvgPrice
                ]);

                $desc = "Dividen Saham/Bonus: +" . $request->quantity_received . " unit " . $request->asset_symbol;
                $wallet = null; // Tidak melibatkan uang cash
                $cashFlow = 0;
            }
            elseif ($type == 'SPLIT') {
                // STOCK SPLIT: Ubah Quantity & Harga sesuai Rasio
                // Contoh Rasio 1:5 (1 Saham lama jadi 5 Saham baru)
                // Input user: Faktor pengali (misal 5)
                $request->validate(['split_ratio' => 'required|numeric|min:0.1']);

                $ratio = $request->split_ratio; // Misal 5 (Stock Split) atau 0.5 (Reverse Split)
                
                $portfolio->quantity = $portfolio->quantity * $ratio;
                $portfolio->average_buy_price = $portfolio->average_buy_price / $ratio;
                $portfolio->save();

                $desc = "Stock Split " . $request->asset_symbol . " Rasio 1:" . $ratio;
                $wallet = null;
                $cashFlow = 0;
            }
            elseif ($type == 'RIGHT_ISSUE') {
                // RIGHT ISSUE: Seperti Beli Baru (Tebus), tapi ada harga khusus
                // Mengurangi Wallet, Menambah Portfolio, Update Average Price
                $request->validate([
                    'quantity' => 'required|numeric|min:1',
                    'exercise_price' => 'required|numeric|min:1',
                    'wallet_id' => 'required'
                ]);

                $wallet = Wallet::where('id', $request->wallet_id)->where('user_id', $user->id)->firstOrFail();
                $cost = $request->quantity * $request->exercise_price;

                if ($wallet->balance < $cost) {
                    throw \Illuminate\Validation\ValidationException::withMessages(['error' => 'Saldo tidak cukup untuk tebus Right Issue']);
                }

                $wallet->decrement('balance', $cost);

                // Hitung Average Down
                $oldVal = $portfolio->quantity * $portfolio->average_buy_price;
                $newVal = $oldVal + $cost;
                $newQty = $portfolio->quantity + $request->quantity;
                
                $portfolio->update([
                    'quantity' => $newQty,
                    'average_buy_price' => $newVal / $newQty
                ]);

                $desc = "Tebus Right Issue " . $request->asset_symbol . " @ " . number_format($request->exercise_price);
                $cashFlow = -$cost;
            }

            // Catat Transaksi
            Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet ? $wallet->id : null, // Bisa null jika stock split/dividen unit
                'type' => $type, // Pastikan kolom ENUM di database support ini, atau ubah jadi VARCHAR
                'status' => 'approved',
                'asset_symbol' => $request->asset_symbol,
                'amount' => $request->quantity ?? $request->quantity_received ?? 0, // Unit aset yg berubah
                'price_per_unit' => $request->exercise_price ?? 0,
                'amount_cash' => $cashFlow,
                'description' => $desc,
                'date' => $request->date ?? now(),
            ]);

            return redirect()->route('portfolio.index')->with('success', 'Corporate Action berhasil diproses!');
        });
    }
}