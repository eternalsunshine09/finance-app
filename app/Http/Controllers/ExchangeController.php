<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\Transaction; // Pastikan model Transaction ada
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExchangeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // AMBIL SEMUA DOMPET MILIK USER
        // Karena strukturnya satu tabel, kita get() semua.
        $wallets = Wallet::where('user_id', $user->id)->get();

        // Rate default (bisa diganti logic API nanti)
        $currentRate = 15500; 

        return view('exchange.index', compact('wallets', 'currentRate'));
    }

    public function process(Request $request)
{
    $request->validate([
        'source_wallet_id' => 'required|exists:wallets,id',
        'target_wallet_id' => 'required|exists:wallets,id|different:source_wallet_id',
        'amount_source'    => 'required|numeric|min:1',
        'rate'             => 'required|numeric|min:0.0001',
    ]);

    $user = Auth::user();

    $sourceWallet = Wallet::where('id', $request->source_wallet_id)->where('user_id', $user->id)->firstOrFail();
    $targetWallet = Wallet::where('id', $request->target_wallet_id)->where('user_id', $user->id)->firstOrFail();

    if ($sourceWallet->balance < $request->amount_source) {
        return back()->with('error', 'Saldo dompet asal tidak mencukupi!');
    }

    $amountReceived = $request->amount_source / $request->rate;

    DB::transaction(function () use ($user, $sourceWallet, $targetWallet, $request, $amountReceived) {
        $sourceWallet->lockForUpdate();
        $targetWallet->lockForUpdate();

        // 1. Update Saldo
        $sourceWallet->decrement('balance', $request->amount_source);
        $targetWallet->increment('balance', $amountReceived);

        // 2. Catat Transaksi PENGIRIM (Type: SELL = Uang Keluar)
        Transaction::create([
            'user_id'         => $user->id,
            'wallet_id'       => $sourceWallet->id,
            'type'            => 'SELL', // Gunakan SELL agar terdeteksi sebagai pengeluaran
            'status'          => 'approved',
            'amount_cash'     => -($request->amount_source), // Negatif
            'amount'          => 0, // Wajib diisi 0
            'price_per_unit'  => 1, // Wajib diisi 1
            'asset_symbol'    => null,
            'date'            => now(),
            'description'     => "Kirim Valas ke " . $targetWallet->bank_name,
            'exchange_rate'   => $request->rate,
            'target_currency' => $targetWallet->currency
        ]);

        // 3. Catat Transaksi PENERIMA (Type: BUY = Uang Masuk)
        Transaction::create([
            'user_id'         => $user->id,
            'wallet_id'       => $targetWallet->id,
            'type'            => 'BUY', // Gunakan BUY agar terdeteksi sebagai pemasukan
            'status'          => 'approved',
            'amount_cash'     => $amountReceived, // Positif
            'amount'          => 0,
            'price_per_unit'  => 1,
            'asset_symbol'    => null,
            'date'            => now(),
            'description'     => "Terima Valas dari " . $sourceWallet->bank_name,
            'exchange_rate'   => $request->rate,
            'target_currency' => $sourceWallet->currency
        ]);
    });

    return redirect()->route('wallet.index')->with('success', 'Konversi berhasil!');
}
}