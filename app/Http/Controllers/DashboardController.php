<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Wallet;
use App\Models\Portfolio;
use App\Models\Asset;
use App\Models\ExchangeRate;
use App\Models\Transaction; // Pastikan ini ada
use App\Models\PortfolioHistory;
use Carbon\Carbon;

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

        // --- HISTORY RECORDING (Otomatis Simpan Total Harian) ---
        $totalKekayaanSaatIni = $totalCashIDR + $totalInvestasiIDR;
        $today = now()->format('Y-m-d');

        PortfolioHistory::updateOrCreate(
            ['user_id' => $userId, 'date' => $today],
            ['total_value' => $totalKekayaanSaatIni]
        );

        // Kirim Data ke View
        return view('dashboard', [
            'user' => $user->name, 
            'rekap' => [
                'uang_tunai' => $totalCashIDR,
                'nilai_investasi' => $totalInvestasiIDR,
                'total_kekayaan' => $totalKekayaanSaatIni
            ],
            'detail_aset' => $daftarAset,
            'chartLabels' => $donutLabels, 
            'chartValues' => $donutValues,
            'usdRate' => $usdRate
        ]);
    }

    // --- API UNTUK CHART DINAMIS ---
    public function getChartData(Request $request)
    {
        $user = Auth::user();
        $filter = $request->query('filter', '1M'); // Default 1 Bulan
        $month = $request->query('month');
        $year = $request->query('year');

        // Logic Filter Waktu
        if ($filter == 'custom' && $month && $year) {
            $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
            $formatGroup = 'd M'; // 01 Jan
        } 
        elseif ($filter == '1D') {
            $startDate = now()->startOfDay();
            $endDate = now()->endOfDay();
            $formatGroup = 'H:00'; // 13:00
        }
        elseif ($filter == '1W') {
            $startDate = now()->subDays(7);
            $endDate = now();
            $formatGroup = 'D, d M'; // Mon, 12 Jan
        }
        elseif ($filter == '1Y') {
            $startDate = now()->subYear();
            $endDate = now();
            $formatGroup = 'M Y'; // Jan 2024
        }
        else { // Default 1M
            $startDate = now()->subMonth();
            $endDate = now();
            $formatGroup = 'd M';
        }

        // Ambil Data Transaksi (Cashflow)
        // Kita hitung pergerakan uang (Masuk - Keluar) per periode
        $transactions = Transaction::where('user_id', $user->id)
                        ->where('status', 'approved')
                        ->whereBetween('date', [$startDate, $endDate])
                        ->orderBy('date', 'asc')
                        ->get()
                        ->groupBy(function($item) use ($filter, $formatGroup) {
                            return Carbon::parse($item->date)->format($formatGroup);
                        });

        $labels = [];
        $values = [];

        foreach ($transactions as $date => $group) {
            $labels[] = $date;
            // Sum amount_cash (Positif = Masuk, Negatif = Keluar)
            $values[] = $group->sum('amount_cash');
        }

        // Jika data kosong, berikan array kosong agar chart tidak error
        if (empty($labels)) {
            $labels = [];
            $values = [];
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }
}