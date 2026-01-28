@extends('layouts.app')

@section('title', 'Pasar Indonesia')
@section('header', 'Indonesia Stock Market')
@section('header_description', 'Data pergerakan Indeks Harga Saham Gabungan (IHSG) dan emiten saham.')

@section('content')
<div class="space-y-6">

    {{-- 1. CHART SECTION (IHSG) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 relative overflow-hidden">
        {{-- Header Chart --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-red-50 flex items-center justify-center">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/5/53/Indonesia_Stock_Exchange.png"
                        alt="IDX" class="w-6 h-6 object-contain">
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Composite Index (IHSG)</h2>
                    <p class="text-xs text-gray-500 font-mono">JKSE • Market Open</p>
                </div>
            </div>

            {{-- Timeframe Selector --}}
            <div class="flex bg-gray-100 rounded-lg p-1">
                @foreach($timeframes as $tfValue => $tfLabel)
                <a href="{{ request()->fullUrlWithQuery(['timeframe' => $tfValue]) }}"
                    class="px-4 py-1.5 text-xs font-bold rounded-md transition-all duration-200 {{ ($timeframe ?? '1mo') == $tfValue ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                    {{ $tfLabel }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Price Display --}}
        <div class="flex items-baseline gap-4 mb-2">
            <h1 class="text-4xl font-black text-gray-900 tracking-tight">
                {{ number_format($ihsg['price'], 2, ',', '.') }}
            </h1>
            <div
                class="flex items-center gap-2 px-3 py-1 rounded-full {{ $ihsg['change_percent'] >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="{{ $ihsg['change_percent'] >= 0 ? 'M13 7h8m0 0v8m0-8l-8 8-4-4-6 6' : 'M13 17h8m0 0V9m0 8l-8-8-4 4-6-6' }}">
                    </path>
                </svg>
                <span class="text-sm font-bold">
                    {{ $ihsg['change_percent'] > 0 ? '+' : '' }}{{ number_format($ihsg['change_percent'], 2) }}%
                </span>
            </div>
        </div>
        <p class="text-xs text-gray-400 mb-6">Last update: {{ now()->format('d M Y, H:i') }} WIB</p>

        {{-- Area Chart --}}
        <div id="chart-indonesia" class="w-full" style="min-height: 320px;"></div>
    </div>

    {{-- 2. STOCK LIST --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
            <h3 class="text-lg font-bold text-gray-900">Emiten Terdaftar</h3>
            <div class="relative w-full md:w-72">
                <input type="text" id="searchInput" placeholder="Cari kode atau nama..."
                    class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-black focus:border-transparent outline-none transition">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold tracking-wider">
                    <tr>
                        <th class="py-4 px-6 text-left">Emiten</th>
                        <th class="py-4 px-6 text-left">Nama Perusahaan</th>
                        <th class="py-4 px-6 text-right">Harga</th>
                        <th class="py-4 px-6 text-right">% Change</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-gray-50 transition-colors duration-150 asset-row group">

                        {{-- Kolom Emiten (Logo) --}}
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-10 h-10 rounded-lg bg-white border border-gray-100 flex items-center justify-center shadow-sm overflow-hidden p-1">
                                    @if($asset->logo)
                                    <img src="{{ $asset->logo }}" class="w-full h-full object-contain">
                                    @else
                                    @php
                                    $colors = ['bg-red-50 text-red-600', 'bg-blue-50 text-blue-600', 'bg-green-50
                                    text-green-600', 'bg-purple-50 text-purple-600', 'bg-orange-50 text-orange-600'];
                                    $colorClass = $colors[$asset->id % count($colors)];
                                    @endphp
                                    <div
                                        class="w-full h-full rounded {{ $colorClass }} flex items-center justify-center font-black text-xs">
                                        {{ substr($asset->symbol, 0, 2) }}
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900 symbol-text text-base">{{ $asset->symbol }}
                                    </div>
                                    <div class="text-[10px] text-gray-400 font-mono">JK</div>
                                </div>
                            </div>
                        </td>

                        {{-- Kolom Nama --}}
                        <td class="py-4 px-6">
                            <div class="font-medium text-gray-700 name-text max-w-[200px] truncate"
                                title="{{ $asset->name }}">
                                {{ $asset->name }}
                            </div>
                        </td>

                        {{-- Kolom Harga --}}
                        <td class="py-4 px-6 text-right">
                            <span class="font-mono font-bold text-gray-900 text-base">
                                Rp {{ number_format($asset->current_price, 0, ',', '.') }}
                            </span>
                        </td>

                        {{-- Kolom Perubahan --}}
                        <td class="py-4 px-6 text-right">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-bold {{ $asset->change_percent >= 0 ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700' }}">
                                {{ $asset->change_percent > 0 ? '+' : '' }}{{ number_format($asset->change_percent, 2) }}%
                            </span>
                        </td>

                        {{-- Kolom Aksi (INI YANG DIPERBAIKI) --}}
                        <td class="py-4 px-6 text-center">
                            {{-- Mengarahkan ke route 'buy' dengan parameter symbol --}}
                            <a href="{{ route('buy', ['symbol' => $asset->symbol]) }}"
                                class="inline-flex items-center px-4 py-2 bg-black hover:bg-gray-800 text-white text-xs font-bold rounded-lg transition-all shadow-sm opacity-0 group-hover:opacity-100 transform translate-y-1 group-hover:translate-y-0">
                                Trade
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-16 text-center">
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 01-2-2z">
                                    </path>
                                </svg>
                                <p class="font-medium">Belum ada data saham.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($assets->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50 flex justify-center">
            {{ $assets->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. CHART CONFIGURATION
    const chartData = @json($ihsg['chart_data'] ?? []);
    const changePercent = @json($ihsg['change_percent'] ?? 0);
    const mainColor = changePercent >= 0 ? '#10b981' : '#ef4444';

    const options = {
        series: [{
            name: 'Harga IHSG',
            data: chartData
        }],
        chart: {
            type: 'area',
            height: 320,
            fontFamily: 'Inter, sans-serif',
            toolbar: {
                show: false
            },
            zoom: {
                enabled: true
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 800
            }
        },
        colors: [mainColor],
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
            curve: 'smooth',
            width: 2
        },
        grid: {
            show: true,
            borderColor: '#f3f4f6',
            strokeDashArray: 4,
            xaxis: {
                lines: {
                    show: false
                }
            }
        },
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
                },
                datetimeFormatter: {
                    year: 'yyyy',
                    month: "MMM 'yy",
                    day: 'dd MMM',
                    hour: 'HH:mm'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#9ca3af',
                    fontSize: '11px'
                },
                formatter: (val) => val.toFixed(0)
            }
        },
        tooltip: {
            theme: 'light',
            x: {
                format: 'dd MMM yyyy HH:mm'
            },
            y: {
                formatter: (val) => val.toLocaleString('id-ID', {
                    style: 'currency',
                    currency: 'IDR'
                })
            }
        }
    };

    if (chartData.length > 0) {
        const chart = new ApexCharts(document.querySelector("#chart-indonesia"), options);
        chart.render();
    } else {
        document.querySelector("#chart-indonesia").innerHTML =
            '<div class="flex items-center justify-center h-full text-gray-400">Data grafik tidak tersedia saat ini.</div>';
    }

    // 2. SEARCH
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const query = this.value.toLowerCase();
            document.querySelectorAll('.asset-row').forEach(row => {
                const text = row.innerText.toLowerCase();
                row.style.display = text.includes(query) ? '' : 'none';
            });
        });
    }
});
</script>
@endsection