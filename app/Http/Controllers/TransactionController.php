<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\Asset;
use App\Models\Portfolio;
use App\Models\Transaction;
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
        // ... validasi (biarkan sama) ...

        DB::transaction(function () use ($request) {
            
            // 1. Pastikan Dompet Ada (Biarkan)
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $request->user_id, 'currency' => $request->currency],
                ['balance' => 0]
            );

            // 2. Catat Transaksi dengan status PENDING (Biarkan)
            Transaction::create([
                'user_id' => $request->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'TOPUP',
                'amount_cash' => $request->amount,
                'date' => now(),
                'status' => 'pending' 
            ]);

            // 👇👇👇 BAGIAN INI YANG HARUS KAMU MATIKAN 👇👇👇
            // Beri tanda // di depannya supaya jadi komentar (tidak dijalankan)
            
            // $wallet->increment('balance', $request->amount); 
            
            // 👆👆👆 PASTIKAN ADA GARIS MIRINGNYA (//) 👆👆👆
        });

        return redirect()->route('dashboard')->with('success', 'Permintaan Top Up dikirim! Menunggu Admin.');
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
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'asset_symbol' => 'required|exists:assets,symbol',
            'quantity' => 'required|numeric|min:0.00000001',
            'price_per_unit' => 'required|numeric',
        ]);

        DB::transaction(function () use ($request) {
            $totalCost = $request->quantity * $request->price_per_unit;

            // A. Cek Saldo
            $wallet = Wallet::where('user_id', $request->user_id)
                            ->where('currency', 'IDR')
                            ->first();

            if (!$wallet || $wallet->balance < $totalCost) {
                // Lempar error validasi agar user tahu saldonya kurang
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'quantity' => 'Saldo tidak cukup untuk membeli aset ini!',
                ]);
            }

            // B. Catat Transaksi
            Transaction::create([
                'user_id' => $request->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'BUY',
                'asset_symbol' => $request->asset_symbol,
                'amount_cash' => -$totalCost,
                'amount_asset' => $request->quantity,
                'price_per_unit' => $request->price_per_unit,
                'date' => now(),
            ]);

            // C. Potong Uang
            $wallet->decrement('balance', $totalCost);

            // D. Update Portofolio (Average Price)
            $portfolio = Portfolio::firstOrNew([
                'user_id' => $request->user_id,
                'asset_symbol' => $request->asset_symbol
            ]);

            $oldQty = $portfolio->quantity ?? 0;
            $oldAvg = $portfolio->average_buy_price ?? 0;
            $newAvg = (($oldQty * $oldAvg) + $totalCost) / ($oldQty + $request->quantity);

            $portfolio->quantity = $oldQty + $request->quantity;
            $portfolio->average_buy_price = $newAvg;
            $portfolio->save();
        });

        return redirect()->route('dashboard')->with('success', 'Pembelian Aset Berhasil!');
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
    // 4. FITUR WITHDRAW
    // ===========================
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
                return response()->json(['message' => 'Saldo tidak cukup!'], 400);
            }

            Transaction::create([
                'user_id' => $request->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'WITHDRAW',
                'amount_cash' => -$request->amount,
                'date' => now(),
            ]);

            $wallet->decrement('balance', $request->amount);
        });

        return redirect()->route('dashboard')->with('success', 'Penarikan Berhasil!');
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