<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Portfolio;
use App\Models\Asset; // <--- WAJIB TAMBAH INI (Supaya bisa baca harga pasar)

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Data User
        $user = Auth::user();

        // [LOGIKA BARU] Cek Role: Kalau Admin, tendang ke Dashboard Admin
        if ($user->role == 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $userId = $user->id;

        // 2. Ambil Total Uang Tunai (Cash)
        $wallets = Wallet::where('user_id', $userId)->get();
        $totalCash = $wallets->sum('balance');

        // 3. Ambil Aset & Hitung Valuasinya
        $portfolios = Portfolio::where('user_id', $userId)->get();
        
        $totalInvestasi = 0;
        $daftarAset = [];

        foreach ($portfolios as $porto) {
            // [LOGIKA BARU] Ambil Harga Pasar dari Database Aset (Bukan Hardcode lagi)
            $dataAset = Asset::where('symbol', $porto->asset_symbol)->first();
            
            // Kalau aset ditemukan, pakai harganya. Kalau tidak (misal dihapus), set 0.
            $hargaPasar = $dataAset ? $dataAset->current_price : 0; 

            // Hitung-hitungan (Sama seperti sebelumnya)
            $nilaiAset = $porto->quantity * $hargaPasar;
            $modalAwal = $porto->quantity * $porto->average_buy_price;
            $profit = $nilaiAset - $modalAwal;

            $totalInvestasi += $nilaiAset;

            $daftarAset[] = [
                'aset' => $porto->asset_symbol,
                'nama_lengkap' => $dataAset->name ?? 'Unknown', // Tambahan: Tampilkan nama panjang
                'jumlah' => $porto->quantity,
                'modal' => $modalAwal,
                'nilai_sekarang' => $nilaiAset,
                'cuan' => $profit
            ];
        }

        // 4. Kirim Data ke View
        return view('dashboard', [
            'user' => $user->name, 
            'rekap' => [
                'uang_tunai' => $totalCash,
                'nilai_investasi' => $totalInvestasi,
                'total_kekayaan' => $totalCash + $totalInvestasi
            ],
            'detail_aset' => $daftarAset
        ]);
    }
}