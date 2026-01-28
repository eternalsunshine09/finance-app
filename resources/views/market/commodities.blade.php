@extends('layouts.app')

@section('title', 'Harga Emas')
@section('header', 'Gold & Commodities')
@section('header_description', 'Harga Emas Antam & Digital (IDR/Gram)')

@section('content')
<div class="p-6">

    {{-- Cari Aset 'GOLD' dari Database untuk Sinkronisasi Harga --}}
    @php
    $goldAsset = $assets->firstWhere('symbol', 'GOLD');
    $displayPrice = $goldAsset ? $goldAsset->current_price : $gold['price'];
    $displayChange = $goldAsset ? $goldAsset->change_percent : $gold['change_percent'];
    @endphp

    {{-- 1. CHART SECTION (GOLD IDR) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6 mb-8">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
            <div class="flex items-center gap-4">
                <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-gray-900 tracking-tight">Emas (Gold)</h2>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">XAU/IDR • Harga per
                        Gram</span>
                </div>
            </div>

            {{-- Timeframe --}}
            <div class="flex bg-gray-100 p-1 rounded-lg">
                @foreach($timeframes as $tfValue => $tfLabel)
                <a href="{{ request()->fullUrlWithQuery(['timeframe' => $tfValue]) }}"
                    class="px-4 py-1.5 text-xs font-bold rounded-md transition-all duration-200
                   {{ ($timeframe ?? '1mo') == $tfValue ? 'bg-white text-black shadow-sm' : 'text-gray-500 hover:text-black' }}">
                    {{ $tfLabel }}
                </a>
                @endforeach
            </div>
        </div>

        {{-- Harga Big Display (Sinkron dengan Database 'GOLD') --}}
        <div class="mb-6">
            <div class="text-sm text-gray-500 font-medium mb-1">Harga Beli Sekarang (per Gram)</div>
            <div class="flex items-baseline gap-3">
                <div class="text-5xl font-black text-gray-900 tracking-tight">
                    Rp {{ number_format($displayPrice, 0, ',', '.') }}
                </div>
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-sm font-bold {{ $displayChange >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                    {{ $displayChange > 0 ? '▲' : '▼' }} {{ number_format(abs($displayChange), 2) }}%
                </span>
            </div>
            <p class="text-xs text-gray-400 mt-2">*Harga konversi realtime dari pasar dunia (Spot Gold).</p>
        </div>

        {{-- Chart --}}
        <div id="chart-gold" class="w-full" style="height: 350px;"></div>
    </div>

    {{-- 2. LIST PRODUK EMAS (ANTAM, DIGITAL, ETC) --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900">Produk Emas Tersedia</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr
                        class="bg-gray-50/50 border-b border-gray-100 text-left text-xs font-bold text-gray-400 uppercase tracking-wider">
                        <th class="py-4 px-6">Produk</th>
                        <th class="py-4 px-6 text-right">Harga / Gram</th>
                        <th class="py-4 px-6 text-center">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">

                    {{-- Loop Aset dari Database --}}
                    @forelse($assets as $asset)
                    <tr class="hover:bg-yellow-50/50 transition asset-row">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600 font-bold text-xs border border-yellow-200">
                                    @if($asset->logo)
                                    <img src="{{ $asset->logo }}" class="w-full h-full object-cover rounded-full">
                                    @else
                                    Au
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">{{ $asset->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $asset->symbol }} • Digital Spot</div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-right font-mono font-bold text-gray-900">
                            Rp {{ number_format($asset->current_price, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6 text-center">
                            <a href="{{ route('buy') }}?asset={{ $asset->symbol }}"
                                class="inline-flex items-center px-4 py-2 bg-black text-white text-xs font-bold rounded-lg hover:bg-gray-800 transition shadow-sm">
                                Beli
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="py-8 text-center text-gray-400 text-sm">
                            Belum ada produk emas tersedia.
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
    const chartData = @json($gold['chart_data'] ?? []);

    const options = {
        series: [{
            name: 'Harga Emas (IDR/gr)',
            data: chartData
        }],
        chart: {
            type: 'area',
            height: 350,
            toolbar: {
                show: false
            },
            fontFamily: 'Inter',
            animations: {
                enabled: true
            }
        },
        colors: ['#EAB308'], // Warna Kuning Emas
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
        xaxis: {
            type: 'datetime',
            labels: {
                style: {
                    colors: '#9ca3af',
                    fontSize: '11px'
                }
            },
            tooltip: {
                enabled: false
            }
        },
        yaxis: {
            labels: {
                formatter: (val) => (val / 1000000).toFixed(2) + 'jt',
                style: {
                    colors: '#9ca3af',
                    fontSize: '11px'
                }
            }
        },
        grid: {
            borderColor: '#f3f4f6',
            strokeDashArray: 4
        },
        tooltip: {
            theme: 'light',
            y: {
                formatter: (val) => "Rp " + new Intl.NumberFormat('id-ID').format(val)
            }
        }
    };

    const chart = new ApexCharts(document.querySelector("#chart-gold"), options);
    chart.render();
});
</script>
@endsection