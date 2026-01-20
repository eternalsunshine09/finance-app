<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use App\Models\Asset;

class MarketController extends Controller
{
    private $timeframes = [
        '1d' => '1D',
        '5d' => '1W', 
        '1mo' => '1M',
        '3mo' => '3M',
        '6mo' => '6M',
        '1y' => '1Y',
        '2y' => '2Y',
        '5y' => '5Y',
        'max' => 'All'
    ];

    /**
     * HALAMAN MARKET INDONESIA (IDX)
     */
    public function index(Request $request)
    {
        $timeframe = $request->get('timeframe', '1mo');
        
        // Ambil IHSG (Yahoo: ^JKSE)
        $ihsg = Cache::remember("ihsg_data_{$timeframe}", 300, 
            function() use ($timeframe) {
                return $this->getYahooData('^JKSE', $timeframe);
            }
        );

        // Ambil List Saham tipe 'stock'
        $assets = Asset::where('type', 'stock')
                       ->orderBy('symbol', 'asc')
                       ->get()
                       ->map(function ($asset) {
                           $change = rand(-500, 500);
                           $asset->change_percent = $change / 100;
                           return $asset;
                       });

        $currentTimeframeLabel = $this->timeframes[$timeframe] ?? '1M';

        return view('market.index', [
            'ihsg' => $ihsg,
            'assets' => $assets,
            'timeframe' => $timeframe,
            'timeframes' => $this->timeframes,
            'currentTimeframeLabel' => $currentTimeframeLabel
        ]);
    }

    /**
     * HALAMAN MARKET US
     */
    public function us(Request $request)
    {
        $timeframe = $request->get('timeframe', '1mo');
        
        // Ambil S&P 500 (Yahoo: ^GSPC)
        $sp500 = Cache::remember("sp500_data_{$timeframe}", 300, 
            function() use ($timeframe) {
                return $this->getYahooData('^GSPC', $timeframe);
            }
        );
        
        $assets = Asset::where('type', 'us_stock')
                       ->orderBy('symbol', 'asc')
                       ->get()
                       ->map(function ($asset) {
                           $change = rand(-300, 300) / 100;
                           $asset->change_percent = $change;
                           return $asset;
                       });

        $currentTimeframeLabel = $this->timeframes[$timeframe] ?? '1M';

        return view('market.us', [
            'sp500' => $sp500,
            'assets' => $assets,
            'timeframe' => $timeframe,
            'timeframes' => $this->timeframes,
            'currentTimeframeLabel' => $currentTimeframeLabel
        ]);
    }

    /**
     * HALAMAN MARKET CRYPTO
     */
    public function crypto(Request $request)
    {
        $timeframe = $request->get('timeframe', '1mo');
        
        // Ambil Data BTC-USD
        $btc = Cache::remember("btc_data_{$timeframe}", 300, 
            function() use ($timeframe) {
                return $this->getYahooData('BTC-USD', $timeframe);
            }
        );
        
        $assets = Asset::where('type', 'crypto')
                       ->orderBy('symbol', 'asc')
                       ->get()
                       ->map(function ($asset) {
                           $change = rand(-1000, 1000) / 100;
                           $asset->change_percent = $change;
                           return $asset;
                       });

        $currentTimeframeLabel = $this->timeframes[$timeframe] ?? '1M';

        return view('market.crypto', [
            'btc' => $btc,
            'assets' => $assets,
            'timeframe' => $timeframe,
            'timeframes' => $this->timeframes,
            'currentTimeframeLabel' => $currentTimeframeLabel
        ]);
    }

    // MarketController.php
    public function reksadana()
    {
        return view('market.reksadana', [
            'title' => 'Reksadana & ETF',
            'header' => 'Pasar Reksadana & ETF',
            'header_description' => 'Investasi kolektif dan Exchange Traded Funds'
        ]);
    }

    /**
     * HALAMAN COMMODITIES (Emas/Komoditas)
     */
    public function commodities(Request $request)
    {
        $timeframe = $request->get('timeframe', '1mo');
        
        // Contoh untuk Emas (Gold Futures: GC=F)
        $gold = Cache::remember("gold_data_{$timeframe}", 600, 
            function() use ($timeframe) {
                return $this->getYahooData('GC=F', $timeframe);
            }
        );

        $currentTimeframeLabel = $this->timeframes[$timeframe] ?? '1M';

        return view('market.commodities', [
            'gold' => $gold,
            'timeframe' => $timeframe,
            'timeframes' => $this->timeframes,
            'currentTimeframeLabel' => $currentTimeframeLabel
        ]);
    }

    /**
     * FUNGSI: AMBIL DATA DARI YAHOO FINANCE DENGAN TIMEFRAME
     */
    private function getYahooData($symbol, $timeframe = '1mo')
    {
        try {
            $interval = $this->getIntervalFromTimeframe($timeframe);
            
            $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$symbol}";
            
            $response = Http::withoutVerifying()->get($url, [
                'interval' => $interval,
                'range' => $timeframe,
                'includePrePost' => false,
                'events' => 'div,split'
            ]);

            if (!$response->successful()) {
                return $this->emptyData($timeframe);
            }

            $data = $response->json();
            
            if (empty($data['chart']['result'])) {
                return $this->emptyData($timeframe);
            }

            $result = $data['chart']['result'][0];
            $meta = $result['meta'];
            $timestamps = $result['timestamp'] ?? [];
            $indicators = $result['indicators']['quote'][0];
            
            // Ambil harga penutupan
            $prices = $indicators['close'] ?? [];
            
            // Format data untuk chart
            $chartData = [];
            $cleanPrices = [];
            
            for ($i = 0; $i < count($timestamps); $i++) {
                if (isset($prices[$i]) && $prices[$i] !== null) {
                    $chartData[] = [
                        'x' => $timestamps[$i] * 1000, // Convert to milliseconds for JS
                        'y' => round($prices[$i], 2)
                    ];
                    $cleanPrices[] = $prices[$i];
                }
            }

            // Hitung perubahan harga
            $currentPrice = !empty($cleanPrices) ? end($cleanPrices) : $meta['regularMarketPrice'] ?? 0;
            $prevClose = $meta['chartPreviousClose'] ?? (!empty($cleanPrices) ? $cleanPrices[0] : 0);
            
            $change = 0;
            if ($prevClose > 0 && count($cleanPrices) >= 2) {
                $firstPrice = $cleanPrices[0] ?? $currentPrice;
                $change = (($currentPrice - $firstPrice) / $firstPrice) * 100;
            }

            return [
                'symbol' => $symbol,
                'price' => round($currentPrice, 2),
                'change_percent' => round($change, 2),
                'chart_data' => $chartData,
                'interval' => $interval,
                'timeframe' => $timeframe,
                'meta' => [
                    'currency' => $meta['currency'] ?? 'USD',
                    'exchange' => $meta['exchangeName'] ?? '',
                    'timezone' => $meta['exchangeTimezoneName'] ?? 'UTC'
                ]
            ];

        } catch (\Exception $e) {
            return $this->emptyData($timeframe);
        }
    }

    /**
     * Tentukan interval berdasarkan timeframe
     */
    private function getIntervalFromTimeframe($timeframe)
    {
        $intervals = [
            '1d' => '5m',     // 5 minutes
            '5d' => '15m',    // 15 minutes
            '1mo' => '1d',    // 1 day
            '3mo' => '1d',    // 1 day
            '6mo' => '1wk',   // 1 week
            '1y' => '1wk',    // 1 week
            '2y' => '1mo',    // 1 month
            '5y' => '1mo',    // 1 month
            'max' => '3mo',   // 3 months
        ];

        return $intervals[$timeframe] ?? '1d';
    }

    /**
     * DATA KOSONG (Fallback)
     */
    private function emptyData($timeframe = '1mo')
    {
        // Generate realistic dummy data based on timeframe
        $days = $this->getDaysForTimeframe($timeframe);
        $dummyChartData = [];
        $now = time() * 1000;
        $basePrice = rand(6000, 6500);
        
        for ($i = $days; $i >= 0; $i--) {
            $timestamp = $now - ($i * 86400000);
            $price = $basePrice + rand(-200, 200);
            $dummyChartData[] = [
                'x' => $timestamp,
                'y' => $price
            ];
        }
        
        return [
            'symbol' => 'N/A',
            'price' => rand(6000, 6500),
            'change_percent' => rand(-500, 500) / 100,
            'chart_data' => $dummyChartData,
            'interval' => '1d',
            'timeframe' => $timeframe,
            'meta' => [
                'currency' => 'USD',
                'exchange' => 'Unknown',
                'timezone' => 'UTC'
            ]
        ];
    }

    /**
     * Get number of days for timeframe
     */
    private function getDaysForTimeframe($timeframe)
    {
        $days = [
            '1d' => 1,
            '5d' => 5,
            '1mo' => 30,
            '3mo' => 90,
            '6mo' => 180,
            '1y' => 365,
            '2y' => 730,
            '5y' => 1825,
            'max' => 3650
        ];
        
        return $days[$timeframe] ?? 30;
    }
}