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

        DB::transaction(function () use ($request) {
            $user = Auth::user();
            
            // 1. Ambil Data Wallet & Aset
            $wallet = Wallet::where('id', $request->wallet_id)
                            ->where('user_id', $user->id)
                            ->lockForUpdate()
                            ->firstOrFail();
                            
            $asset = Asset::where('symbol', $request->asset_symbol)->firstOrFail();

            // 🔥 VALIDASI TIPE ASET VS MATA UANG DOMPET 🔥
            if ($asset->type == 'Crypto' && $wallet->currency != 'USD') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'wallet_id' => "Aset Crypto ({$asset->symbol}) wajib dibeli pakai Saldo USD. Silakan pilih dompet USD atau Tukar Valas dulu."
                ]);
            }

            if ($asset->type == 'Stock' && $wallet->currency != 'IDR') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'wallet_id' => "Saham Indonesia ({$asset->symbol}) wajib dibeli pakai Saldo IDR."
                ]);
            }

            // Hitung Total
            $totalCost = $request->amount * $request->buy_price;

            // Cek Saldo
            if ($wallet->balance < $totalCost) {
                $currencySymbol = ($wallet->currency == 'USD') ? '$' : 'Rp';
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'amount' => "Saldo tidak cukup! Butuh {$currencySymbol} " . number_format($totalCost, 2)
                ]);
            }

            // Potong Saldo
            $wallet->decrement('balance', $totalCost);

            // Update Portofolio
            $portfolio = Portfolio::firstOrCreate(
                ['user_id' => $user->id, 'asset_symbol' => $request->asset_symbol],
                ['amount' => 0, 'average_price' => 0]
            );

            // Hitung Average Price (Average Down)
            $oldTotalVal = $portfolio->amount * $portfolio->average_price;
            $newTotalVal = $oldTotalVal + $totalCost;
            $newAmount   = $portfolio->amount + $request->amount;
            $newAvgPrice = $newAmount > 0 ? $newTotalVal / $newAmount : 0;

            $portfolio->update([
                'amount' => $newAmount,
                'average_price' => $newAvgPrice
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
        
        // Ambil semua dompet user (walaupun saldo 0, tetap bisa di-topup)
        $wallets = Wallet::where('user_id', $user->id)->get();

        return view('transactions.topup', compact('wallets'));
    }

    public function topUp(Request $request)
    {
        $request->validate([
            'wallet_id'     => 'required|exists:wallets,id', // Wajib pilih dompet tujuan
            'amount'        => 'required|numeric|min:10000',
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg|max:2048', 
        ]);

        DB::transaction(function () use ($request) {
            $user = Auth::user();

            // 1. Simpan Foto
            $proofPath = $request->file('payment_proof')->store('receipts', 'public');

            // 2. Ambil Dompet Tujuan
            $wallet = Wallet::where('id', $request->wallet_id)
                            ->where('user_id', $user->id)
                            ->firstOrFail();

            // 3. Catat Transaksi Pending
            Transaction::create([
                'user_id'       => $user->id,
                'wallet_id'     => $wallet->id, // Link ke dompet spesifik
                'type'          => 'TOPUP',
                'amount_cash'   => $request->amount,
                'date'          => now(),
                'status'        => 'pending',
                'description'   => 'Top Up Saldo ' . $wallet->bank_name,
                'payment_proof' => $proofPath 
            ]);

            // Saldo TIDAK bertambah di sini. Nunggu Admin Approve.
        });

        return redirect()->route('wallet.index')->with('success', 'Permintaan Top Up dikirim! Mohon tunggu verifikasi Admin.');
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