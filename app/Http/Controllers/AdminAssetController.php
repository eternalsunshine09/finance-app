<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http;

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
            'symbol' => 'required|unique:assets,symbol',
            'name' => 'required',
            'type' => 'required',
            'current_price' => 'required|numeric',
            // Subtype opsional, tapi kalau mau strict bisa dikasih logic tambahan
        ]);

        Asset::create([
            'symbol' => strtoupper($request->symbol),
            'name' => $request->name,
            'type' => $request->type,
            'subtype' => $request->subtype, // <--- Simpan subtype
            'api_id' => $request->api_id,
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
    
    // 1. UPDATE KURS MANUAL
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

    // 2. SYNC HARGA ASET (Crypto -> USD, Saham -> IDR)
    // GANTI method sync() dengan kode ini:
    public function sync()
    {
        try {
            // Ambil semua aset yang punya API ID
            $assets = Asset::whereNotNull('api_id')->where('api_id', '!=', '')->get();
            $updatedCount = 0;

            foreach ($assets as $asset) {
                // 🔥 LOGIKA PINTAR:
                // Jika tipe Crypto -> Ambil harga USD
                // Jika tipe Stock  -> Ambil harga IDR
                $currency = ($asset->type == 'Crypto') ? 'usd' : 'idr';

                // Panggil API CoinGecko sesuai mata uang
                $url = "https://api.coingecko.com/api/v3/simple/price?ids={$asset->api_id}&vs_currencies={$currency}";
                $response = Http::get($url);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Cek apakah data ada
                    if (isset($data[$asset->api_id][$currency])) {
                        $price = $data[$asset->api_id][$currency];

                        // Simpan ke database
                        // Bitcoin akan tersimpan misal: 96000 (bukan 1,6 Miliar lagi)
                        $asset->update(['current_price' => $price]);
                        $updatedCount++;
                    }
                }
            }

            return back()->with('success', "Berhasil update harga $updatedCount aset! (Crypto dalam USD, Saham dalam IDR)");

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal koneksi API: ' . $e->getMessage());
        }
    }

    // 2. UPDATE KURS OTOMATIS (Pakai API)
    public function syncRateAPI()
    {
        try {
            $assets = Asset::whereNotNull('api_id')->get(); // Ambil aset yang punya API ID saja
            $updatedCount = 0;

            foreach ($assets as $asset) {
                // 🔥 LOGIKA MATA UANG:
                // Jika Crypto => Request harga dalam 'usd'
                // Jika Stock/Lainnya => Request harga dalam 'idr'
                $currency = ($asset->type == 'Crypto') ? 'usd' : 'idr';

                // Panggil API CoinGecko
                $url = "https://api.coingecko.com/api/v3/simple/price?ids={$asset->api_id}&vs_currencies={$currency}";
                $response = Http::get($url);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    // Cek apakah data tersedia di response
                    if (isset($data[$asset->api_id][$currency])) {
                        $price = $data[$asset->api_id][$currency];

                        // Update harga di database
                        $asset->update(['current_price' => $price]);
                        $updatedCount++;
                    }
                }
            }

            return back()->with('success', "Berhasil sinkronisasi harga $updatedCount aset (Crypto: USD, Saham: IDR)!");

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal koneksi ke API CoinGecko: ' . $e->getMessage());
        }
    }
}