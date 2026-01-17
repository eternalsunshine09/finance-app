<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Transaction;

class WalletController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // 1. Ambil SEMUA dompet milik user
        $wallets = Wallet::where('user_id', $user->id)->get();

        // Kalau user belum punya dompet sama sekali (User Baru), buatkan 1 default
        if($wallets->isEmpty()) {
            $default = Wallet::create([
                'user_id' => $user->id,
                'balance' => 0,
                'currency' => 'IDR',
                'bank_name' => 'System',
                'account_name' => 'Tunai Utama',
                'account_number' => rand(10000000, 99999999)
            ]);
            // Refresh biar variabel $wallets ada isinya
            $wallets = Wallet::where('user_id', $user->id)->get();
        }

        // 2. Ambil History (Gabungan semua dompet)
        $cashHistory = Transaction::where('user_id', $user->id)
                                  ->whereIn('type', ['TOPUP', 'WITHDRAW'])
                                  ->with('wallet') // Load data wallet biar tau ini transaksi dompet mana
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        return view('wallet.index', compact('wallets', 'cashHistory'));
    }

    // FITUR BARU: TAMBAH DOMPET
    public function store(Request $request)
    {
        $request->validate([
            'account_name' => 'required|string|max:50', // Misal: "RDN Ajaib"
            'bank_name' => 'required|string|max:50',    // Misal: "Bank Permata"
            'account_number' => 'nullable|numeric',
        ]);

        Wallet::create([
            'user_id' => Auth::id(),
            'balance' => 0, // Dompet baru saldonya 0
            'currency' => 'IDR',
            'account_name' => $request->account_name,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number ?? rand(10000000, 99999999)
        ]);

        return back()->with('success', 'Dompet baru berhasil ditambahkan!');
    }
}