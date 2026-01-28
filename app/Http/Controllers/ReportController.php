<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Portfolio;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();
        $now = Carbon::now();

        // ---------------------------------------------------------------------
        // 1. RINGKASAN LABA/RUGI (PROFIT & LOSS)
        // ---------------------------------------------------------------------
        
        // A. Realized Profit (Sudah Dijual)
        // Cek dulu apakah kolom 'profit_amount' sudah ada di database biar gak error
        $hasProfitCol = Schema::hasColumn('transactions', 'profit_amount');
        
        $realizedProfit = 0;
        if ($hasProfitCol) {
            $realizedProfit = Transaction::where('user_id', $userId)
                ->where('type', 'SELL')
                ->where('status', 'approved')
                ->sum('profit_amount');
        }

        // B. Unrealized Profit (Masih Dipegang)
        // Rumus: (Harga Sekarang - Harga Beli) * Quantity
        $portfolios = Portfolio::with('asset')->where('user_id', $userId)->where('quantity', '>', 0)->get();
        $unrealizedProfit = 0;
        $totalInvestedCapital = 0;
        $totalCurrentValue = 0;

        foreach ($portfolios as $item) {
            $livePrice = $item->asset->current_price ?? 0;
            
            $valNow = $item->quantity * $livePrice;
            $costBasis = $item->quantity * $item->average_buy_price;
            
            $unrealizedProfit += ($valNow - $costBasis);
            $totalInvestedCapital += $costBasis;
            $totalCurrentValue += $valNow;
        }

        // C. Total Dividen (Pendapatan Pasif)
        $totalDividends = Transaction::where('user_id', $userId)
            ->whereIn('type', ['DIV_CASH']) 
            ->where('status', 'approved')
            ->sum('amount_cash');

        // Total Return %
        $totalReturnPct = $totalInvestedCapital > 0 
            ? (($realizedProfit + $unrealizedProfit + $totalDividends) / $totalInvestedCapital) * 100 
            : 0;

        // ---------------------------------------------------------------------
        // 2. ARUS KAS (CASH FLOW) BULAN INI
        // ---------------------------------------------------------------------
        $moneyIn = Transaction::where('user_id', $userId)
            ->whereIn('type', ['TOPUP', 'SELL', 'DIV_CASH'])
            ->where('status', 'approved')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('amount_cash');

        $moneyOut = Transaction::where('user_id', $userId)
            ->whereIn('type', ['WITHDRAW', 'BUY']) 
            ->where('status', 'approved')
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->sum('amount_cash');
        
        $moneyOut = abs($moneyOut); // Pastikan positif untuk tampilan

        // ---------------------------------------------------------------------
        // 3. ALOKASI ASET (PIE CHART DATA)
        // ---------------------------------------------------------------------
        $allocation = $portfolios->groupBy(function($item) {
                return $item->asset->type ?? 'Other';
            })
            ->map(function ($row) {
                return $row->sum(function ($item) {
                    $price = $item->asset->current_price ?? 0;
                    return $item->quantity * $price;
                });
            });

        return view('report.index', compact(
            'realizedProfit',
            'unrealizedProfit',
            'totalDividends',
            'totalReturnPct',
            'moneyIn',
            'moneyOut',
            'allocation',
            'totalCurrentValue'
        ));
    }

    /**
     * API untuk Grafik Tren (Bar Chart)
     */
    public function getChartData(Request $request)
    {
        $filter = $request->query('filter', '1M'); 
        $userId = Auth::id();
        $now = Carbon::now();
        
        $labels = [];
        $buyData = [];
        $sellData = [];

        // Tentukan Range Waktu
        switch ($filter) {
            case '1D': $start = $now->copy()->subDay(); $interval = 'hour'; $format = 'H:00'; break;
            case '1W': $start = $now->copy()->subWeek(); $interval = 'day'; $format = 'D d'; break;
            case '1M': $start = $now->copy()->subDays(30); $interval = 'day'; $format = 'd M'; break;
            case '1Y': $start = $now->copy()->subYear(); $interval = 'month'; $format = 'M Y'; break;
            case 'ALL': 
                $firstTrx = Transaction::where('user_id', $userId)->orderBy('created_at', 'asc')->first();
                $start = $firstTrx ? Carbon::parse($firstTrx->created_at) : $now->copy()->subYear();
                $interval = 'year'; $format = 'Y';
                break;
            default: $start = $now->copy()->subMonth(); $interval = 'day'; $format = 'd M';
        }

        // Ambil Transaksi
        $transactions = Transaction::where('user_id', $userId)
            ->where('status', 'approved')
            ->where('created_at', '>=', $start)
            ->get();

        $currentDate = $start->copy();
        
        // Loop timeline untuk mengisi data grafik
        while ($currentDate <= $now) {
            $label = $currentDate->format($format);
            $labels[] = $label;

            $periodTrans = $transactions->filter(function ($trx) use ($currentDate, $interval) {
                $trxDate = Carbon::parse($trx->created_at);
                if ($interval == 'hour') return $trxDate->isSameHour($currentDate);
                if ($interval == 'day') return $trxDate->isSameDay($currentDate);
                if ($interval == 'month') return $trxDate->isSameMonth($currentDate) && $trxDate->isSameYear($currentDate);
                if ($interval == 'year') return $trxDate->isSameYear($currentDate);
                return false;
            });

            // Hitung Total Beli & Jual di titik waktu ini
            $buyData[] = $periodTrans->where('type', 'BUY')->sum(function($t) { return abs($t->amount_cash); });
            $sellData[] = $periodTrans->where('type', 'SELL')->sum('amount_cash');

            if ($interval == 'hour') $currentDate->addHour();
            elseif ($interval == 'day') $currentDate->addDay();
            elseif ($interval == 'month') $currentDate->addMonth();
            elseif ($interval == 'year') $currentDate->addYear();
        }

        return response()->json([
            'labels' => $labels,
            'buy' => $buyData,
            'sell' => $sellData
        ]);
    }

    /**
     * Download CSV
     */
    public function exportCsv()
    {
        $userId = Auth::id();
        $fileName = 'Laporan_Investasi_' . date('Y-m-d_H-i') . '.csv';
        
        $transactions = Transaction::where('user_id', $userId)->orderBy('created_at', 'desc')->get();

        $headers = [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function() use($transactions) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'Jenis', 'Aset', 'Jumlah', 'Harga', 'Total (IDR)', 'Tanggal', 'Ket']);
            foreach ($transactions as $row) {
                fputcsv($file, [
                    '#' . $row->id, $row->type, $row->asset_symbol ?? '-', $row->amount, $row->price_per_unit, abs($row->amount_cash), $row->created_at->format('d M Y H:i'), $row->description
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}