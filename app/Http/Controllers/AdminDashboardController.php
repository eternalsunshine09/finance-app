<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Asset;
use App\Models\Transaction;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // 1. Hitung Total User (Hanya member, admin tidak dihitung)
        $totalUser = User::where('role', 'member')->count();

        // 2. Hitung Total Jenis Aset yang terdaftar
        $totalAset = Asset::count();

        // 3. Hitung Topup yang statusnya masih 'pending' (Butuh persetujuan)
        $pendingTopUp = Transaction::where('type', 'TOPUP')
            ->where('status', 'pending')
            ->count();

        // 4. Hitung Total semua transaksi yang pernah terjadi
        $totalTransactions = Transaction::count();
        
        // 5. Ambil 5 Transaksi Topup Terakhir untuk list "Aktivitas Terbaru"
        // Menggunakan 'with' agar query ke user efisien (Eager Loading)
        $recentTopUps = Transaction::with('user')
            ->where('type', 'TOPUP')
            ->latest() // Urutkan dari yang terbaru
            ->take(5)  // Ambil 5 saja
            ->get();

        // Kirim semua data ke View
        return view('admin.dashboard', compact(
            'totalUser', 
            'totalAset', 
            'pendingTopUp', 
            'totalTransactions', 
            'recentTopUps'
        ));
    }
}