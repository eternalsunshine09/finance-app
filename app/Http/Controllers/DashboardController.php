<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wallet;
use App\Models\Portfolio;
use App\Models\Asset;
use App\Models\ExchangeRate;
use App\Models\PortfolioHistory; // <--- JANGAN LUPA IMPORT MODEL BARU INI

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil Data User
        $user = Auth::user();

        if ($user->role == 'admin') {
            return redirect()->route('admin.dashboard');
        }

        $userId = $user->id;

        // 2. Ambil Kurs USD ke IDR Terbaru
        $usdRate = ExchangeRate::where('from_currency', 'USD')->value('rate') ?? 15500;

        // 3. Ambil Total Uang Tunai (Cash)
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
            $dataAset = Asset::where('symbol', $porto->asset_symbol)->first();
            
            $hargaPasar = $dataAset ? $dataAset->current_price : 0; 
            $tipeAset = $dataAset ? $dataAset->type : 'Stock';
            $namaAset = $dataAset ? $dataAset->name : 'Unknown';

            // Hitung Nilai Asli
            $nilaiAsetAsli = $porto->quantity * $hargaPasar;
            $modalAwalAsli = $porto->quantity * $porto->average_buy_price;
            $profitAsli = $nilaiAsetAsli - $modalAwalAsli;

            // Hitung Nilai IDR
            $nilaiAsetIDR = $nilaiAsetAsli;
            if ($tipeAset == 'Crypto') { 
                $nilaiAsetIDR = $nilaiAsetAsli * $usdRate;
            }

            $totalInvestasiIDR += $nilaiAsetIDR;

            $daftarAset[] = [
                'aset' => $porto->asset_symbol,
                'nama_lengkap' => $namaAset,
                'type' => $tipeAset,
                'jumlah' => $porto->quantity,
                'modal' => $modalAwalAsli,
                'nilai_sekarang' => $nilaiAsetAsli,
                'nilai_idr' => $nilaiAsetIDR,
                'cuan' => $profitAsli
            ];
        }

        // --- CHART 1: ALOKASI (DONUT CHART) ---
        $donutLabels = [];
        $donutValues = [];

        if ($totalCashIDR > 0) {
            $donutLabels[] = 'Cash';
            $donutValues[] = $totalCashIDR;
        }
        foreach ($daftarAset as $item) {
            if ($item['nilai_idr'] > 0) {
                $donutLabels[] = $item['aset'];
                $donutValues[] = $item['nilai_idr'];
            }
        }

        // ---------------------------------------------------------
        // FITUR BARU: HISTORY PORTFOLIO (LINE CHART)
        // ---------------------------------------------------------

        $totalKekayaanSaatIni = $totalCashIDR + $totalInvestasiIDR;
        $today = now()->format('Y-m-d');

        // STEP 6: SIMPAN/UPDATE DATA HARI INI KE DATABASE
        // Ini akan berjalan otomatis setiap user buka dashboard
        PortfolioHistory::updateOrCreate(
            ['user_id' => $userId, 'date' => $today],
            ['total_value' => $totalKekayaanSaatIni]
        );

        // STEP 7: AMBIL DATA HISTORY 30 HARI TERAKHIR
        $histories = PortfolioHistory::where('user_id', $userId)
                        ->orderBy('date', 'asc') // Urutkan dari tanggal lama ke baru
                        ->limit(30)
                        ->get();

        // Format data untuk Chart.js (Line Chart)
        $lineChartLabels = [];
        $lineChartValues = [];

        foreach($histories as $h) {
            $lineChartLabels[] = $h->date->format('d M'); // Contoh: "19 Jan"
            $lineChartValues[] = $h->total_value;
        }

        // Jika user baru (history kosong), masukkan data hari ini saja agar chart tidak error
        if (count($lineChartLabels) == 0) {
            $lineChartLabels[] = now()->format('d M');
            $lineChartValues[] = $totalKekayaanSaatIni;
        }

        // ---------------------------------------------------------

        // 8. Kirim Semua Data ke View
        return view('dashboard', [
            'user' => $user->name, 
            'rekap' => [
                'uang_tunai' => $totalCashIDR,
                'nilai_investasi' => $totalInvestasiIDR,
                'total_kekayaan' => $totalKekayaanSaatIni
            ],
            'detail_aset' => $daftarAset,
            
            // Data untuk Donut Chart (Alokasi)
            'chartLabels' => $donutLabels, 
            'chartValues' => $donutValues,
            
            // Data untuk Line Chart (Pertumbuhan - BARU)
            'lineChartLabels' => $lineChartLabels,
            'lineChartValues' => $lineChartValues,
            
            'usdRate' => $usdRate
        ]);
    }
}