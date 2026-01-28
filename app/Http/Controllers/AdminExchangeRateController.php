<?php

namespace App\Http\Controllers;

use App\Models\ExchangeRate;
use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AdminExchangeRateController extends Controller
{
    public function index()
    {
        $rates = ExchangeRate::orderBy('from_currency', 'asc')->get();
        return view('admin.exchange_rates.index', compact('rates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'currency_code' => 'required|string|size:3|uppercase',
            'rate' => 'required|numeric|min:0'
        ]);

        $this->updateCurrencyData($request->currency_code, $request->rate);

        return back()->with('success', 'Kurs ' . $request->currency_code . ' berhasil disimpan.');
    }

    public function destroy($currency)
    {
        ExchangeRate::where('from_currency', $currency)->delete();
        // Opsional: Hapus aset juga jika diinginkan
        // Asset::where('symbol', $currency)->where('type', 'Currency')->delete();
        
        return back()->with('success', 'Mata uang berhasil dihapus.');
    }

    // =====================================================================
    // 🔥 SYNC API: UPDATE SEMUA MATA UANG (CROSS RATE) 🔥
    // =====================================================================
    public function syncApi()
    {
        try {
            // 1. Ambil Data API (Base USD)
            // Kita gunakan Frankfurter (Data Bank Sentral Eropa) karena gratis & stabil
            $response = Http::withoutVerifying()->get('https://api.frankfurter.app/latest?from=USD');
            
            // Backup jika Frankfurter down, pakai ExchangeRate-API
            if ($response->failed()) {
                $response = Http::withoutVerifying()->get('https://api.exchangerate-api.com/v4/latest/USD');
            }

            if ($response->successful()) {
                $data = $response->json();
                $rates = $data['rates'] ?? [];
                
                // Pastikan ada data IDR
                if (!isset($rates['IDR'])) {
                    return back()->with('error', 'Data IDR tidak ditemukan di API.');
                }

                $usdToIdr = $rates['IDR']; // Contoh: 16000
                $updatedCount = 0;

                // 2. Ambil SEMUA mata uang yang ada di Database kita
                // Kita hanya update yang sudah didaftarkan admin agar tidak menuh-menuhin database
                $myCurrencies = ExchangeRate::pluck('from_currency')->toArray();

                // Jika list kosong (belum ada data sama sekali), kita inisiatif tambahkan USD
                if (empty($myCurrencies)) {
                    $myCurrencies = ['USD'];
                }

                foreach ($myCurrencies as $code) {
                    $code = strtoupper($code);
                    $newRate = 0;

                    if ($code === 'USD') {
                        // Jika USD, langsung pakai harga IDR
                        $newRate = $usdToIdr;
                    } 
                    elseif (isset($rates[$code]) && $rates[$code] > 0) {
                        // Jika mata uang lain (misal JPY), hitung Cross Rate
                        // Rumus: Rate IDR / Rate JPY = Harga 1 JPY dalam Rupiah
                        // Contoh: 16000 / 150 = 106.66
                        $rateInUsd = $rates[$code];
                        $newRate = $usdToIdr / $rateInUsd;
                    }

                    // 3. Simpan ke Database jika rate valid
                    if ($newRate > 0) {
                        $this->updateCurrencyData($code, $newRate);
                        $updatedCount++;
                    }
                }

                return back()->with('success', "Berhasil sinkronisasi {$updatedCount} mata uang (Base USD: Rp " . number_format($usdToIdr) . ")");
            }
            
            return back()->with('error', 'Gagal terhubung ke server API.');

        } catch (\Exception $e) {
            Log::error("Sync API Error: " . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * Helper Function: Update tabel ExchangeRate DAN Asset sekaligus
     */
    private function updateCurrencyData($symbol, $rate)
    {
        $symbol = strtoupper($symbol);

        // 1. Update Tabel Kurs
        ExchangeRate::updateOrCreate(
            ['from_currency' => $symbol, 'to_currency' => 'IDR'],
            [
                'rate' => $rate,
                'date' => now() // Timestamp update
            ]
        );

        // 2. Update Tabel Asset (Agar bisa masuk Portfolio)
        Asset::updateOrCreate(
            ['symbol' => $symbol],
            [
                'name' => 'Mata Uang ' . $symbol,
                'type' => 'Currency',
                'current_price' => $rate,
                'logo' => "https://flagcdn.com/w80/" . strtolower(substr($symbol, 0, 2)) . ".png",
                'change_percent' => 0
            ]
        );
    }
}