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
            'wallet_id'    => 'required|exists:wallets,id',
            'asset_symbol' => 'required|exists:assets,symbol',
            'buy_price'    => 'required|numeric|min:0',
            'amount'       => 'required|numeric|min:0.00000001', 
        ]);

        $user = Auth::user();
        $totalCost = $request->amount * $request->buy_price;

        DB::transaction(function () use ($request, $user, $totalCost) {
            
            // 1. Ambil Data Wallet & Aset
            $wallet = Wallet::where('id', $request->wallet_id)
                            ->where('user_id', $user->id)
                            ->lockForUpdate()
                            ->firstOrFail();
                            
            $asset = Asset::where('symbol', $request->asset_symbol)->firstOrFail();

            // Validasi Mata Uang
            if ($asset->type == 'Crypto' && $wallet->currency != 'USD') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'wallet_id' => "Aset Crypto ({$asset->symbol}) wajib dibeli pakai Saldo USD."
                ]);
            }

            if ($asset->type == 'Stock' && $wallet->currency != 'IDR') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'wallet_id' => "Saham Indonesia ({$asset->symbol}) wajib dibeli pakai Saldo IDR."
                ]);
            }

            // Cek Saldo
            if ($wallet->balance < $totalCost) {
                $currencySymbol = ($wallet->currency == 'USD') ? '$' : 'Rp';
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => "Saldo tidak cukup! Butuh {$currencySymbol} " . number_format($totalCost, 2)
                ]);
            }

            // Potong Saldo
            $wallet->decrement('balance', $totalCost);

            // ---------------------------------------------------------
            // 🔥 PERBAIKAN DI SINI (Ganti 'amount' jadi 'quantity') 🔥
            // ---------------------------------------------------------
            
            // Cari atau Buat Portfolio Baru
            $portfolio = Portfolio::firstOrCreate(
                ['user_id' => $user->id, 'asset_symbol' => $request->asset_symbol],
                ['quantity' => 0, 'average_buy_price' => 0] // ✅ GANTI nama kolom
            );

            // Hitung Average Price (Average Down Logic)
            // Gunakan $portfolio->average_buy_price (sesuai DB)
            $oldTotalVal = $portfolio->quantity * $portfolio->average_buy_price; 
            $newTotalVal = $oldTotalVal + $totalCost;
            $newQuantity = $portfolio->quantity + $request->amount; 
            
            $newAvgPrice = $newQuantity > 0 ? $newTotalVal / $newQuantity : 0;

            // Update Database Portfolio
            $portfolio->update([
                'quantity' => $newQuantity, 
                'average_buy_price' => $newAvgPrice // ✅ GANTI nama kolom
            ]);

            // Catat Transaksi
            Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'type' => 'BUY',
                'status' => 'approved',
                'asset_symbol' => $request->asset_symbol,
                'amount' => $request->amount, 
                'price_per_unit' => $request->buy_price,
                'amount_cash' => -$totalCost,
                'description' => "Beli " . $request->asset_symbol,
                
                // 🔥 ADD THIS LINE HERE:
                'date' => $request->custom_date ?? now(), 
                
                // You had 'created_at' mapped to custom_date, which is fine, 
                // but the database strictly requires a 'date' column too.
                'created_at' => $request->custom_date ?? now(),
            ]);
        });

        return redirect()->route('wallet.index')->with('success', 'Pembelian Aset Berhasil!');
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
}