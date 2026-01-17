<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Asset;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung Statistik untuk Admin
        $totalUser = User::where('role', 'user')->count();
        $totalAset = Asset::count();
        
        // Hitung transaksi yang statusnya 'pending'
        $pendingTopUp = Transaction::where('type', 'TOPUP')
                                   ->where('status', 'pending')
                                   ->count();

        // 2. Kirim ke View Khusus Admin
        return view('admin.dashboard', compact('totalUser', 'totalAset', 'pendingTopUp'));
    }
}