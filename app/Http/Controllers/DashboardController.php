<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wallet;
use App\Models\Portfolio;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = 2; // Kita hardcode ID si Budi dulu

        // 1. Ambil Total Uang Tunai (Cash)
        $wallets = Wallet::where('user_id', $userId)->get();
        $totalCash = $wallets->sum('balance');

        // 2. Ambil Aset & Hitung Valuasinya
        $portfolios = Portfolio::where('user_id', $userId)->get();
        
        $totalInvestasi = 0;
        $daftarAset = [];

        foreach ($portfolios as $porto) {
            // CERITA: Anggap harga pasar ANTM sekarang NAIK jadi Rp 2.500
            // (Nanti ini bisa kita ambil dari database harga live)
            $hargaPasar = 2500; 
            
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

        // 3. Output Data Laporan
        return response()->json([
            'user' => 'Budi Investor',
            'rekap_keuangan' => [
                'uang_tunai' => $totalCash,
                'nilai_investasi' => $totalInvestasi,
                'total_kekayaan' => $totalCash + $totalInvestasi
            ],
            'detail_aset' => $daftarAset
        ]);
    }
}