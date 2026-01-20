@extends('layouts.app')

@section('title', 'Pasar Crypto')
@section('header', 'Crypto Market')
@section('header_description', 'Bitcoin (BTC) - Real-time cryptocurrency data')

@section('content')
<div class="p-6">
    {{-- Chart Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div class="mb-4 md:mb-0">
                <h2 class="text-2xl font-bold text-gray-900">Bitcoin (BTC)</h2>
                <p class="text-sm text-gray-600 mt-1">Leading Cryptocurrency</p>
            </div>

            {{-- Time Frame Selector --}}
            <div class="flex items-center space-x-2 bg-gray-100 rounded-lg p-1">
                @foreach($timeframes as $tfValue => $tfLabel)
                <a href="{{ request()->fullUrlWithQuery(['timeframe' => $tfValue]) }}" class="px-3 py-1.5 text-sm font-medium rounded-md transition-all duration-200
                              {{ ($timeframe ?? '1mo') == $tfValue 
                                 ? 'bg-gray-900 text-white shadow-sm' 
                                 : 'text-gray-700 hover:bg-gray-200 hover:text-gray-900' }}">
                    {{ $tfLabel }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Price Display --}}
        <div class="flex justify-between items-end mb-6">
            <div>
                <div class="text-4xl font-bold text-gray-900 mb-2">
                    ${{ number_format($btc['price'], 2) }}
                </div>
                <div class="flex items-center gap-3">
                    <span
                        class="text-sm font-medium {{ $btc['change_percent'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $btc['change_percent'] > 0 ? '+' : '' }}{{ number_format($btc['change_percent'], 2) }}%
                    </span>
                    <span class="text-xs text-gray-500">
                        {{ $currentTimeframeLabel ?? '1M' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Chart Container --}}
        <div id="chart-crypto" class="w-full" style="height: 300px;"></div>
    </div>

    {{-- Crypto List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Cryptocurrencies</h3>
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Search crypto..."
                    class="text-sm bg-white border border-gray-300 rounded-lg px-3 py-2 pl-10 w-64 focus:outline-none focus:ring-2 focus:ring-gray-900 focus:border-transparent">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr
                        class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider font-medium border-b border-gray-200">
                        <th class="py-3 px-6 text-left">Symbol</th>
                        <th class="py-3 px-6 text-left">Name</th>
                        <th class="py-3 px-6 text-right">Price</th>
                        <th class="py-3 px-6 text-right">Change</th>
                        <th class="py-3 px-6 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 asset-row">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-yellow-100 flex items-center justify-center">
                                    <span
                                        class="text-xs font-bold text-yellow-800">{{ substr($asset->symbol, 0, 1) }}</span>
                                </div>
                                <span class="font-medium text-gray-900 symbol-text">{{ $asset->symbol }}</span>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="text-sm text-gray-700 name-text">{{ $asset->name }}</div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="font-mono text-gray-900 font-medium">
                                ${{ number_format($asset->current_price, 2) }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span
                                class="text-sm font-medium {{ $asset->change_percent >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $asset->change_percent > 0 ? '+' : '' }}{{ number_format($asset->change_percent, 2) }}%
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <a href="{{ route('buy', ['symbol' => $asset->symbol]) }}"
                                class="inline-block px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-xs font-medium rounded-lg transition-colors duration-200">
                                Trade
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-8 text-center text-gray-500">
                            <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <p>No cryptocurrency data available</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// Render Crypto Chart
const chartData = @json($btc['chart_data'] ?? []);
const changePercent = @json($btc['change_percent'] ?? 0);
const chartColor = changePercent >= 0 ? '#10b981' : '#ef4444';

const options = {
    series: [{
        name: 'BTC',
        data: chartData
    }],
    chart: {
        type: 'area',
        height: 300,
        toolbar: {
            show: true,
            tools: {
                download: true,
                selection: true,
                zoom: true,
                zoomin: true,
                zoomout: true,
                pan: true,
                reset: true
            }
        },
        zoom: {
            enabled: true,
            type: 'x'
        }
    },
    stroke: {
        curve: 'smooth',
        width: 2,
        colors: [chartColor]
    },
    fill: {
        type: 'gradient',
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.7,
            opacityTo: 0.1,
            stops: [0, 90, 100]
        }
    },
    grid: {
        borderColor: '#f3f4f6',
        strokeDashArray: 4,
        xaxis: {
            lines: {
                show: true
            }
        },
        yaxis: {
            lines: {
                show: true
            }
        }
    },
    xaxis: {
        type: 'datetime',
        labels: {
            datetimeUTC: false,
            style: {
                colors: '#6b7280',
                fontSize: '11px'
            }
        },
        tooltip: {
            enabled: false
        }
    },
    yaxis: {
        labels: {
            formatter: function(value) {
                return '$' + value.toLocaleString();
            },
            style: {
                colors: '#6b7280',
                fontSize: '11px'
            }
        }
    },
    tooltip: {
        x: {
            format: 'dd MMM yyyy'
        },
        y: {
            formatter: function(value) {
                return '$' + value.toLocaleString(undefined, {
                    minimumFractionDigits: 2
                });
            }
        },
        theme: 'light',
        style: {
            fontSize: '12px'
        }
    },
    colors: [chartColor]
};

const chart = new ApexCharts(document.querySelector("#chart-crypto"), options);
chart.render();

// Search Functionality
document.getElementById('searchInput').addEventListener('keyup', function() {
    const query = this.value.toLowerCase();
    document.querySelectorAll('.asset-row').forEach(row => {
        const symbol = row.querySelector('.symbol-text').textContent.toLowerCase();
        const name = row.querySelector('.name-text').textContent.toLowerCase();
        row.style.display = (symbol.includes(query) || name.includes(query)) ? '' : 'none';
    });
});
</script>
@endsection