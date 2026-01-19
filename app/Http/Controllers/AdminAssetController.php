<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http; // Pastikan ini ada!

class AdminAssetController extends Controller
{
    public function index()
    {
        $assets = Asset::orderBy('created_at', 'desc')->get();
        return view('admin.assets.index', compact('assets'));
    }

    public function create()
    {
        return view('admin.assets.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'symbol'        => 'required|unique:assets,symbol',
            'name'          => 'required',
            'type'          => 'required',
            'current_price' => 'required|numeric',
            'api_id'        => 'nullable|string',
        ]);

        Asset::create([
            'symbol'        => strtoupper($request->symbol),
            'name'          => $request->name,
            'type'          => $request->type,
            'subtype'       => $request->subtype,
            'api_id'        => $request->api_id,
            'current_price' => $request->current_price,
        ]);

        return redirect()->route('admin.assets.index')->with('success', 'Aset berhasil ditambahkan!');
    }

    public function updatePrice(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);
        $asset->update(['current_price' => $request->current_price]);
        return back()->with('success', 'Harga berhasil diupdate manual.');
    }

    public function destroy($id)
    {
        Asset::destroy($id);
        return back()->with('success', 'Aset berhasil dihapus.');
    }
    
    // --- 1. UPDATE KURS MANUAL ---
    public function updateRate(Request $request)
    {
        $request->validate([
            'rate' => 'required|numeric|min:1'
        ]);

        ExchangeRate::create([
            'from_currency' => 'USD',
            'to_currency'   => 'IDR',
            'rate'          => $request->rate,
            'date'          => now(),
        ]);

        return back()->with('success', 'Kurs USD berhasil diupdate manual menjadi Rp ' . number_format($request->rate));
    }

    // --- 2. SYNC HARGA ASET (HYBRID: COINGECKO + YAHOO) ---
    // Nama method ini HARUS 'syncPrices' agar cocok dengan routes/web.php
    public function syncPrices()
    {
        // Ambil semua aset yang punya API ID
        $assets = Asset::whereNotNull('api_id')->where('api_id', '!=', '')->get();
        
        if ($assets->isEmpty()) {
            return back()->with('error', 'Tidak ada aset dengan API ID.');
        }

        $successCount = 0;
        $failCount = 0;

        foreach ($assets as $asset) {
            try {
                $price = null;

                // --- OPSI A: JIKA CRYPTO (Gunakan CoinGecko) ---
                if ($asset->type == 'Crypto') {
                    // API ID CoinGecko harus lowercase (misal: bitcoin, ethereum, tether)
                    $apiId = strtolower($asset->api_id); 
                    $url = "https://api.coingecko.com/api/v3/simple/price?ids={$apiId}&vs_currencies=usd";
                    
                    $response = Http::get($url);
                    
                    if ($response->successful()) {
                        $data = $response->json();
                        if (isset($data[$apiId]['usd'])) {
                            $price = $data[$apiId]['usd'];
                        }
                    }
                } 
                
                // --- OPSI B: JIKA SAHAM/STOCK (Gunakan Yahoo Finance) ---
                // CoinGecko jelek untuk saham, jadi kita alihkan ke Yahoo
                else {
                    $yahooSymbol = $asset->api_id;
                    
                    // Otomatis tambahkan .JK jika Saham Indo dan belum ada ekstensinya
                    if ($asset->type == 'Stock' && !str_contains($yahooSymbol, '.') && strlen($yahooSymbol) == 4) {
                        $yahooSymbol .= '.JK'; 
                    }

                    $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$yahooSymbol}";
                    $response = Http::get($url);

                    if ($response->successful()) {
                        $meta = $response->json()['chart']['result'][0]['meta'] ?? null;
                        if ($meta && isset($meta['regularMarketPrice'])) {
                            $price = $meta['regularMarketPrice'];
                        }
                    }
                }

                // --- SIMPAN JIKA HARGA DITEMUKAN ---
                if ($price) {
                    $asset->update([
                        'current_price' => $price,
                        'updated_at' => now()
                    ]);
                    $successCount++;
                } else {
                    $failCount++;
                }

            } catch (\Exception $e) {
                $failCount++;
                continue; // Lanjut ke aset berikutnya jika error
            }
        }

        return back()->with('success', "Sync Selesai! Berhasil: $successCount, Gagal: $failCount");
    }
}