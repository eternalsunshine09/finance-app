<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\Asset;
use App\Models\Portfolio;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB; // <--- Ini penting buat Database Transaction

class TransactionController extends Controller
{
    // FITUR 1: TOP UP SALDO (Uang Masuk)
    public function topUp(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'amount' => 'required|numeric|min:10000', // Minimal topup 10rb
            'currency' => 'required|in:IDR,USD',
        ]);

        // Gunakan DB::transaction agar aman (All or Nothing)
        return DB::transaction(function () use ($request) {
            
            // A. Cari Dompet User
            $wallet = Wallet::firstOrCreate(
                ['user_id' => $request->user_id, 'currency' => $request->currency],
                ['balance' => 0]
            );

            // B. Catat Riwayat Transaksi
            Transaction::create([
                'user_id' => $request->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'TOPUP',
                'amount_cash' => $request->amount,
                'date' => now(),
            ]);

            // C. Update Saldo Dompet
            $wallet->increment('balance', $request->amount);

            return response()->json(['message' => 'Top Up Berhasil!', 'saldo_baru' => $wallet->balance]);
        });
    }

    // FITUR 2: BELI ASET (Uang Keluar, Aset Masuk)
    public function buyAsset(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'asset_symbol' => 'required|exists:assets,symbol',
            'quantity' => 'required|numeric|min:0.00000001', // Bisa beli pecahan kecil (crypto)
            'price_per_unit' => 'required|numeric', // Harga saat ini
        ]);

        return DB::transaction(function () use ($request) {
            // Hitung total uang yang dibutuhkan
            $totalCost = $request->quantity * $request->price_per_unit;

            // A. Cek Apakah User Punya Saldo IDR Cukup?
            // (Asumsi beli pakai IDR dulu biar simpel)
            $wallet = Wallet::where('user_id', $request->user_id)
                            ->where('currency', 'IDR')
                            ->first();

            if (!$wallet || $wallet->balance < $totalCost) {
                return response()->json(['message' => 'Saldo tidak cukup!'], 400);
            }

            // B. Catat Transaksi
            Transaction::create([
                'user_id' => $request->user_id,
                'wallet_id' => $wallet->id,
                'type' => 'BUY',
                'asset_symbol' => $request->asset_symbol,
                'amount_cash' => -$totalCost, // Uang berkurang (negatif)
                'amount_asset' => $request->quantity, // Aset bertambah (positif)
                'price_per_unit' => $request->price_per_unit,
                'date' => now(),
            ]);

            // C. Potong Uang di Dompet
            $wallet->decrement('balance', $totalCost);

            // D. Update Portofolio (Logika Average Price)
            $portfolio = Portfolio::firstOrNew([
                'user_id' => $request->user_id,
                'asset_symbol' => $request->asset_symbol
            ]);

            // Hitung harga rata-rata baru (Average Down/Up)
            $oldQty = $portfolio->quantity ?? 0;
            $oldAvg = $portfolio->average_buy_price ?? 0;
            
            // Rumus Average: ((QtyLama * HargaLama) + (QtyBaru * HargaBaru)) / TotalQty
            $newAvg = (($oldQty * $oldAvg) + $totalCost) / ($oldQty + $request->quantity);

            $portfolio->quantity = $oldQty + $request->quantity;
            $portfolio->average_buy_price = $newAvg;
            $portfolio->save();

            return response()->json(['message' => 'Pembelian Berhasil!', 'portfolio' => $portfolio]);
        });
    }
}