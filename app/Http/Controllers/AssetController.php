<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class AssetController extends Controller
{
    /**
     * TAMPILKAN SEMUA ASET (Admin)
     */
    public function index()
    {
        // This returns a Paginator, which HAS the ->links() method
        $assets = Asset::orderBy('type', 'asc')
                    ->orderBy('symbol', 'asc')
                    ->paginate(10); // Change 10 to however many items you want per page

        return view('admin.assets.index', compact('assets'));
    }

    /**
     * TAMPILKAN FORM TAMBAH ASET
     */
    public function create()
    {
        return view('admin.assets.create');
    }

    /**
     * SIMPAN ASET BARU
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'symbol'        => ['required', 'unique:assets,symbol', 'uppercase', 'max:10'],
            'name'          => ['required', 'string', 'max:255'],
            'type'          => ['required', 'in:stock,us_stock,crypto'],
            'current_price' => ['required', 'numeric', 'min:0'],
            'api_id'        => ['nullable', 'string', 'max:100'],
        ]);

        Asset::create($validated);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Aset berhasil ditambahkan!');
    }

    /**
     * TAMPILKAN FORM EDIT ASET
     * Menggunakan Route Model Binding (Asset $asset) menggantikan $id
     */
    public function edit(Asset $asset)
    {
        return view('admin.assets.edit', compact('asset'));
    }

    /**
     * UPDATE ASET
     */
    public function update(Request $request, Asset $asset)
    {
        $validated = $request->validate([
            'symbol' => [
                'required',
                'uppercase',
                'max:10',
                Rule::unique('assets')->ignore($asset->id), // Syntax modern untuk unique ignore
            ],
            'name'          => ['required', 'string', 'max:255'],
            'type'          => ['required', 'in:stock,us_stock,crypto'],
            'current_price' => ['required', 'numeric', 'min:0'],
            'api_id'        => ['nullable', 'string', 'max:100'],
        ]);

        $asset->update($validated);

        return redirect()->route('admin.assets.index')
            ->with('success', 'Aset berhasil diperbarui!');
    }

    /**
     * UPDATE HARGA SAJA
     */
    public function updatePrice(Request $request, Asset $asset)
    {
        $request->validate([
            'current_price' => 'required|numeric|min:0'
        ]);

        $asset->update(['current_price' => $request->current_price]);

        return redirect()->back()
            ->with('success', 'Harga aset berhasil diperbarui!');
    }

    /**
     * HAPUS ASET
     */
    public function destroy(Asset $asset)
    {
        $asset->delete();

        return redirect()->route('admin.assets.index')
            ->with('success', 'Aset berhasil dihapus!');
    }

    /**
     * SINCRONISASI HARGA CRYPTO DARI COINGECKO
     */
    public function syncCryptoPrices()
    {
        $assets = Asset::where('type', 'crypto')
            ->whereNotNull('api_id')
            ->get();

        if ($assets->isEmpty()) {
            return back()->with('error', 'Tidak ada aset crypto dengan API ID!');
        }

        // Ambil semua ID untuk mengurangi request API (Batch request)
        $ids = $assets->pluck('api_id')->join(',');

        try {
            $response = Http::get("https://api.coingecko.com/api/v3/simple/price", [
                'ids'           => $ids,
                'vs_currencies' => 'usd'
            ]);

            if ($response->failed()) {
                return back()->with('error', 'Gagal terhubung ke CoinGecko API!');
            }

            $data = $response->json();
            $updatedCount = 0;

            foreach ($assets as $asset) {
                // Cek apakah data tersedia di response JSON
                if (isset($data[$asset->api_id]['usd'])) {
                    $asset->update([
                        'current_price' => $data[$asset->api_id]['usd']
                    ]);
                    $updatedCount++;
                }
            }

            return back()->with('success', "Berhasil sinkronisasi {$updatedCount} aset crypto!");

        } catch (\Exception $e) {
            Log::error("CoinGecko Error: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem saat sinkronisasi.');
        }
    }

    /**
     * SINCRONISASI SEMUA HARGA DARI YAHOO FINANCE
     */
    public function syncAllPrices()
    {
        $this->syncCryptoPrices();
        $this->syncAllPrices();
        // Tips: Untuk production, sebaiknya gunakan Job/Queue agar tidak timeout
        $assets = Asset::all();
        $updated = 0;

        foreach ($assets as $asset) {
            try {
                // Tentukan simbol yang sesuai format Yahoo Finance
                $symbol = ($asset->type === 'crypto') ? $asset->symbol . '-USD' : $asset->symbol;
                
                $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}?interval=1d&range=1d";
                
                // withoutVerifying() digunakan jika sertifikat SSL yahoo bermasalah di lokal server
                $response = Http::withoutVerifying()->get($url);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Validasi struktur JSON Yahoo Finance yang dalam
                    $result = $data['chart']['result'][0] ?? null;
                    
                    if ($result && isset($result['meta']['regularMarketPrice'])) {
                        $price = $result['meta']['regularMarketPrice'];
                        
                        if ($price > 0) {
                            $asset->update(['current_price' => $price]);
                            $updated++;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Lanjutkan loop meskipun satu aset gagal, tapi catat errornya
                Log::warning("Yahoo Finance Error for {$asset->symbol}: " . $e->getMessage());
                continue;
            }
        }

        return back()->with('success', 'Semua harga aset berhasil diperbarui!');
    }
}