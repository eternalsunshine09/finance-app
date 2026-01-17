<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Wajib import ini
use App\Models\Wallet;
use App\Models\Portfolio;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Data User yang Sedang Login (Dinamis)
        $user = Auth::user();
        $userId = $user->id;

        // 2. Ambil Total Uang Tunai (Cash)
        $wallets = Wallet::where('user_id', $userId)->get();
        $totalCash = $wallets->sum('balance');

        // 3. Ambil Aset & Hitung Valuasinya
        $portfolios = Portfolio::where('user_id', $userId)->get();
        
        $totalInvestasi = 0;
        $daftarAset = [];

        foreach ($portfolios as $porto) {
            // [LOGIKA SEMENTARA] Harga Pasar Hardcoded
            // Nanti diganti dengan API real-time
            $hargaPasar = 2500; 

            // Hitung-hitungan
            $nilaiAset = $porto->quantity * $hargaPasar;
            $modalAwal = $porto->quantity * $porto->average_buy_price;
            $profit = $nilaiAset - $modalAwal;

            $totalInvestasi += $nilaiAset;

            $daftarAset[] = [
                'aset' => $porto->asset_symbol,
                'jumlah' => $porto->quantity,
                'modal' => $modalAwal,
                'nilai_sekarang' => $nilaiAset,
                'cuan' => $profit
            ];
        }

        // 4. Kirim Data ke View (Blade)
        return view('dashboard', [
            'user' => $user->name, // Mengambil nama asli dari database
            'rekap' => [
                'uang_tunai' => $totalCash,
                'nilai_investasi' => $totalInvestasi,
                'total_kekayaan' => $totalCash + $totalInvestasi
            ],
            'detail_aset' => $daftarAset
        ]);
    }
}