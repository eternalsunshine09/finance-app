@extends('layouts.app')

@section('title', 'Analisis Portofolio')
@section('header', 'Portofolio Detail')

@section('content')

{{-- 1. HEADER SECTION --}}
<div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-8">
    <div>
        <h2 class="text-2xl font-bold text-gray-900 tracking-tight">Kinerja Investasi</h2>
        <p class="text-sm text-gray-500 mt-1">Pantau pertumbuhan aset dan keuntungan Anda secara real-time.</p>
    </div>
    <div class="text-right hidden md:block">
        <span class="text-xs font-semibold text-gray-400 bg-gray-100 px-3 py-1 rounded-full">
            Last Update: {{ now()->format('H:i, d M Y') }}
        </span>
    </div>
</div>

{{-- 2. KARTU RINGKASAN (STATS) --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    {{-- Total Modal --}}
    <div
        class="bg-white p-6 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-blue-50 text-blue-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                        </path>
                    </svg>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Modal</span>
            </div>
            <div class="text-2xl font-black text-gray-800">
                Rp {{ number_format($totalModal, 0, ',', '.') }}
            </div>
        </div>
        <div
            class="absolute -right-4 -bottom-4 opacity-[0.03] group-hover:opacity-10 transition duration-500 scale-150">
            <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 20 20">
                <path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z" />
                <path fill-rule="evenodd"
                    d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z"
                    clip-rule="evenodd" />
            </svg>
        </div>
    </div>

    {{-- Nilai Aset Sekarang --}}
    <div
        class="bg-white p-6 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border border-gray-100 relative overflow-hidden group hover:-translate-y-1 transition-all duration-300">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <div class="p-2 bg-indigo-50 text-indigo-600 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nilai Estimasi</span>
            </div>
            <div class="text-2xl font-black text-indigo-600">
                Rp {{ number_format($totalNilaiSekarang, 0, ',', '.') }}
            </div>
        </div>
    </div>

    {{-- Total Profit/Loss --}}
    <div
        class="p-6 rounded-2xl shadow-[0_2px_15px_-3px_rgba(0,0,0,0.07),0_10px_20px_-2px_rgba(0,0,0,0.04)] border relative overflow-hidden group hover:-translate-y-1 transition-all duration-300 {{ $totalProfitRp >= 0 ? 'bg-emerald-50/50 border-emerald-100' : 'bg-rose-50/50 border-rose-100' }}">
        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <div
                    class="p-2 rounded-lg {{ $totalProfitRp >= 0 ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <span
                    class="text-xs font-bold {{ $totalProfitRp >= 0 ? 'text-emerald-600' : 'text-rose-600' }} uppercase tracking-wider">Unrealized
                    PnL</span>
            </div>

            <div class="flex items-baseline gap-2">
                <span class="text-2xl font-black {{ $totalProfitRp >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ $totalProfitRp >= 0 ? '+' : '' }} Rp {{ number_format(abs($totalProfitRp), 0, ',', '.') }}
                </span>
            </div>

            <div
                class="mt-2 inline-flex items-center px-2 py-0.5 rounded-md text-xs font-bold {{ $totalProfitRp >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-rose-100 text-rose-700' }}">
                {{ $totalProfitRp >= 0 ? '▲' : '▼' }} {{ number_format($totalProfitPct, 2) }}%
            </div>
        </div>
    </div>
</div>

{{-- 3. LAYOUT UTAMA (TABEL & CHART) --}}
<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

    {{-- CHART DONUT --}}
    <div
        class="lg:col-span-1 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 flex flex-col h-fit sticky top-6">
        <h3 class="font-bold text-gray-800 text-center mb-6 text-sm uppercase tracking-wide">Alokasi Aset (IDR)</h3>

        <div class="relative w-full aspect-square flex items-center justify-center mb-6">
            <canvas id="portfolioChart"></canvas>
            {{-- Center Text --}}
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">Total</span>
                <span class="text-xl font-black text-gray-800">100%</span>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex-1 overflow-y-auto pr-1 space-y-3 max-h-[300px] custom-scrollbar">
            @php $colors = ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1']; @endphp
            @foreach($portfolioList as $index => $item)
            @php $percentage = ($item->current_value_idr / ($totalNilaiSekarang ?: 1)) * 100; @endphp
            <div class="flex justify-between items-center text-xs group">
                <div class="flex items-center gap-2 overflow-hidden">
                    <span class="w-2 h-2 rounded-full flex-shrink-0"
                        style="background-color: {{ $colors[$index % count($colors)] }}"></span>
                    <span
                        class="font-medium text-gray-600 truncate group-hover:text-black transition">{{ $item->symbol }}</span>
                </div>
                <span class="font-bold text-gray-800">{{ number_format($percentage, 1) }}%</span>
            </div>
            @endforeach
        </div>
    </div>

    {{-- TABEL PORTFOLIO --}}
    <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
            <div>
                <h3 class="font-bold text-gray-800 text-lg">Rincian Aset</h3>
                <p class="text-gray-400 text-xs mt-1">Detail performa setiap instrumen.</p>
            </div>
            <span
                class="text-xs font-bold bg-white border border-gray-200 text-gray-600 px-3 py-1.5 rounded-lg shadow-sm">
                {{ count($portfolioList) }} Items
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead
                    class="bg-gray-50 text-gray-500 text-[10px] uppercase font-bold tracking-wider border-b border-gray-100">
                    <tr>
                        <th class="p-5 pl-6">Produk</th>
                        <th class="p-5 text-center">Unit</th>
                        <th class="p-5 text-right">Avg Buy</th>
                        <th class="p-5 text-right">Harga Pasar</th>
                        <th class="p-5 text-right hidden sm:table-cell">Total Nilai</th>
                        <th class="p-5 text-right">PnL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 text-sm">
                    @forelse($portfolioList as $item)

                    {{-- Variabel Logic --}}
                    @php
                    $isCrypto = $item->type == 'Crypto';
                    $currency = $isCrypto ? '$' : 'Rp';
                    $decimal = $isCrypto ? 2 : 0;
                    $decPoint = $isCrypto ? '.' : ',';
                    $thouSep = $isCrypto ? ',' : '.';

                    // Styling Icon
                    $iconBg = 'bg-gray-100'; $iconColor = 'text-gray-500';
                    $sym = strtoupper($item->symbol);
                    if($sym == 'BTC') { $iconBg = 'bg-orange-100'; $iconColor = 'text-orange-600'; }
                    elseif($sym == 'ETH') { $iconBg = 'bg-purple-100'; $iconColor = 'text-purple-600'; }
                    elseif($sym == 'USDT') { $iconBg = 'bg-emerald-100'; $iconColor = 'text-emerald-600'; }
                    elseif($item->type == 'Stock') { $iconBg = 'bg-blue-100'; $iconColor = 'text-blue-600'; }

                    $isProfit = $item->profit_loss_rp >= 0;
                    $pnlColor = $isProfit ? 'text-emerald-600' : 'text-rose-600';
                    $pnlBg = $isProfit ? 'bg-emerald-500' : 'bg-rose-500';
                    @endphp

                    <tr class="hover:bg-gray-50 transition duration-150 group">
                        {{-- 1. PRODUK --}}
                        <td class="p-5 pl-6">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl {{ $iconBg }} flex items-center justify-center font-bold text-sm {{ $iconColor }} border border-white shadow-sm ring-1 ring-gray-100">
                                    {{ substr($sym, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-gray-800 flex items-center gap-2">
                                        {{ $item->symbol }}
                                        @if($isCrypto)
                                        <span
                                            class="text-[9px] bg-gray-100 text-gray-500 px-1.5 py-0.5 rounded font-mono">USD</span>
                                        @endif
                                    </div>
                                    <div class="text-xs text-gray-400 font-medium">{{ Str::limit($item->name, 20) }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- 2. UNIT --}}
                        <td class="p-5 text-center font-mono text-gray-600 text-xs">
                            {{ number_format($item->quantity, 4) }}
                        </td>

                        {{-- 3. AVG BUY --}}
                        <td class="p-5 text-right text-gray-500 text-xs font-mono">
                            {{ $currency }} {{ number_format($item->avg_price, $decimal, $decPoint, $thouSep) }}
                        </td>

                        {{-- 4. HARGA PASAR --}}
                        <td class="p-5 text-right font-medium text-gray-700 font-mono">
                            {{ $currency }} {{ number_format($item->current_price, $decimal, $decPoint, $thouSep) }}
                        </td>

                        {{-- 5. TOTAL NILAI --}}
                        <td class="p-5 text-right hidden sm:table-cell">
                            <div class="font-bold text-gray-800">
                                {{ $currency }} {{ number_format($item->current_value, $decimal, $decPoint, $thouSep) }}
                            </div>
                            @if($isCrypto)
                            <div class="text-[10px] text-gray-400">≈ Rp
                                {{ number_format($item->current_value_idr, 0, ',', '.') }}</div>
                            @endif
                        </td>

                        {{-- 6. PNL (Visual Bar) --}}
                        <td class="p-5 text-right">
                            <div class="flex flex-col items-end">
                                <span class="text-xs font-bold {{ $pnlColor }} font-mono">
                                    {{ $isProfit ? '+' : '' }}{{ number_format($item->profit_loss_pct, 2) }}%
                                </span>
                                <span class="text-[10px] text-gray-400 mb-1">
                                    {{ $isProfit ? '+' : '' }}{{ $currency }}
                                    {{ number_format($item->profit_loss_rp, $decimal, $decPoint, $thouSep) }}
                                </span>

                                {{-- Simple Progress Bar --}}
                                <div class="w-20 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                    <div class="h-full {{ $pnlBg }}"
                                        style="width: {{ min(abs($item->profit_loss_pct), 100) }}%"></div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <div class="p-4 bg-gray-50 rounded-full mb-3">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>
                                </div>
                                <p class="mb-3 font-medium text-gray-500">Belum ada aset.</p>
                                <a href="{{ route('buy') }}"
                                    class="px-5 py-2 bg-black hover:bg-gray-800 text-white rounded-lg text-xs font-bold transition shadow-lg">Mulai
                                    Investasi</a>
                            </div>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Logic Chart tetap sama
const labels = @json(array_column($portfolioList, 'symbol'));
const values = @json(array_column($portfolioList, 'current_value_idr'));
const colors = ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1'];

const ctx = document.getElementById('portfolioChart');

if (labels.length > 0 && ctx) {
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%', // Lebih tipis agar terlihat modern
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#1e293b',
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return ' Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                        }
                    }
                }
            }
        }
    });
}
</script>
@endsection