@extends('layouts.app')

@section('title', 'Analisis Portofolio')
@section('header', '📊 Portofolio Detail')

@section('content')

{{-- 1. KARTU RINGKASAN --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    {{-- Total Modal --}}
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden group">
        <div class="relative z-10">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Modal (Est. IDR)</span>
            <div class="text-3xl font-black text-slate-800 mt-2">
                Rp {{ number_format($totalModal, 0, ',', '.') }}
            </div>
        </div>
        <div
            class="absolute right-0 top-0 p-6 opacity-5 pointer-events-none group-hover:scale-110 transition duration-500">
            <svg class="w-24 h-24 text-slate-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                </path>
            </svg>
        </div>
    </div>

    {{-- Nilai Aset Sekarang --}}
    <div class="bg-white p-6 rounded-[2rem] shadow-sm border border-slate-100 relative overflow-hidden group">
        <div class="relative z-10">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Nilai Aset (Est. IDR)</span>
            <div class="text-3xl font-black text-indigo-600 mt-2">
                Rp {{ number_format($totalNilaiSekarang, 0, ',', '.') }}
            </div>
        </div>
        <div
            class="absolute right-0 top-0 p-6 opacity-5 pointer-events-none group-hover:scale-110 transition duration-500">
            <svg class="w-24 h-24 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
            </svg>
        </div>
    </div>

    {{-- Total Profit/Loss --}}
    <div
        class="p-6 rounded-[2rem] shadow-sm border relative overflow-hidden group {{ $totalProfitRp >= 0 ? 'bg-emerald-50 border-emerald-100' : 'bg-rose-50 border-rose-100' }}">
        <div class="relative z-10">
            <span
                class="text-xs font-bold {{ $totalProfitRp >= 0 ? 'text-emerald-600' : 'text-rose-600' }} uppercase tracking-wider">
                Total Unrealized PnL
            </span>
            <div class="flex items-end gap-3 mt-2">
                <span class="text-3xl font-black {{ $totalProfitRp >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                    {{ $totalProfitRp >= 0 ? '+' : '' }} Rp {{ number_format(abs($totalProfitRp), 0, ',', '.') }}
                </span>
            </div>
            <div
                class="mt-2 inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $totalProfitRp >= 0 ? 'bg-emerald-200 text-emerald-800' : 'bg-rose-200 text-rose-800' }}">
                {{ $totalProfitRp >= 0 ? '▲' : '▼' }} {{ number_format($totalProfitPct, 2) }}%
            </div>
        </div>
        <div
            class="absolute right-0 top-0 p-6 opacity-10 pointer-events-none group-hover:scale-110 transition duration-500">
            <svg class="w-24 h-24 {{ $totalProfitRp >= 0 ? 'text-emerald-600' : 'text-rose-600' }}" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                </path>
            </svg>
        </div>
    </div>
</div>

{{-- 2. DETAIL TABEL & CHART --}}
<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">

    {{-- TABEL PORTFOLIO --}}
    <div class="lg:col-span-3 bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
        <div
            class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-slate-50/50">
            <div>
                <h3 class="font-bold text-slate-800 text-lg">Rincian Aset</h3>
                <p class="text-slate-400 text-xs mt-1">Detail performa setiap instrumen investasi.</p>
            </div>
            <span
                class="text-xs font-bold bg-white border border-slate-200 text-slate-600 px-3 py-1.5 rounded-lg shadow-sm">
                {{ count($portfolioList) }} Aset Aktif
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead
                    class="bg-slate-50 text-slate-500 text-[10px] uppercase font-bold tracking-wider border-b border-slate-100">
                    <tr>
                        <th class="p-5 pl-8">Produk</th>
                        <th class="p-5 text-center">Unit</th>
                        <th class="p-5 text-right">Avg Buy</th>
                        <th class="p-5 text-right">Harga Pasar</th>
                        <th class="p-5 text-right">Total Modal</th>
                        <th class="p-5 text-right">Total Nilai</th>
                        <th class="p-5 text-right">Unrealized PnL</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-sm">
                    @forelse($portfolioList as $item)

                    {{-- Logic Variabel --}}
                    @php
                    $isCrypto = $item->type == 'Crypto';
                    $currency = $isCrypto ? '$' : 'Rp';
                    $decimal = $isCrypto ? 2 : 0;
                    $decPoint = $isCrypto ? '.' : ',';
                    $thouSep = $isCrypto ? ',' : '.';

                    // Warna Icon
                    $iconBg = 'bg-slate-100'; $iconColor = 'text-slate-600';
                    $sym = strtoupper($item->symbol);
                    if($sym == 'BTC') { $iconBg = 'bg-orange-50'; $iconColor = 'text-orange-500'; }
                    elseif($sym == 'ETH') { $iconBg = 'bg-purple-50'; $iconColor = 'text-purple-500'; }
                    elseif($sym == 'USDT') { $iconBg = 'bg-emerald-50'; $iconColor = 'text-emerald-500'; }
                    elseif($sym == 'BBCA') { $iconBg = 'bg-blue-50'; $iconColor = 'text-blue-600'; }
                    @endphp

                    <tr class="hover:bg-slate-50/80 transition duration-150 group">

                        {{-- 1. PRODUK --}}
                        <td class="p-5 pl-8">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-xl {{ $iconBg }} flex items-center justify-center font-bold text-xs {{ $iconColor }} border border-slate-100 shadow-sm">
                                    {{ substr($sym, 0, 1) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 flex items-center gap-1">
                                        {{ $item->symbol }}
                                        @if($isCrypto) <span
                                            class="text-[8px] bg-slate-100 text-slate-500 px-1 rounded border border-slate-200">USD</span>
                                        @endif
                                    </div>
                                    <div class="text-[10px] text-slate-400">{{ $item->name }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- 2. UNIT SISA --}}
                        <td class="p-5 text-center font-mono text-slate-600">
                            {{ number_format($item->quantity, 4) }}
                        </td>

                        {{-- 3. AVG BUY --}}
                        <td class="p-5 text-right text-slate-500 text-xs font-mono">
                            {{ $currency }} {{ number_format($item->avg_price, $decimal, $decPoint, $thouSep) }}
                        </td>

                        {{-- 4. HARGA PASAR --}}
                        <td class="p-5 text-right font-medium text-slate-700">
                            {{ $currency }} {{ number_format($item->current_price, $decimal, $decPoint, $thouSep) }}
                        </td>

                        {{-- 5. TOTAL MODAL --}}
                        <td class="p-5 text-right text-slate-500 text-xs">
                            {{ $currency }}
                            {{ number_format($item->avg_price * $item->quantity, $decimal, $decPoint, $thouSep) }}
                        </td>

                        {{-- 6. TOTAL NILAI --}}
                        <td class="p-5 text-right">
                            <div class="font-bold text-slate-800">
                                {{ $currency }} {{ number_format($item->current_value, $decimal, $decPoint, $thouSep) }}
                            </div>
                            @if($isCrypto)
                            <div class="text-[9px] text-slate-400">≈ Rp
                                {{ number_format($item->current_value_idr, 0, ',', '.') }}</div>
                            @endif
                        </td>

                        {{-- 7. UNREALIZED PNL --}}
                        <td class="p-5 text-right">
                            @php
                            $isProfit = $item->profit_loss_rp >= 0;
                            $colorText = $isProfit ? 'text-emerald-600' : 'text-rose-600';
                            $bgBadge = $isProfit ? 'bg-emerald-50' : 'bg-rose-50';
                            @endphp
                            <div class="flex flex-col items-end">
                                <span class="text-xs font-bold {{ $colorText }}">
                                    {{ $isProfit ? '+' : '' }}{{ $currency }}
                                    {{ number_format($item->profit_loss_rp, $decimal, $decPoint, $thouSep) }}
                                </span>
                                <span
                                    class="mt-1 text-[10px] font-bold px-1.5 py-0.5 rounded {{ $bgBadge }} {{ $colorText }}">
                                    {{ $isProfit ? '▲' : '▼' }} {{ number_format($item->profit_loss_pct, 2) }}%
                                </span>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="p-12 text-center text-slate-400">
                            <div class="flex flex-col items-center">
                                <div class="p-4 bg-slate-50 rounded-full mb-3">
                                    <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                        </path>
                                    </svg>
                                </div>
                                <p class="mb-3 font-medium text-slate-500">Belum ada aset dalam portofolio.</p>
                                <a href="{{ route('buy') }}"
                                    class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition shadow-md shadow-indigo-200">
                                    Mulai Investasi
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- CHART DONUT --}}
    <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 p-6 flex flex-col">
        <h3 class="font-bold text-slate-800 text-center mb-6">Alokasi Aset (IDR)</h3>

        <div class="relative w-full h-56 flex items-center justify-center mb-6">
            <canvas id="portfolioChart"></canvas>
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total</span>
                <span class="text-lg font-black text-slate-800">100%</span>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex-1 overflow-y-auto pr-2 space-y-3 max-h-64 custom-scrollbar">
            @php $colors = ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1']; @endphp
            @foreach($portfolioList as $index => $item)
            <div class="flex justify-between items-center text-xs">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full"
                        style="background-color: {{ $colors[$index % count($colors)] }}"></span>
                    <span class="font-medium text-slate-600">{{ $item->symbol }}</span>
                </div>
                <span class="font-bold text-slate-800">
                    {{ number_format(($item->current_value_idr / ($totalNilaiSekarang ?: 1)) * 100, 1) }}%
                </span>
            </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
// Data Chart menggunakan IDR Value agar donatnya akurat
const labels = @json(array_column($portfolioList, 'symbol'));
const values = @json(array_column($portfolioList, 'current_value_idr'));
const colors = ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#6366F1'];

const ctx = document.getElementById('portfolioChart');

if (labels.length > 0) {
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: colors,
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: '#fff',
                    titleColor: '#1e293b',
                    bodyColor: '#475569',
                    borderColor: '#e2e8f0',
                    borderWidth: 1,
                    padding: 10,
                    callbacks: {
                        label: function(context) {
                            let val = context.raw;
                            return ' Rp ' + new Intl.NumberFormat('id-ID').format(val);
                        }
                    }
                }
            }
        }
    });
}
</script>
@endsection