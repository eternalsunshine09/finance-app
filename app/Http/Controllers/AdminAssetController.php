<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http; // Pastikan ini ada!

class AdminAssetController extends Controller
{
public function index(Request $request)
    {
        $query = Asset::query();

        // 1. Fitur Search (Simbol atau Nama)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('symbol', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // 2. Fitur Filter Kategori
        if ($request->filled('type') && $request->type !== 'All') {
            $query->where('type', $request->type);
        }

        // Urutkan dan Paginate
        $assets = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
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
            'logo'          => 'nullable|string',
            'name'          => 'required',
            'type'          => 'required',
            'current_price' => 'required|numeric',
            'api_id'        => 'nullable|string',
        ]);

        Asset::create([
            'symbol'        => strtoupper($request->symbol),
            'logo'          => $request->logo,
            'name'          => $request->name,
            'type'          => $request->type,
            'subtype'       => $request->subtype,
            'api_id'        => $request->api_id,
            'current_price' => $request->current_price,
        ]);

        return redirect()->route('admin.assets.index')->with('success', 'Aset berhasil ditambahkan!');
    }

    // 3. HALAMAN EDIT (FULL)
    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        return view('admin.assets.edit', compact('asset'));
    }

    // 4. PROSES UPDATE DATA (FULL)
    public function update(Request $request, $id)
    {
        $request->validate([
            'symbol'        => 'required|unique:assets,symbol,'.$id, // Ignore unique self
            'name'          => 'required',
            'type'          => 'required',
            'logo'          => 'nullable|string', // String agar muat base64/link panjang
            'api_id'        => 'nullable|string',
        ]);

        $asset = Asset::findOrFail($id);
        
        $asset->update([
            'symbol'        => strtoupper($request->symbol),
            'name'          => $request->name,
            'type'          => $request->type,
            'subtype'       => $request->subtype,
            'logo'          => $request->logo,
            'api_id'        => $request->api_id,
            // Harga tidak diupdate disini agar tidak menimpa harga live, 
            // kecuali user memaksa update harga di form edit (opsional)
        ]);

        return redirect()->route('admin.assets.index')->with('success', 'Data aset berhasil diperbarui!');
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
// 🔥 UPDATE LOGIKA SYNC AGAR MENGAMBIL LOGO JUGA
public function syncPrices()
{
    try {
        $assets = Asset::whereNotNull('api_id')->where('api_id', '!=', '')->get();
        
        if ($assets->isEmpty()) {
            return back()->with('error', 'Tidak ada aset dengan API ID.');
        }

        $successCount = 0;
        $failCount = 0;

        // 1. KUMPULKAN ID CRYPTO UNTUK BATCH UPDATE (Supaya Hemat API Call)
        // CoinGecko support multiple IDs: bitcoin,ethereum,solana
        $cryptoAssets = $assets->where('type', 'Crypto');
        
        if ($cryptoAssets->count() > 0) {
            $ids = $cryptoAssets->pluck('api_id')->implode(',');
            
            // Gunakan endpoint 'markets' karena menyediakan Image + Price
            $url = "https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&ids={$ids}&order=market_cap_desc&per_page=250&page=1&sparkline=false";
            
            $response = Http::get($url);
            
            if ($response->successful()) {
                $data = $response->json();
                
                foreach ($data as $coin) {
                    // Cari aset di DB berdasarkan API ID
                    $asset = $cryptoAssets->where('api_id', $coin['id'])->first();
                    
                    if ($asset) {
                        // Update Harga & Logo Otomatis
                        $asset->update([
                            'current_price' => $coin['current_price'],
                            'logo' => $coin['image'], // <--- SIMPAN LOGO
                            'updated_at' => now()
                        ]);
                        $successCount++;
                    }
                }
            }
        }

        // 2. LOOPING UNTUK SAHAM (Yahoo Finance - Harga Saja)
        // Logo saham biasanya statis, jadi lebih baik input manual URL-nya
        $stockAssets = $assets->where('type', '!=', 'Crypto');

        foreach ($stockAssets as $asset) {
            try {
                $symbol = $asset->api_id;
                // Auto add .JK for Indo Stocks
                if ($asset->type == 'Stock' && !str_contains($symbol, '.') && strlen($symbol) == 4) {
                    $symbol .= '.JK'; 
                }

                $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}";
                $response = Http::get($url);

                if ($response->successful()) {
                    $meta = $response->json()['chart']['result'][0]['meta'] ?? null;
                    if ($meta && isset($meta['regularMarketPrice'])) {
                        $asset->update([
                            'current_price' => $meta['regularMarketPrice'],
                            'updated_at' => now()
                        ]);
                        $successCount++;
                    } else {
                        $failCount++;
                    }
                } else {
                    $failCount++;
                }
            } catch (\Exception $e) {
                $failCount++;
            }
        }

        return back()->with('success', "Sync Selesai! Berhasil: $successCount, Gagal: $failCount. Logo Crypto berhasil diperbarui.");

    } catch (\Exception $e) {
        return back()->with('error', 'Error: ' . $e->getMessage());
    }
}
}