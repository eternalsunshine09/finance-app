<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Transaction;

class WalletController extends Controller
{
    // 1. HALAMAN UTAMA (Hanya menampilkan kartu dompet)
    public function index()
    {
        $wallets = Wallet::where('user_id', Auth::id())->get();
        // Hitung total saldo (opsional jika dipakai di view)
        $totalBalance = $wallets->sum(function($w) {
            return $w->currency == 'USD' ? $w->balance * 16000 : $w->balance;
        });

        return view('wallet.index', compact('wallets', 'totalBalance'));
    }

    // --- 1. FITUR SHOW (LIHAT RIWAYAT SPESIFIK) ---
    public function show($id)
    {
        $wallet = Wallet::where('user_id', Auth::id())->findOrFail($id);
        
        // Ambil transaksi KHUSUS dompet ini
        $transactions = Transaction::where('wallet_id', $id)
                                   ->orderBy('created_at', 'desc')
                                   ->paginate(10);

        return view('wallet.show', compact('wallet', 'transactions'));
    }

    // 2. HALAMAN KHUSUS RIWAYAT (Bisa difilter per wallet)
    public function history(Request $request)
    {
        $user = Auth::user();
        
        // Mulai Query Transaksi
        $query = Transaction::where('user_id', $user->id)
                            ->whereIn('type', ['TOPUP', 'WITHDRAW'])
                            ->with('wallet') // Eager load relasi wallet
                            ->orderBy('created_at', 'desc');

        // Jika ada filter dompet tertentu dari URL (?wallet_id=1)
        if ($request->has('wallet_id') && $request->wallet_id != 'all') {
            $query->where('wallet_id', $request->wallet_id);
        }

        // Gunakan Pagination agar ringan (10 data per halaman)
        $transactions = $query->paginate(10)->withQueryString();
        
        // Kita butuh daftar wallet juga untuk dropdown filter di halaman history
        $wallets = Wallet::where('user_id', $user->id)->get();

        return view('wallet.history', compact('transactions', 'wallets'));
    }

    // 3. HALAMAN EDIT (Form Terpisah)
    public function edit($id)
    {
        $wallet = Wallet::where('user_id', Auth::id())->findOrFail($id);
        return view('wallet.edit', compact('wallet'));
    }

    // 4. PROSES UPDATE
    public function update(Request $request, $id)
    {
        $wallet = Wallet::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'account_name' => 'required|string|max:50',
            'bank_name' => 'required|string|max:50',
            'account_number' => 'nullable|numeric',
        ]);

        $wallet->update([
            'account_name' => $request->account_name,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number,
        ]);

        return redirect()->route('wallet.index')->with('success', 'Dompet berhasil diperbarui!');
    }

    // 5. PROSES SIMPAN BARU
    public function store(Request $request)
    {
        // ... (Validasi & Create logic sama seperti sebelumnya) ...
        $request->validate([
            'account_name' => 'required|string|max:50',
            'bank_name' => 'required|string|max:50',
            'currency' => 'required|in:IDR,USD',
        ]);

        Wallet::create([
            'user_id' => Auth::id(),
            'balance' => 0,
            'currency' => $request->currency,
            'account_name' => $request->account_name,
            'bank_name' => $request->bank_name,
            'account_number' => $request->account_number ?? rand(10000000, 99999999)
        ]);

        return back()->with('success', 'Dompet baru berhasil ditambahkan!');
    }

    // 6. PROSES HAPUS
    public function destroy($id)
    {
        $wallet = Wallet::where('user_id', Auth::id())->findOrFail($id);
        $wallet->delete();
        return redirect()->route('wallet.index')->with('success', 'Dompet dihapus!');
    }
}