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
}