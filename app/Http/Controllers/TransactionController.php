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
                ['quantity' => 0, 'average_price' => 0] // ✅ Ganti 'amount' jadi 'quantity'
            );

            // Hitung Average Price (Average Down Logic)
            // Gunakan $portfolio->quantity (bukan amount)
            $oldTotalVal = $portfolio->quantity * $portfolio->average_price; 
            $newTotalVal = $oldTotalVal + $totalCost;
            $newQuantity = $portfolio->quantity + $request->amount; // Ditambah jumlah beli baru
            
            $newAvgPrice = $newQuantity > 0 ? $newTotalVal / $newQuantity : 0;

            // Update Database Portfolio
            $portfolio->update([
                'quantity' => $newQuantity, // ✅ Ganti 'amount' jadi 'quantity'
                'average_price' => $newAvgPrice
            ]);

            // Catat Transaksi
            Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $wallet->id,
                'type' => 'BUY',
                'status' => 'approved',
                'asset_symbol' => $request->asset_symbol,
                'amount' => $request->amount, // Di tabel Transaction tetap 'amount' (sesuai migrasi awal)
                'price_per_unit' => $request->buy_price,
                'amount_cash' => -$totalCost,
                'description' => "Beli " . $request->asset_symbol,
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
            // 🔥 UPDATE: min:0.01 agar bisa input desimal kecil (misal $0.7)
            'amount'        => 'required|numeric|min:0.01', 
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        DB::transaction(function () use ($request) {
            $user = Auth::user();
            
            // Simpan Foto
            $proofPath = $request->file('payment_proof')->store('receipts', 'public');

            // Ambil Dompet
            $wallet = Wallet::findOrFail($request->wallet_id);

            // Buat Transaksi (Status Pending)
            Transaction::create([
                'user_id'       => $user->id,
                'wallet_id'     => $wallet->id,
                'type'          => 'TOPUP',
                'amount_cash'   => $request->amount,
                'date'          => now(),
                'status'        => 'pending', // Menunggu Admin
                'description'   => 'Top Up ' . $wallet->currency . ' via Transfer',
                'payment_proof' => $proofPath 
            ]);
        });

        return redirect()->route('wallet.index')->with('success', 'Top Up berhasil diajukan! Admin akan memverifikasi bukti transfer Anda.');
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
                'user_id' => $request->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'WITHDRAW',
                'amount_cash' => -$request->amount,
                'date' => now(),
                'status' => 'pending' 
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
}