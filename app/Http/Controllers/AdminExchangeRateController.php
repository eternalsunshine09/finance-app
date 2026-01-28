<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate; // Pastikan model ini ada
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http; // Untuk Sync API

class AdminExchangeRateController extends Controller
{
    // Menampilkan halaman kelola kurs
    public function index()
    {
        $rates = ExchangeRate::orderBy('from_currency', 'asc')->get();
        return view('admin.exchange_rates.index', compact('rates'));
    }

    // Menambah/Update kurs manual
    public function store(Request $request)
    {
        $request->validate([
            'currency_code' => 'required|string|size:3|uppercase',
            'rate' => 'required|numeric|min:0'
        ]);

        ExchangeRate::updateOrCreate(
            ['from_currency' => $request->currency_code, 'to_currency' => 'IDR'],
            ['rate' => $request->rate]
        );

        return back()->with('success', 'Kurs ' . $request->currency_code . ' berhasil disimpan.');
    }

    // Menghapus kurs
    public function destroy($currency)
    {
        ExchangeRate::where('from_currency', $currency)->delete();
        return back()->with('success', 'Mata uang berhasil dihapus.');
    }

    // Sync API Otomatis (Contoh menggunakan ExchangeRate-API gratisan)
    public function syncApi()
    {
        try {
            // Contoh URL API (Bisa diganti dengan API berbayar/gratis lain)
            // https://api.exchangerate-api.com/v4/latest/USD
            $response = Http::get('https://api.exchangerate-api.com/v4/latest/USD');
            
            if ($response->successful()) {
                $data = $response->json();
                $usdToIdr = $data['rates']['IDR'] ?? 16000;

                // Update USD
                ExchangeRate::updateOrCreate(
                    ['from_currency' => 'USD', 'to_currency' => 'IDR'],
                    ['rate' => $usdToIdr]
                );

                // Update mata uang lain jika perlu (EUR, SGD, JPY)
                // Logic: Rate Mata Uang X ke IDR = (1 / Rate USD ke X) * Rate USD ke IDR
                // ... (implementasi logika konversi) ...

                return back()->with('success', 'Sinkronisasi berhasil! Rate USD saat ini: Rp ' . number_format($usdToIdr));
            }
            
            return back()->with('error', 'Gagal mengambil data dari API.');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}