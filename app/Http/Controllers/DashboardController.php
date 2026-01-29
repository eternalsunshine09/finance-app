<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PortfolioHistory;
use App\Models\Transaction;
use App\Models\Wallet;
use App\Models\Asset;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // 1. Ambil Kurs USD ke IDR Terbaru
        $usdRate = ExchangeRate::where('from_currency', 'USD')->value('rate') ?? 15500;

        // 2. Ambil semua portofolio user
        $portfolios = DB::table('portfolios')
            ->join('assets', 'portfolios.asset_symbol', '=', 'assets.symbol')
            ->where('portfolios.user_id', $user->id)
            ->where('portfolios.quantity', '>', 0)
            ->get();

        // 3. Hitung total kekayaan
        $totalKekayaan = 0;
        $totalNilaiInvestasi = 0;
        $totalUangTunai = Wallet::where('user_id', $user->id)->sum('balance');
        
        $detailAset = [];
        $chartLabels = [];
        $chartValues = [];

        foreach ($portfolios as $porto) {
            $nilaiSekarang = $porto->quantity * $porto->current_price;
            $modalAwal = $porto->quantity * $porto->average_buy_price;
            
            // Konversi ke IDR jika crypto
            if ($porto->type == 'Crypto') {
                $nilaiSekarangIDR = $nilaiSekarang * $usdRate;
                $modalAwalIDR = $modalAwal * $usdRate;
            } else {
                $nilaiSekarangIDR = $nilaiSekarang;
                $modalAwalIDR = $modalAwal;
            }

            $detailAset[] = [
                'aset' => $porto->symbol,
                'nama_lengkap' => $porto->name,
                'type' => $porto->type,
                'jumlah' => $porto->quantity,
                'modal' => $porto->average_buy_price,
                'nilai_sekarang' => $nilaiSekarang,
                'nilai_idr' => $nilaiSekarangIDR,
                'cuan' => $nilaiSekarang - $modalAwal,
            ];

            $totalNilaiInvestasi += $nilaiSekarangIDR;
            
            // Data untuk chart (ambil 6 terbesar)
            if ($nilaiSekarangIDR > 0) {
                $chartLabels[] = $porto->symbol;
                $chartValues[] = $nilaiSekarangIDR;
            }
        }

        $totalKekayaan = $totalUangTunai + $totalNilaiInvestasi;

        // 4. Hitung Growth Percentage dari Portfolio History
        $growthPercentage = $this->calculateGrowthPercentage($user, $totalKekayaan);

        // 5. Update atau buat portfolio history untuk bulan ini
        $this->updatePortfolioHistory($user, $totalKekayaan);

        return view('dashboard.index', [
            'rekap' => [
                'total_kekayaan' => $totalKekayaan,
                'uang_tunai' => $totalUangTunai,
                'nilai_investasi' => $totalNilaiInvestasi,
            ],
            'growth_percentage' => $growthPercentage,
            'detail_aset' => $detailAset,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
        ]);
    }

    public function getChartData(Request $request)
    {
        $user = Auth::user();
        $filter = $request->query('filter', '1M');

        // Tentukan berapa hari ke belakang berdasarkan filter
        $days = match($filter) {
            '1W'  => 7,
            '1M'  => 30,
            '3M'  => 90,
            '6M'  => 180,
            '1Y'  => 365,
            'ALL' => 1000, // Sesuaikan dengan sejak kapan user bergabung
            default => 30
        };

        // Ambil history dari database
        // Pastikan Anda punya tabel portfolio_histories yang mencatat nilai aset harian
        $history = \App\Models\PortfolioHistory::where('user_id', $user->id)
            ->where('date', '>=', now()->subDays($days))
            ->orderBy('date', 'asc')
            ->get();

        $labels = [];
        $values = [];

        foreach ($history as $data) {
            // Format label: Jika range pendek munculkan tgl/bln, jika panjang munculkan bln/thn
            $labels[] = ($days <= 30) 
                ? $data->date->format('d M') 
                : $data->date->format('M Y');
                
            $values[] = $data->total_value;
        }

        return response()->json([
            'labels' => $labels,
            'values' => $values
        ]);
    }

    /**
     * Hitung persentase pertumbuhan dari bulan lalu
     */
    private function calculateGrowthPercentage($user, $currentWealth)
    {
        $lastMonth = Carbon::now()->subMonth();
        
        // Cari data bulan lalu di portfolio_histories
        $lastMonthHistory = PortfolioHistory::where('user_id', $user->id)
            ->whereYear('date', $lastMonth->year)
            ->whereMonth('date', $lastMonth->month)
            ->first();

        if ($lastMonthHistory && $lastMonthHistory->total_value > 0) {
            // Hitung persentase pertumbuhan
            $growth = (($currentWealth - $lastMonthHistory->total_value) / $lastMonthHistory->total_value) * 100;
            return round($growth, 1);
        }

        // Jika tidak ada data bulan lalu, hitung dari rata-rata deposit vs withdraw
        return $this->calculateEstimatedGrowth($user);
    }

    /**
     * Estimasi growth jika tidak ada data historis
     */
    private function calculateEstimatedGrowth($user)
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        // Hitung net cash flow bulan lalu
        $lastMonthDeposits = Transaction::where('user_id', $user->id)
            ->where('type', 'TOPUP')
            ->whereBetween('date', [$startOfLastMonth, $endOfLastMonth])
            ->sum('amount_cash');

        $lastMonthWithdrawals = abs(Transaction::where('user_id', $user->id)
            ->where('type', 'WITHDRAW')
            ->whereBetween('date', [$startOfLastMonth, $endOfLastMonth])
            ->sum('amount_cash'));

        $lastMonthNetCashFlow = $lastMonthDeposits - $lastMonthWithdrawals;

        // Hitung current wealth untuk estimasi
        $currentWealth = Wallet::where('user_id', $user->id)->sum('balance');
        
        // Tambahkan nilai investasi
        $portfolios = DB::table('portfolios')
            ->join('assets', 'portfolios.asset_symbol', '=', 'assets.symbol')
            ->where('portfolios.user_id', $user->id)
            ->where('portfolios.quantity', '>', 0)
            ->get();

        $usdRate = ExchangeRate::where('from_currency', 'USD')->value('rate') ?? 15500;

        foreach ($portfolios as $porto) {
            $nilaiSekarang = $porto->quantity * $porto->current_price;
            $currentWealth += ($porto->type == 'Crypto') ? $nilaiSekarang * $usdRate : $nilaiSekarang;
        }

        // Estimasi: 30% dari net cash flow memberikan return
        if ($currentWealth > 0 && $lastMonthNetCashFlow > 0) {
            $estimatedGrowth = ($lastMonthNetCashFlow * 0.3) / $currentWealth * 100;
            return round($estimatedGrowth, 1);
        }

        return 0; // Default jika tidak bisa dihitung
    }

    /**
     * Update portfolio history untuk bulan ini
     */
    private function updatePortfolioHistory($user, $currentWealth)
    {
        $today = Carbon::today();
        
        // Tentukan awal dan akhir hari ini
        $startOfDay = $today->copy()->startOfDay();
        $endOfDay = $today->copy()->endOfDay();

        // PERBAIKAN: Gunakan whereBetween dengan parameter yang benar
        // Cek apakah sudah ada data untuk hari ini
        $existingHistory = PortfolioHistory::where('user_id', $user->id)
            ->whereBetween('created_at', [$startOfDay, $endOfDay])
            ->first();

        if (!$existingHistory) {
            // Simpan snapshot harian (opsional bisa dijadikan weekly/monthly)
            PortfolioHistory::create([
                'user_id' => $user->id,
                'date' => $today,
                'total_value' => $currentWealth,
            ]);

            // Hapus data lama (lebih dari 90 hari) untuk menjaga database
            PortfolioHistory::where('user_id', $user->id)
                ->where('date', '<', $today->copy()->subDays(90))
                ->delete();
        }
    }
}