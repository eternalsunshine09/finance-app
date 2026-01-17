<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Support\Facades\DB;

class AdminTransactionController extends Controller
{
    // 1. Tampilkan Daftar Top Up Pending
    public function index()
    {
        // Ambil transaksi TOPUP yang statusnya 'pending' beserta data usernya
        $transactions = Transaction::where('type', 'TOPUP')
                                   ->where('status', 'pending')
                                   ->with('user') // Eager load user biar efisien
                                   ->orderBy('created_at', 'desc')
                                   ->get();

        return view('admin.transactions.index', compact('transactions'));
    }

    // 2. Proses Approve
    public function approve($id)
    {
        DB::transaction(function() use ($id) {
            $trx = Transaction::findOrFail($id);

            // Cek biar gak double approve
            if ($trx->status == 'pending') {
                // A. Ubah Status
                $trx->update(['status' => 'approved']);

                // B. Masukkan Uang ke Dompet User
                $wallet = Wallet::findOrFail($trx->wallet_id);
                $wallet->increment('balance', $trx->amount_cash);
            }
        });

        return redirect()->back()->with('success', 'Top Up disetujui! Saldo user bertambah.');
    }

    // 3. Proses Reject (Tolak)
    public function reject($id)
    {
        $trx = Transaction::findOrFail($id);
        
        if ($trx->status == 'pending') {
            $trx->update(['status' => 'rejected']);
        }

        return redirect()->back()->with('success', 'Top Up ditolak.');
    }

    // ... method index, approve, reject TOP UP yang lama biarkan di atas ...

    // ===========================
    // FITUR APPROVAL WITHDRAW
    // ===========================

    // 1. Tampilkan Daftar Withdraw Pending
    public function indexWithdrawals()
    {
        $transactions = Transaction::where('type', 'WITHDRAW')
                                   ->where('status', 'pending')
                                   ->with('user')
                                   ->orderBy('created_at', 'desc')
                                   ->get();

        return view('admin.transactions.withdrawals', compact('transactions'));
    }

    // 2. Approve Withdraw (Tandai Selesai)
    public function approveWithdraw($id)
    {
        $trx = Transaction::findOrFail($id);
        
        if ($trx->status == 'pending') {
            // Cukup ubah status, karena saldo SUDAH dipotong di awal
            $trx->update(['status' => 'approved']);
        }

        return redirect()->back()->with('success', 'Penarikan disetujui! Transaksi selesai.');
    }

    // 3. Reject Withdraw (Tolak & REFUND Saldo)
    public function rejectWithdraw($id)
    {
        DB::transaction(function() use ($id) {
            $trx = Transaction::findOrFail($id);
            
            if ($trx->status == 'pending') {
                // A. Ubah Status Rejected
                $trx->update(['status' => 'rejected']);

                // B. KEMBALIKAN UANG KE DOMPET (Refund)
                // Karena amount_cash di withdraw itu negatif (misal -100.000),
                // Kita ambil nilai absolutnya (abs) untuk ditambahkan balik.
                $refundAmount = abs($trx->amount_cash);

                $wallet = Wallet::findOrFail($trx->wallet_id);
                $wallet->increment('balance', $refundAmount);
            }
        });

        return redirect()->back()->with('success', 'Penarikan ditolak. Dana dikembalikan ke user.');
    }
}