<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Services\MarketService;

class MarketController extends Controller
{
    protected $marketService;

    // Inject Service
    public function __construct(MarketService $marketService)
    {
        $this->marketService = $marketService;
    }

    /**
     * Helper untuk Label Timeframe
     */
    private function getTimeframeLabel($tf)
    {
        $timeframes = [
            '1d' => '1H', '5d' => '5H', '1mo' => '1B', 
            '3mo' => '3B', '6mo' => '6B', '1y' => '1T', '5y' => '5T'
        ];
        return [
            'list' => $timeframes,
            'current_label' => $timeframes[$tf] ?? '1B'
        ];
    }

    // =========================================================================
    // 1. PASAR INDONESIA (IHSG)
    // =========================================================================
    public function index(Request $request)
    {
        $tf = $request->query('timeframe', '1mo');
        $tfData = $this->getTimeframeLabel($tf);

        // Ambil Data IHSG
        $ihsg = $this->marketService->getIHSG($tf);

        // Ambil Saham Lokal
        $assets = Asset::where('type', 'Stock')
                       ->orderBy('symbol', 'asc')
                       ->paginate(10); 

        return view('market.index', [
            'ihsg' => $ihsg,
            'assets' => $assets,
            'timeframes' => $tfData['list'],
            'timeframe' => $tf,
            'currentTimeframeLabel' => $tfData['current_label']
        ]);
    }

    // =========================================================================
    // 2. PASAR AMERIKA (S&P 500) - FIX ERROR DISINI
    // =========================================================================
    // Cari function us() di App\Http\Controllers\MarketController.php
    public function us(Request $request)
    {
        $tf = $request->query('timeframe', '1mo');
        $tfData = $this->getTimeframeLabel($tf);

        // Ambil Data S&P 500 (Chart)
        $sp500 = $this->marketService->getUSMarket($tf);

        // --- PERBAIKAN DI SINI ---
        // Ubah query agar HANYA mengambil 'US Stock'.
        // Jangan pakai 'Stock' lagi karena itu kode untuk saham Indo.
        $assets = Asset::where('type', 'US Stock') 
                       ->orderBy('symbol', 'asc')
                       ->paginate(10);

        return view('market.us', [
            'sp500' => $sp500,
            'assets' => $assets,
            'timeframes' => $tfData['list'],
            'timeframe' => $tf,
            'currentTimeframeLabel' => $tfData['current_label']
        ]);
    }

    // =========================================================================
    // 3. PASAR CRYPTO (BITCOIN)
    // =========================================================================
    public function crypto(Request $request)
    {
        $tf = $request->query('timeframe', '1mo');
        $tfData = $this->getTimeframeLabel($tf);

        // Ambil Data BTC
        $btc = $this->marketService->getCrypto($tf);

        // Ambil Aset Crypto
        $assets = Asset::where('type', 'Crypto')
                       ->orderBy('symbol', 'asc')
                       ->paginate(10);

        return view('market.crypto', [
            'btc' => $btc, 
            'assets' => $assets, // Ubah nama variabel jadi assets agar konsisten
            'timeframes' => $tfData['list'],
            'timeframe' => $tf,
            'currentTimeframeLabel' => $tfData['current_label']
        ]);
    }

    // =========================================================================
    // 4. REKSADANA
    // =========================================================================
    public function reksadana()
    {
        $funds = Asset::where('type', 'Mutual Fund')
                      ->orderBy('subtype', 'asc') // Misal: Pasar Uang, Saham, Campuran
                      ->orderBy('name', 'asc')
                      ->get();

        return view('market.reksadana', compact('funds'));
    }

    // =========================================================================
    // 5. KOMODITAS (EMAS/MINYAK)
    // =========================================================================
    public function commodities(Request $request)
    {
        $tf = $request->query('timeframe', '1mo');
        $tfData = $this->getTimeframeLabel($tf);

        // 1. Ambil Data Live & PAKSA REFRESH
        $goldLive = $this->marketService->getGoldPriceIdr($tf, true);

        // 2. AUTO-UPDATE DATABASE
        if ($goldLive['success']) {
            // Kita cari aset yang simbolnya 'GOLD' (Apapun huruf besar/kecilnya di DB, kita paksa match)
            $asset = Asset::where('symbol', 'GOLD')->first();

            if ($asset) {
                // Update jika ketemu
                $asset->update([
                    'current_price' => $goldLive['price'],
                    'change_percent' => $goldLive['change_percent'],
                    'updated_at' => now()
                ]);
            } else {
                // DEBUG: Jika aset tidak ketemu, berarti data di DB hilang/salah
                // Kode ini akan otomatis membuat data baru jika 'GOLD' tidak ada
                Asset::create([
                    'symbol' => 'GOLD',
                    'name' => 'Emas Spot IDR',
                    'type' => 'Gold',
                    'current_price' => $goldLive['price'],
                    'change_percent' => $goldLive['change_percent']
                ]);
            }
        }

        // 3. Ambil Data untuk View
        $assets = Asset::where('type', 'Gold')->orderBy('name', 'asc')->paginate(10);

        return view('market.commodities', [
            'gold' => $goldLive,
            'assets' => $assets,
            'timeframes' => $tfData['list'],
            'timeframe' => $tf,
            'currentTimeframeLabel' => $tfData['current_label']
        ]);
    }

    // =========================================================================
    // 6. KURS VALAS (USER VIEW)
    // =========================================================================
    public function valas()
    {
        // Ambil semua data kurs dari database
        $rates = \App\Models\ExchangeRate::orderBy('from_currency', 'asc')->get();

        return view('market.valas', compact('rates'));
    }
}