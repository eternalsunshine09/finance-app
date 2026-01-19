<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Asset;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalUser = User::where('role', 'user')->count();
        $totalAset = Asset::count();
        $pendingTopUp = Transaction::where('type', 'TOPUP')->where('status', 'pending')->count();
        $pendingWithdraw = Transaction::where('type', 'WITHDRAW')->where('status', 'pending')->count();

        return view('admin.dashboard', compact('totalUser', 'totalAset', 'pendingTopUp', 'pendingWithdraw'));
    }
}