<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Portfolio;
use App\Models\Asset;
use App\Models\ExchangeRate; // Pastikan model ini di-import

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Data User
        $user = Auth::user();

        // Cek Role: Kalau Admin, tendang ke Dashboard Admin
        if ($user->role == 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $userId = $user->id;

        // 2. Ambil Kurs USD ke IDR Terbaru (Default 15500 jika tidak ada)
        $usdRate = ExchangeRate::where('from_currency', 'USD')->value('rate') ?? 15500;

        // 3. Ambil Total Uang Tunai (Cash)
        // Kita konversi saldo USD ke IDR untuk perhitungan total kekayaan
        $wallets = Wallet::where('user_id', $userId)->get();
        $totalCashIDR = 0;

        foreach ($wallets as $wallet) {
            if ($wallet->currency == 'USD') {
                $totalCashIDR += $wallet->balance * $usdRate;
            } else {
                $totalCashIDR += $wallet->balance;
            }
        }

        // 4. Ambil Aset & Hitung Valuasinya
        $portfolios = Portfolio::where('user_id', $userId)->get();
        
        $totalInvestasiIDR = 0;
        $daftarAset = [];

        foreach ($portfolios as $porto) {
            // Ambil Data Aset
            $dataAset = Asset::where('symbol', $porto->asset_symbol)->first();
            
            // Default values jika aset tidak ditemukan (mencegah error)
            $hargaPasar = $dataAset ? $dataAset->current_price : 0; 
            $tipeAset = $dataAset ? $dataAset->type : 'Stock';
            $namaAset = $dataAset ? $dataAset->name : 'Unknown';

            // Hitung Nilai dalam Mata Uang Asli (USD/IDR)
            $nilaiAsetAsli = $porto->quantity * $hargaPasar;
            $modalAwalAsli = $porto->quantity * $porto->average_buy_price;
            $profitAsli = $nilaiAsetAsli - $modalAwalAsli;

            // Hitung Nilai dalam IDR (untuk Total Kekayaan & Chart)
            $nilaiAsetIDR = $nilaiAsetAsli;
            if ($tipeAset == 'Crypto') { // Asumsi Crypto pakai USD
                $nilaiAsetIDR = $nilaiAsetAsli * $usdRate;
            }

            $totalInvestasiIDR += $nilaiAsetIDR;

            // MENYUSUN DATA UNTUK VIEW
            $daftarAset[] = [
                'aset' => $porto->asset_symbol,
                'nama_lengkap' => $namaAset,
                'type' => $tipeAset, // PENTING: Kirim tipe aset (Crypto/Stock)
                'jumlah' => $porto->quantity,
                'modal' => $modalAwalAsli,       // Nilai mata uang asli
                'nilai_sekarang' => $nilaiAsetAsli, // Nilai mata uang asli
                'nilai_idr' => $nilaiAsetIDR,    // Nilai estimasi IDR (opsional ditampilkan)
                'cuan' => $profitAsli            // Profit mata uang asli
            ];
        }

        // 5. Siapkan Data Chart (Semua dalam IDR agar proporsional)
        $chartLabels = [];
        $chartValues = [];

        // Masukkan Kas Tunai ke Chart (IDR)
        if ($totalCashIDR > 0) {
            $chartLabels[] = 'Uang Tunai (Est. IDR)';
            $chartValues[] = $totalCashIDR;
        }

        // Masukkan Aset ke Chart (IDR)
        foreach ($daftarAset as $item) {
            if ($item['nilai_idr'] > 0) {
                $chartLabels[] = $item['aset'];
                $chartValues[] = $item['nilai_idr'];
            }
        }

        // 6. Kirim Data ke View
        return view('dashboard', [
            'user' => $user->name, 
            'rekap' => [
                'uang_tunai' => $totalCashIDR,
                'nilai_investasi' => $totalInvestasiIDR,
                'total_kekayaan' => $totalCashIDR + $totalInvestasiIDR
            ],
            'detail_aset' => $daftarAset,
            'chartLabels' => $chartLabels, 
            'chartValues' => $chartValues,
            'usdRate' => $usdRate // Kirim rate jika ingin ditampilkan di view
        ]);
    }
}