<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http;

class AdminAssetController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'Stock'); // Default ke Stock
        
        $query = Asset::where('type', $type);

        // Fitur Search (Simbol atau Nama)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('symbol', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Urutkan dan Paginate
        $assets = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        return view('admin.assets.index', compact('assets', 'type'));
    }

    public function create(Request $request)
    {
        $type = $request->get('type', 'Stock');
        return view('admin.assets.create', compact('type'));
    }

    public function store(Request $request)
    {
        // Validasi dasar
        $request->validate([
            'symbol' => 'required',
            'name' => 'required',
            'type' => 'required',
            'current_price' => 'required|numeric|min:0',
            'price_updated_at' => 'required|date',
        ]);

        // Validasi khusus untuk Mutual Fund
        if ($request->type === 'Mutual Fund') {
            $request->validate([
                'mutual_fund_type' => 'required',
                'investment_manager' => 'required',
                'management_fee' => 'nullable|numeric|min:0|max:100',
                'minimum_purchase' => 'nullable|numeric|min:0',
                'risk_level' => 'nullable|in:Rendah,Sedang,Tinggi,Sangat Tinggi',
                'category' => 'nullable|in:Konvensional,Syariah',
            ]);
        }

        // Validasi khusus untuk US Stock
        if ($request->type === 'US Stock') {
            $request->validate([
                'sector' => 'nullable|string',
                'exchange' => 'nullable|in:NASDAQ,NYSE,NYSE American,OTC',
                'market_cap' => 'nullable|numeric|min:0',
                'country' => 'nullable|string',
                'ceo' => 'nullable|string',
            ]);
        }

        // Siapkan data untuk disimpan
        $data = [
            'symbol' => $request->type === 'US Stock' ? strtoupper($request->symbol) : $request->symbol,
            'name' => $request->name,
            'type' => $request->type,
            'current_price' => $request->current_price,
            'price_updated_at' => $request->price_updated_at,
            'logo_url' => $request->logo_url,
            'notes' => $request->notes,
        ];

        // Tambahkan field khusus untuk Mutual Fund
        if ($request->type === 'Mutual Fund') {
            $data['mutual_fund_type'] = $request->mutual_fund_type;
            $data['investment_manager'] = $request->investment_manager;
            $data['management_fee'] = $request->management_fee;
            $data['minimum_purchase'] = $request->minimum_purchase;
            $data['risk_level'] = $request->risk_level;
            $data['manager_website'] = $request->manager_website;
            $data['launch_date'] = $request->launch_date;
            $data['category'] = $request->category;
        }

        // Tambahkan field khusus untuk US Stock
        if ($request->type === 'US Stock') {
            $data['sector'] = $request->sector;
            $data['exchange'] = $request->exchange;
            $data['market_cap'] = $request->market_cap;
            $data['country'] = $request->country;
            $data['company_website'] = $request->company_website;
            $data['ceo'] = $request->ceo;
        }

        // Simpan ke database
        Asset::create($data);

        return redirect()->route('admin.assets.index', ['type' => $request->type])
            ->with('success', 'Data ' . $request->type . ' berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $asset = Asset::findOrFail($id);
        $type = $asset->type;
        return view('admin.assets.edit', compact('asset', 'type'));
    }

    public function update(Request $request, $id)
    {
        $asset = Asset::findOrFail($id);
        
        // Validasi dasar
        $request->validate([
            'symbol' => 'required|unique:assets,symbol,' . $id,
            'name' => 'required',
            'current_price' => 'required|numeric|min:0',
            'price_updated_at' => 'required|date',
        ]);

        // Validasi khusus untuk Mutual Fund
        if ($asset->type === 'Mutual Fund') {
            $request->validate([
                'mutual_fund_type' => 'required',
                'investment_manager' => 'required',
                'management_fee' => 'nullable|numeric|min:0|max:100',
                'minimum_purchase' => 'nullable|numeric|min:0',
                'risk_level' => 'nullable|in:Rendah,Sedang,Tinggi,Sangat Tinggi',
                'category' => 'nullable|in:Konvensional,Syariah',
            ]);
        }

        // Validasi khusus untuk US Stock
        if ($asset->type === 'US Stock') {
            $request->validate([
                'sector' => 'nullable|string',
                'exchange' => 'nullable|in:NASDAQ,NYSE,NYSE American,OTC',
                'market_cap' => 'nullable|numeric|min:0',
                'country' => 'nullable|string',
                'ceo' => 'nullable|string',
            ]);
        }

        // Update data
        $asset->symbol = $asset->type === 'US Stock' ? strtoupper($request->symbol) : $request->symbol;
        $asset->name = $request->name;
        $asset->current_price = $request->current_price;
        $asset->price_updated_at = $request->price_updated_at;
        $asset->logo_url = $request->logo_url;
        $asset->notes = $request->notes;

        // Update field khusus Mutual Fund
        if ($asset->type === 'Mutual Fund') {
            $asset->mutual_fund_type = $request->mutual_fund_type;
            $asset->investment_manager = $request->investment_manager;
            $asset->management_fee = $request->management_fee;
            $asset->minimum_purchase = $request->minimum_purchase;
            $asset->risk_level = $request->risk_level;
            $asset->manager_website = $request->manager_website;
            $asset->launch_date = $request->launch_date;
            $asset->category = $request->category;
        }

        // Update field khusus US Stock
        if ($asset->type === 'US Stock') {
            $asset->sector = $request->sector;
            $asset->exchange = $request->exchange;
            $asset->market_cap = $request->market_cap;
            $asset->country = $request->country;
            $asset->company_website = $request->company_website;
            $asset->ceo = $request->ceo;
        }

        $asset->save();

        return redirect()->route('admin.assets.index', ['type' => $asset->type])
            ->with('success', 'Data ' . $asset->type . ' berhasil diperbarui!');
    }

    public function updatePrice(Request $request, $id)
    {
        $request->validate([
            'current_price' => 'required|numeric|min:0',
            'price_updated_at' => 'required|date',
        ]);

        $asset = Asset::findOrFail($id);
        $asset->update([
            'current_price' => $request->current_price,
            'price_updated_at' => $request->price_updated_at,
        ]);

        return back()->with('success', 'Harga ' . $asset->symbol . ' berhasil diupdate!');
    }

    public function destroy($id)
    {
        $asset = Asset::findOrFail($id);
        $type = $asset->type;
        $asset->delete();

        return redirect()->route('admin.assets.index', ['type' => $type])
            ->with('success', 'Data ' . $type . ' berhasil dihapus.');
    }

    public function updateRate(Request $request)
    {
        $request->validate([
            'rate' => 'required|numeric|min:1'
        ]);

        ExchangeRate::create([
            'from_currency' => 'USD',
            'to_currency' => 'IDR',
            'rate' => $request->rate,
            'date' => now(),
        ]);

        return back()->with('success', 'Kurs USD berhasil diupdate manual menjadi Rp ' . number_format($request->rate));
    }

    public function syncPrices()
    {
        try {
            $assets = Asset::whereNotNull('api_id')->where('api_id', '!=', '')->get();
            
            if ($assets->isEmpty()) {
                return back()->with('error', 'Tidak ada aset dengan API ID.');
            }

            $successCount = 0;
            $failCount = 0;

            // Sync untuk Crypto
            $cryptoAssets = $assets->where('type', 'Crypto');
            
            if ($cryptoAssets->count() > 0) {
                $ids = $cryptoAssets->pluck('api_id')->implode(',');
                
                $url = "https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd&ids={$ids}&order=market_cap_desc&per_page=250&page=1&sparkline=false";
                
                $response = Http::get($url);
                
                if ($response->successful()) {
                    $data = $response->json();
                    
                    foreach ($data as $coin) {
                        $asset = $cryptoAssets->where('api_id', $coin['id'])->first();
                        
                        if ($asset) {
                            $asset->update([
                                'current_price' => $coin['current_price'],
                                'logo_url' => $coin['image'],
                                'price_updated_at' => now(),
                            ]);
                            $successCount++;
                        }
                    }
                }
            }

            // Sync untuk Saham
            $stockAssets = $assets->where('type', '!=', 'Crypto');

            foreach ($stockAssets as $asset) {
                try {
                    $symbol = $asset->api_id;
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
                                'price_updated_at' => now(),
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

            return back()->with('success', "Sync Selesai! Berhasil: $successCount, Gagal: $failCount");

        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}