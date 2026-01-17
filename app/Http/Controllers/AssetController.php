<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;
use Illuminate\Support\Facades\Http;

class AssetController extends Controller
{
    // 1. Tampilkan Daftar Aset
    public function index()
    {
        $assets = Asset::all();
        return view('admin.assets.index', compact('assets'));
    }

    // 2. Simpan Aset Baru
    // UPDATE METHOD STORE (Tambah api_id)
    public function store(Request $request)
    {
        $request->validate([
            'symbol' => 'required|unique:assets,symbol|uppercase|max:10',
            'name' => 'required|string|max:255',
            'type' => 'required|in:stock,crypto',
            'current_price' => 'required|numeric',
            'api_id' => 'nullable|string', // 👈 Validasi baru
        ]);

        Asset::create($request->all());

        return redirect()->back()->with('success', 'Aset baru berhasil ditambahkan!');
    }

    // 3. Update Harga Aset
    public function updatePrice(Request $request, $id)
    {
        $request->validate([
            'current_price' => 'required|numeric',
        ]);

        $asset = Asset::findOrFail($id);
        $asset->update(['current_price' => $request->current_price]);

        return redirect()->back()->with('success', 'Harga berhasil diupdate!');
    }

    // 👇👇 FITUR BARU: SYNC HARGA DARI COINGECKO 👇👇
    public function syncToApi()
    {
        // 1. Ambil semua aset yang punya api_id (Khusus Crypto biasanya)
        $assets = Asset::whereNotNull('api_id')->get();

        if ($assets->isEmpty()) {
            return back()->with('error', 'Belum ada aset dengan API ID!');
        }

        // 2. Kumpulkan ID-nya (contoh: "bitcoin,ethereum,dogecoin")
        $ids = $assets->pluck('api_id')->join(',');

        // 3. Tembak API CoinGecko (Gratis, tanpa API Key)
        // Kita minta harga dalam IDR
        $response = Http::get("https://api.coingecko.com/api/v3/simple/price", [
            'ids' => $ids,
            'vs_currencies' => 'idr'
        ]);

        if ($response->failed()) {
            return back()->with('error', 'Gagal menghubungi Server CoinGecko!');
        }

        $data = $response->json();

        // 4. Update Database Kita
        foreach ($assets as $asset) {
            $apiId = $asset->api_id; // misal: bitcoin
            
            // Cek apakah data bitcoin ada di response?
            if (isset($data[$apiId]['idr'])) {
                $newPrice = $data[$apiId]['idr'];
                
                $asset->update(['current_price' => $newPrice]);
            }
        }

        return back()->with('success', 'Sukses! Harga aset berhasil di-update dari Pasar Global.');
    }

    // 4. Hapus Aset
    public function destroy($id)
    {
        Asset::destroy($id);
        return redirect()->back()->with('success', 'Aset dihapus!');
    }
}