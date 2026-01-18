<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http;

class AdminExchangeRateController extends Controller
{
    // Tampilkan Daftar Valas Dunia
    public function index()
    {
        // Ambil data kurs TERBARU untuk setiap mata uang unik
        // Query ini mengelompokkan berdasarkan 'from_currency' dan mengambil data tanggal terbaru
        $rates = ExchangeRate::select('from_currency', 'rate', 'updated_at')
                    ->whereIn('id', function($query) {
                        $query->selectRaw('MAX(id)')
                              ->from('exchange_rates')
                              ->groupBy('from_currency');
                    })
                    ->orderBy('from_currency')
                    ->get();

        return view('admin.exchange_rates.index', compact('rates'));
    }

    // Tambah/Update Kurs Manual
    public function store(Request $request)
    {
        $request->validate([
            'currency_code' => 'required|string|size:3', // Contoh: JPY, SGD
            'rate' => 'required|numeric|min:1'
        ]);

        $code = strtoupper($request->currency_code);

        ExchangeRate::create([
            'from_currency' => $code,
            'to_currency' => 'IDR',
            'rate' => $request->rate,
            'date' => now(),
        ]);

        return back()->with('success', "Kurs 1 $code berhasil diupdate menjadi Rp " . number_format($request->rate));
    }

    // Hapus Mata Uang (Hapus historynya juga biar bersih)
    public function destroy($currency)
    {
        ExchangeRate::where('from_currency', $currency)->delete();
        return back()->with('success', "Mata uang $currency berhasil dihapus dari sistem.");
    }

    // Sync Otomatis Semua Mata Uang
    public function syncApi()
    {
        try {
            // Kita pakai API yang base-nya IDR, lalu kita balik nilainya
            // Atau pakai API open.er-api.com yang gratis dan support banyak
            $response = Http::get('https://open.er-api.com/v6/latest/IDR');
            
            if ($response->successful()) {
                $data = $response->json();
                $rates = $data['rates'];

                // Daftar Mata Uang yang ADA di database kita saja yang diupdate
                // Supaya database tidak penuh sampah mata uang aneh
                $trackedCurrencies = ExchangeRate::select('from_currency')->distinct()->pluck('from_currency');

                foreach ($trackedCurrencies as $currency) {
                    if (isset($rates[$currency])) {
                        // Rumus: Jika 1 IDR = 0.000064 USD, maka 1 USD = 1 / 0.000064 IDR
                        $rateInIDR = 1 / $rates[$currency];

                        ExchangeRate::create([
                            'from_currency' => $currency,
                            'to_currency' => 'IDR',
                            'rate' => $rateInIDR,
                            'date' => now(),
                        ]);
                    }
                }

                return back()->with('success', 'Semua mata uang yang terdaftar berhasil disinkronkan dengan pasar global!');
            }
            
            return back()->with('error', 'Gagal terhubung ke API Valas.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
}