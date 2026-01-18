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

    public function store(Request $request)
    {
        $request->validate([
            'symbol' => 'required|unique:assets,symbol',
            'name' => 'required',
            'type' => 'required',
            'current_price' => 'required|numeric'
        ]);

        Asset::create([
            'symbol' => strtoupper($request->symbol),
            'name' => $request->name,
            'type' => $request->type,
            'current_price' => $request->current_price,
            'api_id' => $request->api_id, // Opsional
        ]);

        return back()->with('success', 'Aset berhasil ditambahkan.');
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
    
    // Fitur Sync dummy (kedepannya bisa pakai API beneran)
    public function sync()
    {
        return back()->with('success', 'Simulasi Sync harga live berhasil!');
    }

    // 1. UPDATE KURS MANUAL (Suka-suka Admin)
    public function updateRate(Request $request)
    {
        $request->validate([
            'rate' => 'required|numeric|min:1'
        ]);

        // Simpan ke history database
        ExchangeRate::create([
            'from_currency' => 'USD',
            'to_currency' => 'IDR',
            'rate' => $request->rate,
            'date' => now(),
        ]);

        return back()->with('success', 'Kurs USD berhasil diupdate manual menjadi Rp ' . number_format($request->rate));
    }

    // 2. UPDATE KURS OTOMATIS (Pakai API)
    public function syncRateAPI()
    {
        try {
            // Kita pakai API publik gratis (exchangerate-api.com)
            $response = Http::get('https://api.exchangerate-api.com/v4/latest/USD');
            
            if ($response->successful()) {
                $data = $response->json();
                $liveRate = $data['rates']['IDR']; // Ambil angka IDR

                // Simpan ke database
                ExchangeRate::create([
                    'from_currency' => 'USD',
                    'to_currency' => 'IDR',
                    'rate' => $liveRate,
                    'date' => now(),
                ]);

                return back()->with('success', 'Kurs berhasil disinkronkan dengan pasar global: Rp ' . number_format($liveRate));
            } else {
                return back()->with('error', 'Gagal mengambil data dari API.');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan koneksi internet.');
        }
    }
}