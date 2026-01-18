<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\Transaction;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExchangeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $walletIDR = Wallet::where('user_id', $user->id)->where('currency', 'IDR')->first();
        $walletUSD = Wallet::where('user_id', $user->id)->where('currency', 'USD')->first();

        // Ambil Kurs Referensi (Default dari Admin/API)
        $rateData = ExchangeRate::where('from_currency', 'USD')->latest()->first();
        $currentRate = $rateData ? $rateData->rate : 15500; 

        return view('exchange.index', compact('walletIDR', 'walletUSD', 'currentRate'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'amount_idr' => 'required|numeric|min:1',
            'custom_rate' => 'required|numeric|min:1', // Validasi input kurs user
        ]);

        $user = Auth::user();
        $amountIDR = $request->amount_idr;
        
        // 🔥 UPDATE: Gunakan kurs yang diinput User, bukan dari DB Admin
        $rate = $request->custom_rate; 

        // Hitung Dapat Berapa USD (IDR dibagi Kurs User)
        $amountUSD = $amountIDR / $rate;

        DB::transaction(function () use ($user, $amountIDR, $amountUSD, $rate) {
            
            $walletIDR = Wallet::where('user_id', $user->id)->where('currency', 'IDR')->lockForUpdate()->firstOrFail();
            $walletUSD = Wallet::where('user_id', $user->id)->where('currency', 'USD')->lockForUpdate()->first();

            if (!$walletUSD) {
                $walletUSD = Wallet::create([
                    'user_id' => $user->id,
                    'currency' => 'USD',
                    'balance' => 0,
                    'bank_name' => 'USD Cash',
                    'account_name' => 'Assets USD'
                ]);
            }

            if ($walletIDR->balance < $amountIDR) {
                throw new \Exception('Saldo Rupiah tidak cukup!');
            }

            $walletIDR->decrement('balance', $amountIDR);
            $walletUSD->increment('balance', $amountUSD);

            // Simpan Transaksi Keluar
            Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $walletIDR->id,
                'type' => 'EXCHANGE_OUT',
                'status' => 'approved',
                'amount_cash' => -$amountIDR,
                'description' => "Tukar Valas (Rate: Rp " . number_format($rate) . ")",
                'exchange_rate' => $rate, // Simpan rate user
                'target_currency' => 'USD'
            ]);

            // Simpan Transaksi Masuk
            Transaction::create([
                'user_id' => $user->id,
                'wallet_id' => $walletUSD->id,
                'type' => 'EXCHANGE_IN',
                'status' => 'approved',
                'amount_cash' => $amountUSD,
                'description' => "Hasil Tukar Valas",
                'exchange_rate' => $rate, // Simpan rate user
                'target_currency' => 'IDR'
            ]);
        });

        return redirect()->route('wallet.index')->with('success', 'Konversi berhasil dengan kurs Rp ' . number_format($rate));
    }
}