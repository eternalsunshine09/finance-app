@extends('layouts.app')

@section('title', 'Pasar US')
@section('header', 'Wall Street (US Market)')
@section('header_description', 'Real-time data S&P 500 Index & Top US Companies')

@section('content')
<div class="p-6">

    {{-- 1. MAIN CHART SECTION --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">

        {{-- Header: Title & Timeframe --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Flag_of_the_United_States.svg/320px-Flag_of_the_United_States.svg.png"
                        class="w-8 h-6 rounded shadow-sm object-cover" alt="US Flag">
                    <div>
                        <h2 class="text-2xl font-black text-gray-900 tracking-tight">S&P 500 Index</h2>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">^GSPC • CBOE Market
                            Data</span>
                    </div>
                </div>
            </div>

            {{-- Time Frame Selector --}}
            <div class="flex bg-gray-100 p-1 rounded-lg">
                @foreach(['1d' => '1D', '5d' => '5D', '1mo' => '1M', '6mo' => '6M', '1y' => '1Y'] as $tfValue =>
                $tfLabel)
                <a href="{{ request()->fullUrlWithQuery(['timeframe' => $tfValue]) }}" class="px-4 py-1.5 text-xs font-bold rounded-md transition-all duration-200
                   {{ ($timeframe ?? '1mo') == $tfValue 
                      ? 'bg-white text-black shadow-sm ring-1 ring-gray-200' 
                      : 'text-gray-500 hover:text-black hover:bg-gray-200' }}">
                    {{ $tfLabel }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Big Price & Stats Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 mb-8 border-b border-gray-100 pb-8">
            {{-- Main Price --}}
            <div class="lg:col-span-1">
                <div class="text-sm text-gray-500 font-medium mb-1">Current Value</div>
                <div class="text-5xl font-black text-gray-900 tracking-tight">
                    ${{ number_format($sp500['price'], 2) }}
                </div>
                <div class="flex items-center gap-2 mt-2">
                    <span
                        class="inline-flex items-center px-2.5 py-0.5 rounded text-sm font-bold {{ $sp500['change_percent'] >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                        {{ $sp500['change_percent'] > 0 ? '▲' : '▼' }}
                        {{ number_format(abs($sp500['change_point']), 2) }}
                        ({{ number_format(abs($sp500['change_percent']), 2) }}%)
                    </span>
                    <span class="text-xs text-gray-400 font-medium">vs Prev Close</span>
                </div>
            </div>

            {{-- Key Statistics --}}
            <div class="lg:col-span-3 grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Open</div>
                    <div class="text-lg font-bold text-gray-900">${{ number_format($sp500['open'], 2) }}</div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Day High</div>
                    <div class="text-lg font-bold text-emerald-600">${{ number_format($sp500['high'], 2) }}</div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Day Low</div>
                    <div class="text-lg font-bold text-rose-600">${{ number_format($sp500['low'], 2) }}</div>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="text-xs text-gray-500 uppercase font-bold tracking-wider mb-1">Volume</div>
                    <div class="text-lg font-bold text-gray-900">{{ number_format($sp500['volume'] / 1000000, 1) }}M
                    </div>
                </div>
            </div>
        </div>

        {{-- Chart Container --}}
        <div id="chart-us" class="w-full" style="height: 380px;"></div>
    </div>

    {{-- 2. LIST SAHAM US (Table) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <div>
                <h3 class="text-lg font-bold text-gray-900">Top US Companies</h3>
                <p class="text-xs text-gray-500">Nasdaq & NYSE Listings</p>
            </div>

            {{-- Search Bar --}}
            <div class="relative w-full md:w-72">
                <input type="text" id="searchInput" placeholder="Cari saham (Apple, Tesla...)"
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border-none rounded-xl text-sm font-medium text-gray-900 placeholder-gray-400 focus:ring-2 focus:ring-gray-900 transition-all">
                <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 transform -translate-y-1/2" fill="none"
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
                        class="bg-gray-50/50 border-b border-gray-100 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Company</th>
                        <th class="py-4 px-6 text-right">Price</th>
                        <th class="py-4 px-6 text-right">24h Change</th>
                        <th class="py-4 px-6 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-gray-50/80 transition-colors duration-200 asset-row group">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-black text-sm border border-blue-100 shadow-sm">
                                    {{ substr($asset->symbol, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 symbol-text">{{ $asset->symbol }}</div>
                                    <div class="text-xs text-gray-500 name-text font-medium">{{ $asset->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <div class="font-mono font-bold text-gray-900 text-base">
                                ${{ number_format($asset->current_price, 2) }}
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            @php $isUp = $asset->change_percent >= 0; @endphp
                            <div
                                class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $isUp ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                {{ $isUp ? '▲' : '▼' }} {{ number_format(abs($asset->change_percent), 2) }}%
                            </div>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <a href="{{ route('buy') }}?asset={{ $asset->symbol }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-900 hover:bg-black text-white text-xs font-bold rounded-lg transition-transform active:scale-95 shadow-sm">
                                Trade
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-12 text-center text-gray-400">
                            No data available.
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
document.addEventListener('DOMContentLoaded', function() {
    const chartData = @json($sp500['chart_data'] ?? []);
    const changePercent = @json($sp500['change_percent'] ?? 0);
    const color = changePercent >= 0 ? '#10b981' : '#ef4444'; // Emerald or Rose

    const options = {
        series: [{
            name: 'S&P 500',
            data: chartData
        }],
        chart: {
            type: 'area',
            height: 380,
            fontFamily: 'Inter, sans-serif',
            toolbar: {
                show: false
            },
            animations: {
                enabled: true
            }
        },
        colors: [color],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 100]
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'straight',
            width: 2
        }, // Straight line looks more professional for stocks
        xaxis: {
            type: 'datetime',
            tooltip: {
                enabled: false
            },
            axisBorder: {
                show: false
            },
            axisTicks: {
                show: false
            },
            labels: {
                style: {
                    colors: '#9ca3af',
                    fontSize: '11px'
                }
            }
        },
        yaxis: {
            labels: {
                formatter: (val) => '$' + val.toLocaleString(),
                style: {
                    colors: '#9ca3af',
                    fontSize: '11px'
                }
            }
        },
        grid: {
            borderColor: '#f3f4f6',
            strokeDashArray: 4,
            yaxis: {
                lines: {
                    show: true
                }
            },
            xaxis: {
                lines: {
                    show: false
                }
            },
            padding: {
                top: 0,
                right: 0,
                bottom: 0,
                left: 10
            }
        },
        tooltip: {
            theme: 'dark',
            x: {
                format: 'dd MMM yyyy HH:mm'
            },
            y: {
                formatter: (val) => '$' + val.toLocaleString(undefined, {
                    minimumFractionDigits: 2
                })
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#chart-us"), options);
    chart.render();

    // Search Logic
    const searchInput = document.getElementById('searchInput');
    searchInput.addEventListener('keyup', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.asset-row').forEach(row => {
            const symbol = row.querySelector('.symbol-text').innerText.toLowerCase();
            const name = row.querySelector('.name-text').innerText.toLowerCase();
            row.style.display = (symbol.includes(query) || name.includes(query)) ? '' : 'none';
        });
    });
});
</script>
@endsection