@extends('layouts.app')

@section('title', 'Pasar Indonesia')
@section('header', 'Indonesia Stock Market')
@section('header_description', 'Jakarta Composite Index (IHSG) - Real-time data and analysis')

@section('content')
<div class="p-6">
    {{-- Chart Section --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
            <div class="mb-4 md:mb-0">
                <h2 class="text-2xl font-bold text-gray-900">IHSG</h2>
                <p class="text-sm text-gray-600 mt-1">Jakarta Composite Index</p>
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
                    {{ number_format($ihsg['price'], 2) }}
                </div>
                <div class="flex items-center gap-3">
                    <span
                        class="text-sm font-medium {{ $ihsg['change_percent'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $ihsg['change_percent'] > 0 ? '+' : '' }}{{ number_format($ihsg['change_percent'], 2) }}%
                    </span>
                    <span class="text-xs text-gray-500">
                        {{ $currentTimeframeLabel ?? '1M' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Chart Container --}}
        <div id="chart-indonesia" class="w-full" style="height: 300px;"></div>
    </div>

    {{-- Stock List --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Saham Terdaftar</h3>
            <div class="relative">
                <input type="text" id="searchInput" placeholder="Cari saham..."
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
                        <th class="py-4 px-6 text-left">Symbol</th>
                        <th class="py-4 px-6 text-left">Nama Perusahaan</th>
                        <th class="py-4 px-6 text-right">Harga</th>
                        <th class="py-4 px-6 text-right">% Perubahan</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 asset-row">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center font-bold text-gray-900 border border-gray-200">
                                    {{ substr($asset->symbol, 0, 2) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 symbol-text">{{ $asset->symbol }}</div>
                                    <div class="text-xs text-gray-500">IDX</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6">
                            <div class="text-sm text-gray-900 name-text">{{ $asset->name }}</div>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="font-bold text-gray-900">
                                Rp {{ number_format($asset->current_price, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span
                                class="text-sm font-semibold {{ $asset->change_percent >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $asset->change_percent > 0 ? '+' : '' }}{{ number_format($asset->change_percent, 2) }}%
                            </span>
                        </td>
                        <td class="py-4 px-6 text-center">
                            <a href="{{ route('buy', ['symbol' => $asset->symbol]) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Beli
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center">
                            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                                </path>
                            </svg>
                            <p class="text-gray-500">Belum ada data saham tersedia</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Market Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
        <div class="bg-white p-6 rounded-xl border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Total Saham</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ $assets->count() }}</p>
                </div>
                <div class="p-3 bg-blue-50 rounded-lg">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Perubahan Rata-rata</p>
                    @php
                    $avgChange = $assets->avg('change_percent') ?? 0;
                    @endphp
                    <p class="text-2xl font-bold mt-1 {{ $avgChange >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $avgChange > 0 ? '+' : '' }}{{ number_format($avgChange, 2) }}%
                    </p>
                </div>
                <div class="p-3 {{ $avgChange >= 0 ? 'bg-green-50' : 'bg-red-50' }} rounded-lg">
                    <svg class="w-6 h-6 {{ $avgChange >= 0 ? 'text-green-600' : 'text-red-600' }}" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500">Volume Perdagangan</p>
                    <p class="text-2xl font-bold text-gray-900 mt-1">{{ number_format(rand(1000, 10000), 0) }}B</p>
                </div>
                <div class="p-3 bg-purple-50 rounded-lg">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                        </path>
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
// Render Indonesia Chart
const chartData = @json($ihsg['chart_data'] ?? []);
const changePercent = @json($ihsg['change_percent'] ?? 0);
const chartColor = changePercent >= 0 ? '#10b981' : '#ef4444';

const options = {
    series: [{
        name: 'IHSG',
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
                return value.toLocaleString();
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
                return value.toLocaleString();
            }
        },
        theme: 'light',
        style: {
            fontSize: '12px'
        }
    },
    colors: [chartColor]
};

const chart = new ApexCharts(document.querySelector("#chart-indonesia"), options);
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