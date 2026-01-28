<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Services\MarketService;

class MarketController extends Controller
{
    protected $marketService;

    public function __construct(MarketService $marketService)
    {
        $this->marketService = $marketService;
    }

    // --- HALAMAN PASAR INDONESIA ---
    public function index(Request $request)
    {
        // 1. Timeframe Logic
        $timeframes = [
            '1d' => '1H', '1mo' => '1B', '3mo' => '3B', 
            '1y' => '1T', '5y' => '5T'
        ];
        $tf = $request->query('timeframe', '1mo');
        $label = $timeframes[$tf] ?? '1B';

        // 2. Ambil Data IHSG dari Service (Grafik & Harga)
        $ihsg = $this->marketService->getIHSG($tf);

        // 3. Ambil List Saham (Paginate untuk fix error "hasPages")
        $assets = Asset::where('type', 'Stock')
                       ->orderBy('symbol', 'asc')
                       ->paginate(10); 

        return view('market.index', [
            'ihsg' => $ihsg,
            'assets' => $assets,
            'timeframes' => $timeframes,
            'timeframe' => $tf, // Fix variable name for view check
            'currentTimeframeLabel' => $label
        ]);
    }

    // --- HALAMAN REKSADANA ---
    public function reksadana()
    {
        $funds = Asset::where('type', 'Mutual Fund')
                      ->orderBy('subtype', 'asc')
                      ->orderBy('name', 'asc')
                      ->get();

        return view('market.reksadana', compact('funds'));
    }

    // --- HALAMAN CRYPTO ---
    public function crypto(Request $request)
    {
        $tf = $request->query('timeframe', '1mo');
        $btc = $this->marketService->getCrypto($tf);
        $assets = Asset::where('type', 'Crypto')->orderBy('symbol', 'asc')->paginate(10);

        return view('market.crypto', [
            'btc' => $btc, 
            'assets' => $assets,
            'timeframe' => $tf
        ]);
    }
}