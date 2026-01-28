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

            // 1. Format Chart Data untuk ApexCharts [{x: timestamp, y: price}]
            $chartData = [];
            $cleanPrices = [];

            for ($i = 0; $i < count($timestamps); $i++) {
                if (isset($prices[$i])) {
                    $chartData[] = [
                        'x' => $timestamps[$i] * 1000, // Konversi ke milidetik untuk JS
                        'y' => round($prices[$i], 2)
                    ];
                    $cleanPrices[] = $prices[$i];
                }
            }

            // 2. Hitung Harga & Perubahan
            $currentPrice = end($cleanPrices) ?: ($meta['regularMarketPrice'] ?? 0);
            $prevClose = $meta['chartPreviousClose'] ?? ($cleanPrices[0] ?? 0);
            
            $change = 0;
            if ($prevClose > 0) {
                $change = (($currentPrice - $prevClose) / $prevClose) * 100;
            }

            return [
                'price' => $currentPrice,
                'change_percent' => $change,
                'chart_data' => $chartData, // INI YANG PENTING AGAR GRAFIK MUNCUL
                'logo' => null,
                'success' => true
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
        return ['price' => 0, 'change_percent' => 0, 'chart_data' => [], 'success' => false];
    }
}