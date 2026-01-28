<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class AdminTransactionController extends Controller
{
    // Halaman List Transaksi (Topup / Buy / Sell)
    public function index()
    {
        $transactions = Transaction::with('user')
            ->whereIn('type', ['TOPUP', 'BUY', 'SELL']) // Sesuaikan filter
            ->latest()
            ->paginate(10);

        return view('admin.transactions.index', compact('transactions'));
    }

    // Halaman List Withdrawal (Penarikan)
    public function indexWithdrawals()
    {
        $withdrawals = Transaction::with('user')
            ->where('type', 'WITHDRAW')
            ->latest()
            ->paginate(10);

        return view('admin.withdrawals.index', compact('withdrawals'));
    }

    // Approve Transaksi
    public function approve($id)
    {
        $trx = Transaction::findOrFail($id);
        $trx->status = 'approved';
        // Tambahkan logika saldo masuk user di sini jika perlu
        $trx->save();

        return back()->with('success', 'Transaksi disetujui.');
    }

    // Reject Transaksi
    public function reject($id)
    {
        $trx = Transaction::findOrFail($id);
        $trx->status = 'rejected';
        $trx->save();

        return back()->with('success', 'Transaksi ditolak.');
    }
}