<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Portfolio;
use App\Models\Asset;

class PortfolioController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        
        // 1. Ambil Data Portofolio + Data Asetnya (Harga Sekarang)
        $items = Portfolio::where('user_id', $userId)
                          ->where('quantity', '>', 0) // Cuma ambil yang masih punya saldo
                          ->get();

        // 2. Siapkan Variabel Rekap
        $totalModal = 0;
        $totalNilaiSekarang = 0;
        $portfolioList = [];

        // 3. Loop untuk hitung satu-satu
        foreach ($items as $item) {
            // Ambil harga pasar terbaru dari tabel Assets
            $asset = Asset::where('symbol', $item->asset_symbol)->first();
            $currentPrice = $asset ? $asset->current_price : 0;

            // Hitung nilai
            $modalAset = $item->quantity * $item->average_buy_price;
            $nilaiAset = $item->quantity * $currentPrice;
            $cuanRp = $nilaiAset - $modalAset;
            $cuanPersen = ($modalAset > 0) ? ($cuanRp / $modalAset) * 100 : 0;

            // Masukkan ke rekap total
            $totalModal += $modalAset;
            $totalNilaiSekarang += $nilaiAset;

            // Masukkan ke list untuk tabel
            $portfolioList[] = (object) [
                'symbol' => $item->asset_symbol,
                'name' => $asset->name ?? 'Unknown',
                'type' => $asset->type ?? 'stock',
                'quantity' => $item->quantity,
                'avg_price' => $item->average_buy_price,
                'current_price' => $currentPrice,
                'current_value' => $nilaiAset,
                'profit_loss_rp' => $cuanRp,
                'profit_loss_pct' => $cuanPersen
            ];
        }

        // Hitung Total Profit Global
        $totalProfitRp = $totalNilaiSekarang - $totalModal;
        $totalProfitPct = ($totalModal > 0) ? ($totalProfitRp / $totalModal) * 100 : 0;

        return view('portfolio.index', compact(
            'portfolioList', 
            'totalModal', 
            'totalNilaiSekarang', 
            'totalProfitRp', 
            'totalProfitPct'
        ));
    }
}