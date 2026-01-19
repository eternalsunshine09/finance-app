<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class AdminTransactionController extends Controller
{
    // --- TOP UP ---
    public function index()
    {
        $transactions = Transaction::where('type', 'TOPUP')
                                   ->where('status', 'pending')
                                   ->with('user')
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        return view('admin.transactions.index', compact('transactions'));
    }

    public function approve($id)
    {
        DB::transaction(function() use ($id) {
            $trx = Transaction::findOrFail($id);
            if ($trx->status == 'pending') {
                $trx->update(['status' => 'approved']);
                $wallet = Wallet::findOrFail($trx->wallet_id);
                $wallet->increment('balance', $trx->amount_cash);
            }
        });
        return back()->with('success', 'Top Up disetujui.');
    }

    public function reject($id)
    {
        $trx = Transaction::findOrFail($id);
        if ($trx->status == 'pending') $trx->update(['status' => 'rejected']);
        return back()->with('success', 'Top Up ditolak.');
    }

    // --- WITHDRAW ---
    public function indexWithdrawals()
    {
        $transactions = Transaction::where('type', 'WITHDRAW')
                                   ->where('status', 'pending')
                                   ->with('user')
                                   ->orderBy('created_at', 'desc')
                                   ->get();
        return view('admin.transactions.withdrawals', compact('transactions'));
    }

    public function approveWithdraw($id)
    {
        $trx = Transaction::findOrFail($id);
        if ($trx->status == 'pending') $trx->update(['status' => 'approved']);
        return back()->with('success', 'Withdraw disetujui.');
    }

    public function rejectWithdraw($id)
    {
        DB::transaction(function() use ($id) {
            $trx = Transaction::findOrFail($id);
            if ($trx->status == 'pending') {
                $trx->update(['status' => 'rejected']);
                // Refund saldo ke user
                $wallet = Wallet::findOrFail($trx->wallet_id);
                $wallet->increment('balance', abs($trx->amount_cash));
            }
        });
        return back()->with('success', 'Withdraw ditolak, saldo dikembalikan.');
    }
}