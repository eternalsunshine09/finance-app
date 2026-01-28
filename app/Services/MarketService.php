<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class MarketService
{
    /**
     * Ambil Data IHSG dengan Caching & Timeframe
     */
    public function getIHSG($timeframe = '1mo')
    {
        return Cache::remember("ihsg_data_{$timeframe}", 600, function () use ($timeframe) {
            return $this->fetchYahooData('^JKSE', $timeframe);
        });
    }

    /**
     * Ambil Data US Market (S&P 500)
     */
    public function getUSMarket($timeframe = '1mo')
    {
        return Cache::remember("sp500_data_{$timeframe}", 600, function () use ($timeframe) {
            return $this->fetchYahooData('^GSPC', $timeframe);
        });
    }

    /**
     * Ambil Data Crypto (Bitcoin)
     */
    public function getCrypto($timeframe = '1mo')
    {
        return Cache::remember("btc_data_{$timeframe}", 300, function () use ($timeframe) {
            return $this->fetchYahooData('BTC-USD', $timeframe);
        });
    }

    public function getGoldPriceIdr($timeframe = '1mo', $forceRefresh = false)
    {
        $cacheKey = "gold_idr_{$timeframe}";

        // 🔥 JIKA FORCE REFRESH, HAPUS CACHE LAMA 🔥
        if ($forceRefresh) {
            Cache::forget($cacheKey);
        }

        return Cache::remember($cacheKey, 300, function () use ($timeframe) {
            // ... (Kode logika di bawah ini tetap sama seperti sebelumnya) ...
            
            // 1. Ambil Harga Emas Dunia (USD/Troy Ounce)
            $goldData = $this->fetchYahooData('GC=F', $timeframe);
            
            // 2. Ambil Kurs USD ke IDR
            $kursData = $this->fetchYahooData('IDR=X', '1d');
            $rate = $kursData['price'] ?? 15500; 

            if (!$goldData['success']) {
                return $goldData;
            }

            // 3. Konversi ke IDR per Gram (Harga USD / 31.1035 * Kurs)
            $pricePerGram = ($goldData['price'] / 31.1035) * $rate;
            $changePercent = $goldData['change_percent'];

            // 4. Konversi Chart Data
            $chartIdr = array_map(function ($point) use ($rate) {
                return [
                    'x' => $point['x'],
                    'y' => ($point['y'] / 31.1035) * $rate
                ];
            }, $goldData['chart_data']);

            return [
                'price' => $pricePerGram,
                'change_percent' => $changePercent,
                'chart_data' => $chartIdr,
                'success' => true
            ];
        });
    }

    /**
     * Fetch Data dari CoinGecko (Khusus Crypto - Harga & Logo)
     */
    public function fetchCoinGeckoData($coinId)
    {
        try {
            $coinId = strtolower($coinId);
            $response = Http::get("https://api.coingecko.com/api/v3/coins/markets", [
                'vs_currency' => 'usd',
                'ids' => $coinId,
                'order' => 'market_cap_desc',
                'per_page' => 1,
                'page' => 1,
                'sparkline' => 'false'
            ]);

            if ($response->successful() && !empty($response->json())) {
                $data = $response->json()[0];
                return [
                    'price' => $data['current_price'] ?? 0,
                    'logo'  => $data['image'] ?? null,
                    'success' => true
                ];
            }
        } catch (\Exception $e) {
            Log::error("CoinGecko Error: " . $e->getMessage());
        }
        return ['success' => false, 'price' => 0, 'logo' => null];
    }

    /**
     * PRIVATE: Fetch Data Yahoo Finance (Harga + Grafik)
     */
    public function fetchYahooData($symbol, $timeframe = '1mo')
        {
            try {
                $interval = $this->getInterval($timeframe);
                $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}";

                $response = Http::withoutVerifying()->get($url, [
                    'interval' => $interval,
                    'range' => $timeframe,
                    'includePrePost' => false
                ]);

                if ($response->failed() || empty($response->json()['chart']['result'])) {
                    return $this->emptyData();
                }

                $result = $response->json()['chart']['result'][0];
                $meta = $result['meta'];
                $timestamps = $result['timestamp'] ?? [];
                $quotes = $result['indicators']['quote'][0];
                $prices = $quotes['close'] ?? [];

                // 1. Format Chart Data
                $chartData = [];
                $cleanPrices = [];

                for ($i = 0; $i < count($timestamps); $i++) {
                    if (isset($prices[$i])) {
                        $chartData[] = [
                            'x' => $timestamps[$i] * 1000,
                            'y' => round($prices[$i], 2)
                        ];
                        $cleanPrices[] = $prices[$i];
                    }
                }

                // 2. Data Harga Real-time
                $currentPrice = $meta['regularMarketPrice'] ?? end($cleanPrices);
                $prevClose = $meta['chartPreviousClose'] ?? ($cleanPrices[0] ?? 0);
                
                // 3. Hitung Perubahan
                $change = 0;
                $changePoint = 0;
                if ($prevClose > 0) {
                    $changePoint = $currentPrice - $prevClose;
                    $change = ($changePoint / $prevClose) * 100;
                }

                // 4. Data Statistik Tambahan (High, Low, Vol, dll)
                // Mengambil dari 'meta' jika ada, atau hitung dari array chart jika tidak ada
                return [
                    'price'          => $currentPrice,
                    'change_point'   => $changePoint,
                    'change_percent' => $change,
                    'chart_data'     => $chartData,
                    'open'           => $meta['regularMarketOpen'] ?? 0,
                    'high'           => $meta['regularMarketDayHigh'] ?? max($cleanPrices),
                    'low'            => $meta['regularMarketDayLow'] ?? min($cleanPrices),
                    'volume'         => $meta['regularMarketVolume'] ?? 0,
                    'prev_close'     => $prevClose,
                    'success'        => true
                ];

            } catch (\Exception $e) {
                Log::error("Yahoo Data Error ({$symbol}): " . $e->getMessage());
                return $this->emptyData();
            }
        }

    private function getInterval($timeframe)
    {
        $map = [
            '1d' => '5m', '5d' => '15m', '1mo' => '1d', 
            '3mo' => '1d', '6mo' => '1wk', '1y' => '1wk', '5y' => '1mo'
        ];
        return $map[$timeframe] ?? '1d';
    }

    private function emptyData()
    {
        return [
            'price' => 0, 'change_point' => 0, 'change_percent' => 0, 
            'chart_data' => [], 'open' => 0, 'high' => 0, 'low' => 0, 'volume' => 0, 
            'success' => false
        ];
    }
}