<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Services\MarketService; // Import Service yang baru dibuat
use Illuminate\Http\Request;

class AssetController extends Controller
{
    protected $marketService;

    // 1. Dependency Injection
    // Laravel otomatis memasukkan MarketService ke sini
    public function __construct(MarketService $marketService)
    {
        $this->marketService = $marketService;
    }

    // --- HALAMAN UTAMA (IDX) ---
    public function index()
    {
        // Panggil fungsi dari Service, codingan jadi pendek & rapi!
        $ihsg = $this->marketService->getIHSG();
        
        $assets = Asset::where('type', 'stock')->orderBy('symbol', 'asc')->get();

        return view('market.index', compact('ihsg', 'assets'));
    }

    // --- HALAMAN US MARKET ---
    public function usMarket()
    {
        $sp500 = $this->marketService->getUSMarket();
        
        $assets = Asset::where('type', 'us_stock')->get();

        return view('market.us', compact('sp500', 'assets'));
    }

    // --- HALAMAN CRYPTO ---
    public function cryptoMarket()
    {
        $btc = $this->marketService->getCrypto();
        
        $assets = Asset::where('type', 'crypto')->get();

        return view('market.crypto', compact('btc', 'assets'));
    }

    // ... (Fungsi store, update, destroy biarkan tetap di sini karena itu urusan Database, bukan API) ...
    
    public function store(Request $request)
    {
        // ... codingan simpan ...
    }
}