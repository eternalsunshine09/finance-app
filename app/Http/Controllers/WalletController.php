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
        
        // 1. Ambil Data Dompet
        $wallet = Wallet::firstOrCreate(
            ['user_id' => $user->id, 'currency' => 'IDR'],
            ['balance' => 0]
        );

        // 2. Ambil History KHUSUS Uang Tunai (Top Up & Withdraw)
        // Kita tidak menampilkan history Beli/Jual saham di sini biar gak pusing
        $cashHistory = Transaction::where('user_id', $user->id)
                                  ->whereIn('type', ['TOPUP', 'WITHDRAW']) // Filter Tipe
                                  ->orderBy('created_at', 'desc')
                                  ->get();

        return view('wallet.index', compact('wallet', 'cashHistory'));
    }
}