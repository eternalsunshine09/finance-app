<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Portfolio;
use App\Models\Asset;
use App\Models\Wallet;
use App\Models\ExchangeRate; // Pastikan model ini ada
use Illuminate\Support\Facades\Auth;

class PortfolioController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // 1. Ambil Kurs USD ke IDR Terbaru
        $usdRate = ExchangeRate::where('from_currency', 'USD')->value('rate') ?? 15500;

        // 2. Ambil Portfolio dengan Relasi Asset
        $portfolios = Portfolio::with('asset')
            ->where('user_id', $user->id)
            ->where('quantity', '>', 0)
            ->get();

        $portfolioList = [];
        $totalModal = 0;
        $totalNilaiSekarang = 0;
        $chartLabels = [];
        $chartValues = [];

        foreach ($portfolios as $porto) {
            $asset = $porto->asset;
            
            // Tentukan Harga Beli & Harga Pasar
            $avgPrice = $porto->average_buy_price; 
            $currentPrice = $asset->current_price;
            
            $modalAwal = $porto->quantity * $avgPrice;
            $nilaiSekarang = $porto->quantity * $currentPrice;

            // --- LOGIKA KONVERSI KE IDR (Untuk Rekap Total) ---
            $modalAwalIDR = $modalAwal;
            $nilaiSekarangIDR = $nilaiSekarang;

            if ($asset->type == 'Crypto') {
                $modalAwalIDR = $modalAwal * $usdRate;
                $nilaiSekarangIDR = $nilaiSekarang * $usdRate;
            }

            // Hitung Profit/Loss
            $profitLossRp = $nilaiSekarang - $modalAwal;
            $profitLossPct = ($modalAwal > 0) ? ($profitLossRp / $modalAwal) * 100 : 0;

            // Simpan Data Bersih ke Array
            $portfolioList[] = (object) [
                'symbol' => $asset->symbol,
                'name' => $asset->name,
                'type' => $asset->type, // <--- PENTING: Kirim Tipe Aset
                'quantity' => $porto->quantity,
                'avg_price' => $avgPrice,
                'current_price' => $currentPrice,
                'current_value' => $nilaiSekarang, // Nilai asli (USD/IDR)
                'current_value_idr' => $nilaiSekarangIDR, // Nilai konversi (IDR)
                'profit_loss_rp' => $profitLossRp,
                'profit_loss_pct' => $profitLossPct,
            ];

            // Akumulasi Total (Semua dalam IDR)
            $totalModal += $modalAwalIDR;
            $totalNilaiSekarang += $nilaiSekarangIDR;

            // Data Chart (Hanya ambil yang signifikan)
            if ($nilaiSekarangIDR > 0) {
                $chartLabels[] = $asset->symbol;
                $chartValues[] = $nilaiSekarangIDR;
            }
        }

        $totalProfitRp = $totalNilaiSekarang - $totalModal;
        $totalProfitPct = ($totalModal > 0) ? ($totalProfitRp / $totalModal) * 100 : 0;

        return view('portfolio.index', compact(
            'portfolioList', 
            'totalModal', 
            'totalNilaiSekarang', 
            'totalProfitRp', 
            'totalProfitPct',
            'chartLabels',
            'chartValues',
            'usdRate' // Kirim rate jika view butuh
        ));
    }
}