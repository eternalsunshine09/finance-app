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
    // 1. FITUR TOP UP
    // ===========================
    public function showTopUpForm()
    {
        return view('transactions.topup');
    }

    public function topUp(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:10000',
            'currency' => 'required|in:IDR,USD',
            // 👇 Validasi Foto (Wajib ada, harus gambar, max 2MB)
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        DB::transaction(function () use ($request) {
            
            // 1. Simpan Foto ke Folder 'public/receipts'
            // Nanti tersimpan sebagai: storage/app/public/receipts/acak.jpg
            $proofPath = $request->file('payment_proof')->store('receipts', 'public');

            $wallet = Wallet::firstOrCreate(
                ['user_id' => $request->user_id, 'currency' => $request->currency],
                ['balance' => 0]
            );

            Transaction::create([
                'user_id' => $request->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'TOPUP',
                'amount_cash' => $request->amount,
                'date' => now(),
                'status' => 'pending',
                // 👇 Simpan Path Foto ke Database
                'payment_proof' => $proofPath 
            ]);

            // Ingat: Jangan increment saldo di sini! (Masih dikomentari)
            // $wallet->increment('balance', $request->amount); 
        });

        return redirect()->route('dashboard')->with('success', 'Bukti transfer berhasil diupload! Menunggu verifikasi Admin.');
    }

    // ===========================
    // 2. FITUR BELI ASET
    // ===========================
    public function showBuyForm()
    {
        $assets = Asset::all();
        // PERBAIKAN: Mengembalikan View, bukan Redirect
        return view('transactions.buy', compact('assets')); 
    }

    public function buyAsset(Request $request)
    {
        // 1. Tambahkan validasi untuk harga manual
        $request->validate([
            'buy_price' => 'required|numeric|min:1',
            'user_id' => 'required|exists:users,id',
            'asset_symbol' => 'required|exists:assets,symbol',
            'quantity' => 'required|numeric|min:0.00000001',
            'price_per_unit' => 'required|numeric',
        ]);

        DB::transaction(function () use ($request) {
            $transactionDate = $request->custom_date ? Carbon::parse($request->custom_date) : now();

            // ❌ JANGAN PAKAI INI LAGI:
            // $asset = Asset::where('symbol', $request->asset_symbol)->first();
            // $currentPrice = $asset->current_price;

            // ✅ PAKAI INI (Harga dari Input User):
            $currentPrice = $request->buy_price; 

            // Hitung Total Bayar
            // Rumus: Jumlah Unit * Harga Per Unit
            // (Tadi kan rumusnya kebalik karena inputnya rupiah, sekarang inputnya unit & harga)
            // Jadi Total Uang Keluar = Unit * Harga
            $totalCost = $request->amount * $currentPrice;

            // Update Wallet
            $wallet = Wallet::where('user_id', $request->user_id)->first();
            
            // Cek saldo cukup gak?
            if ($wallet->balance < $totalCost) {
                throw \Illuminate\Validation\ValidationException::withMessages(['amount' => 'Saldo tidak cukup!']);
            }
            
            $wallet->decrement('balance', $totalCost);

            // Update Portofolio (Average Price Logic)
            $portfolio = Portfolio::firstOrCreate(
                ['user_id' => $request->user_id, 'asset_symbol' => $request->asset_symbol],
                ['quantity' => 0, 'average_buy_price' => 0]
            );

            // Hitung AVG Price Baru
            $oldTotalValue = $portfolio->quantity * $portfolio->average_buy_price;
            $newTotalValue = $oldTotalValue + $totalCost;
            $newQuantity = $portfolio->quantity + $request->amount;
            
            $newAvg = $newTotalValue / $newQuantity;

            $portfolio->update([
                'quantity' => $newQuantity,
                'average_buy_price' => $newAvg
            ]);

            // Simpan Transaksi
            Transaction::create([
                'user_id' => $request->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'BUY',
                'amount_cash' => -$totalCost, // Uang keluar
                'amount_asset' => $request->amount, // Unit aset masuk
                'asset_symbol' => $request->asset_symbol,
                'date' => $transactionDate,
                'status' => 'approved'
            ]);
        });

        return redirect()->route('dashboard')->with('success', 'Pembelian berhasil dicatat!');
    }

    // ===========================
    // 3. FITUR JUAL ASET
    // ===========================
    public function showSellForm($symbol)
    {
        $userId = Auth::id();
        $portfolio = Portfolio::where('user_id', $userId)
                              ->where('asset_symbol', $symbol)
                              ->first();

        if (!$portfolio || $portfolio->quantity <= 0) {
            return redirect()->route('dashboard')->with('error', 'Kamu tidak memiliki aset '.$symbol);
        }

        return view('transactions.sell', compact('portfolio'));
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
                'amount_cash' => $totalRevenue,
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
            // A. Cek Saldo & Dompet
            $wallet = Wallet::where('user_id', $request->user_id)
                            ->where('currency', $request->currency)
                            ->first();

            // Validasi: Apakah saldo cukup?
            if (!$wallet || $wallet->balance < $request->amount) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => 'Saldo tidak cukup untuk penarikan ini!',
                ]);
            }

            // B. Catat Transaksi (Status: PENDING)
            Transaction::create([
                'user_id' => $request->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'WITHDRAW',
                'amount_cash' => -$request->amount, // Negatif karena uang keluar
                'date' => now(),
                'status' => 'pending' 
            ]);

            // C. POTONG SALDO LANGSUNG (Agar tidak bisa dipake beli saham)
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
        
        // Ambil data transaksi milik user, urutkan dari yang terbaru
        // paginate(10) artinya cuma ambil 10 data per halaman
        $transactions = Transaction::where('user_id', $userId)
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(10);

        return view('transactions.history', compact('transactions'));
    }
}