<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class MarketService
{
    /**
     * Ambil Data IHSG (Indonesia)
     */
    public function getIHSG()
    {
        // Cache dipindah ke sini agar Controller tidak perlu tahu soal caching
        return Cache::remember('ihsg_data', 600, function () {
            return $this->fetchYahooData('^JKSE');
        });
    }

    /**
     * Ambil Data US Market (S&P 500)
     */
    public function getUSMarket()
    {
        return Cache::remember('sp500_data', 600, function () {
            return $this->fetchYahooData('^GSPC');
        });
    }

    /**
     * Ambil Data Crypto (Bitcoin)
     */
    public function getCrypto()
    {
        return Cache::remember('btc_data', 300, function () {
            return $this->fetchYahooData('BTC-USD');
        });
    }

    /**
     * LOGIKA PRIVATE: MENGAMBIL DATA DARI YAHOO
     * (Hanya bisa diakses oleh class ini sendiri)
     */
    private function fetchYahooData($symbol)
    {
        try {
            $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}?interval=1d&range=1mo";

            // Gunakan withoutVerifying() untuk menghindari masalah SSL di Localhost
            $response = Http::withoutVerifying()->get($url);

            if ($response->failed() || empty($response->json()['chart']['result'])) {
                return $this->emptyData();
            }

            $data = $response->json()['chart']['result'][0];
            $meta = $data['meta'];
            $quotes = $data['indicators']['quote'][0];

            $prices = $quotes['close'];
            
            // Bersihkan nilai null
            $cleanPrices = [];
            foreach ($prices as $p) {
                if ($p !== null) $cleanPrices[] = $p;
            }

            $currentPrice = end($cleanPrices);
            $prevClose = $meta['chartPreviousClose'];
            $change = (($currentPrice - $prevClose) / $prevClose) * 100;

            return [
                'price' => $currentPrice,
                'change_percent' => $change,
                'history' => $cleanPrices
            ];

        } catch (\Exception $e) {
            return $this->emptyData();
        }
    }

    /**
     * Data default jika error/offline
     */
    private function emptyData()
    {
        return ['price' => 0, 'change_percent' => 0, 'history' => []];
    }
}