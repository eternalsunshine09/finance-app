<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Wallet;
use App\Models\Portfolio;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        // Ambil user biasa (bukan admin)
        $users = User::where('role', 'user')
            ->with('wallets') // Eager load wallet biar ringan
            ->latest()
            ->paginate(10);

        return view('admin.users.index', compact('users'));
    }

    public function show($id)
    {
        $user = User::with(['wallets', 'portfolios.asset'])->findOrFail($id);
        
        // Hitung total kekayaan user (Estimasi dalam IDR)
        $totalWealth = 0;
        
        // 1. Saldo Dompet
        foreach($user->wallets as $wallet) {
            $balance = $wallet->balance;
            if($wallet->currency == 'USD') {
                // Konversi kasar atau ambil rate asli (opsional)
                $balance *= 15500; 
            }
            $totalWealth += $balance;
        }

        // 2. Nilai Aset Portofolio
        foreach($user->portfolios as $porto) {
            $assetPrice = $porto->asset->current_price;
            $val = $porto->quantity * $assetPrice;
            
            // Konversi jika aset crypto (USD)
            if($porto->asset->type == 'Crypto') {
                $val *= 15500;
            }
            $totalWealth += $val;
        }

        return view('admin.users.show', compact('user', 'totalWealth'));
    }
}