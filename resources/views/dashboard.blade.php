@extends('layouts.app')

@section('title', 'Dashboard Investor')
@section('header', 'Ringkasan Portofolio')

@section('content')

{{-- 1. TOMBOL ADMIN --}}
@if(Auth::user()->role == 'admin')
<div class="mb-8">
    <a href="{{ route('admin.dashboard') }}"
        class="w-full md:w-auto bg-indigo-50 border border-indigo-200 text-indigo-600 px-6 py-3 rounded-xl font-bold hover:bg-indigo-600 hover:text-white transition shadow-sm hover:shadow-indigo-200 inline-flex items-center gap-3">
        <span class="bg-indigo-100 text-indigo-600 p-1 rounded group-hover:bg-white/20">👑</span>
        Masuk ke Admin Panel
    </a>
</div>
@endif

{{-- 2. STATS CARDS (GRID UTAMA) --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    {{-- Total Kekayaan --}}
    <div class="md:col-span-1 relative group">
        <div
            class="absolute inset-0 bg-gradient-to-r from-indigo-400 to-purple-400 rounded-[2rem] blur opacity-20 group-hover:opacity-40 transition duration-500">
        </div>
        <div
            class="relative bg-white border border-slate-100 p-8 rounded-[2rem] h-full flex flex-col justify-between overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div>
                <h3 class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-2">Total Kekayaan Bersih</h3>
                <div class="flex items-baseline gap-1">
                    <span class="text-slate-500 text-lg font-medium">Rp</span>
                    <span class="text-3xl md:text-4xl font-black text-slate-800 tracking-tight">
                        {{ number_format($rekap['total_kekayaan'], 0, ',', '.') }}
                    </span>
                </div>
                <div
                    class="mt-4 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-600 text-xs font-bold">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                    <span>+2.4% Bulan ini</span>
                </div>
            </div>
            <div class="absolute right-0 top-0 p-6 opacity-5 pointer-events-none">
                <svg class="w-32 h-32 text-indigo-900" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>
    </div>

    {{-- Kartu Pecahan --}}
    <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
        {{-- Saldo Tunai --}}
        <div
            class="bg-white p-6 rounded-[2rem] relative overflow-hidden shadow-sm border border-slate-100 group hover:border-emerald-200 transition">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-emerald-50 rounded-xl text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                    </div>
                    <span
                        class="text-[10px] uppercase text-emerald-600 font-bold bg-emerald-50 px-2 py-1 rounded-lg">Available</span>
                </div>
                <h3 class="text-slate-500 text-xs font-bold uppercase">Kas Tunai (IDR)</h3>
                <p class="text-2xl font-bold text-slate-800 mt-1">Rp
                    {{ number_format($rekap['uang_tunai'], 0, ',', '.') }}</p>
            </div>
            <div
                class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-50 rounded-full blur-xl group-hover:bg-emerald-100 transition">
            </div>
        </div>

        {{-- Nilai Aset Investasi --}}
        <div
            class="bg-white p-6 rounded-[2rem] relative overflow-hidden shadow-sm border border-slate-100 group hover:border-blue-200 transition">
            <div class="relative z-10">
                <div class="flex items-center justify-between mb-4">
                    <div class="p-2 bg-blue-50 rounded-xl text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <span
                        class="text-[10px] uppercase text-blue-600 font-bold bg-blue-50 px-2 py-1 rounded-lg">Invested</span>
                </div>
                <h3 class="text-slate-500 text-xs font-bold uppercase">Aset Pasar (Est. IDR)</h3>
                <p class="text-2xl font-bold text-slate-800 mt-1">Rp
                    {{ number_format($rekap['nilai_investasi'], 0, ',', '.') }}</p>
            </div>
            <div
                class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-50 rounded-full blur-xl group-hover:bg-blue-100 transition">
            </div>
        </div>
    </div>
</div>

{{-- 3. SECTION CHART (PERTUMBUHAN & ALOKASI) --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8" x-data="chartHandler()">

    {{-- LINE CHART (Cashflow Trend) --}}
    <div class="lg:col-span-2 bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm relative">

        {{-- Header Chart --}}
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 gap-4">
            <div>
                <h3 class="font-bold text-slate-800 text-lg">Cashflow Trend</h3>
                <p class="text-slate-400 text-xs font-medium mt-1">Pergerakan dana masuk & keluar</p>
            </div>

            {{-- Toolbar Filter --}}
            <div class="flex flex-wrap items-center gap-2 bg-slate-50 p-1.5 rounded-xl border border-slate-100">

                {{-- Dropdown Bulan & Tahun --}}
                <div x-show="filterType === 'custom'" x-transition class="flex gap-2 mr-2">
                    <select x-model="selectedMonth" @change="fetchData('custom')"
                        class="text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg py-1.5 px-2 focus:outline-none focus:border-indigo-500 cursor-pointer">
                        @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                            {{ date('M', mktime(0, 0, 0, $m, 1)) }}</option>
                        @endforeach
                    </select>
                    <select x-model="selectedYear" @change="fetchData('custom')"
                        class="text-xs font-bold text-slate-600 bg-white border border-slate-200 rounded-lg py-1.5 px-2 focus:outline-none focus:border-indigo-500 cursor-pointer">
                        @foreach(range(date('Y'), date('Y')-3) as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Tombol Filter --}}
                <button @click="fetchData('1D')"
                    :class="filterType === '1D' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all duration-200">1D</button>
                <button @click="fetchData('1W')"
                    :class="filterType === '1W' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all duration-200">1W</button>
                <button @click="fetchData('1M')"
                    :class="filterType === '1M' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all duration-200">1M</button>
                <button @click="fetchData('1Y')"
                    :class="filterType === '1Y' ? 'bg-white text-indigo-600 shadow-sm' : 'text-slate-400 hover:text-slate-600'"
                    class="px-3 py-1.5 text-xs font-bold rounded-lg transition-all duration-200">1Y</button>

                {{-- Tombol Custom --}}
                <button @click="filterType = 'custom'; fetchData('custom')"
                    :class="filterType === 'custom' ? 'bg-indigo-100 text-indigo-600' : 'text-slate-400 hover:text-slate-600 hover:bg-white'"
                    class="w-8 h-8 flex items-center justify-center rounded-lg transition-all duration-200">
                    <i class="fas fa-calendar-alt text-xs"></i>
                </button>
            </div>
        </div>

        {{-- Loading Overlay --}}
        <div x-show="isLoading" x-transition.opacity
            class="absolute inset-0 bg-white/60 backdrop-blur-[2px] z-10 flex items-center justify-center rounded-[2rem]">
            <div class="flex flex-col items-center">
                <svg class="animate-spin h-8 w-8 text-indigo-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                <span class="text-xs font-bold text-indigo-600">Memuat...</span>
            </div>
        </div>

        {{-- Chart Canvas --}}
        <div class="w-full h-72">
            <canvas id="growthChart"></canvas>
        </div>
    </div>

    {{-- DOUGHNUT CHART (Alokasi) --}}
    <div class="bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm flex flex-col justify-between">
        <div class="flex justify-between items-center mb-4">
            <h3 class="font-bold text-slate-800 text-lg">Alokasi Aset</h3>
            <button class="text-slate-300 hover:text-indigo-600 transition"><i class="fas fa-chart-pie"></i></button>
        </div>

        <div class="relative w-full h-56 flex items-center justify-center">
            <canvas id="allocationChart"></canvas>
            <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none">
                <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total</span>
                <span class="text-2xl font-black text-slate-800">100%</span>
            </div>
        </div>

        {{-- Legend --}}
        <div class="mt-6 space-y-3">
            @php $colors = ['#fb923c', '#a855f7', '#3b82f6', '#10b981', '#f472b6', '#6366f1']; @endphp
            @foreach($chartLabels as $index => $label)
            @if($index < 3) <div class="flex justify-between items-center text-sm">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full"
                        style="background-color: {{ $colors[$index] ?? '#cbd5e1' }}"></span>
                    <span class="text-slate-600 font-medium">{{ $label }}</span>
                </div>
                <span class="font-bold text-slate-800">
                    @php
                    $total = array_sum($chartValues);
                    $val = $chartValues[$index];
                    $percent = $total > 0 ? ($val / $total) * 100 : 0;
                    @endphp
                    {{ number_format($percent, 1) }}%
                </span>
        </div>
        @endif
        @endforeach
    </div>
</div>
</div>

{{-- 4. TABEL ASET --}}
<div class="bg-white rounded-[2rem] overflow-hidden border border-slate-200 shadow-sm">
    <div class="px-8 py-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <h3 class="font-bold text-slate-800 text-lg">Portofolio Aset</h3>
        <a href="{{ route('buy') }}"
            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-indigo-200 flex items-center gap-2">
            <i class="fas fa-plus"></i> Beli Aset
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr
                    class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider border-b border-slate-100 font-bold">
                    <th class="py-5 px-8">Instrument</th>
                    <th class="py-5 px-8 text-right">Balance</th>
                    <th class="py-5 px-8 text-right">Avg. Price</th>
                    <th class="py-5 px-8 text-right">Market Value</th>
                    <th class="py-5 px-8 text-center">Profit/Loss</th>
                    <th class="py-5 px-8 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-sm">
                @forelse($detail_aset as $item)
                @php
                $isCrypto = isset($item['type']) && $item['type'] == 'Crypto';
                $currency = $isCrypto ? '$' : 'Rp';
                $decimal = $isCrypto ? 2 : 0;
                $dec_point = $isCrypto ? '.' : ',';
                $thousands_sep = $isCrypto ? ',' : '.';
                $symbol = strtoupper($item['aset']);

                // Logic Warna Icon Pastel
                $iconBg = 'bg-slate-100'; $iconColor = 'text-slate-600';
                if($symbol == 'BTC') { $iconBg = 'bg-orange-50'; $iconColor = 'text-orange-500'; }
                elseif($symbol == 'ETH') { $iconBg = 'bg-purple-50'; $iconColor = 'text-purple-500'; }
                elseif($symbol == 'USDT') { $iconBg = 'bg-emerald-50'; $iconColor = 'text-emerald-500'; }
                elseif($symbol == 'BBCA') { $iconBg = 'bg-blue-50'; $iconColor = 'text-blue-600'; }
                elseif($symbol == 'GOTO') { $iconBg = 'bg-green-50'; $iconColor = 'text-green-600'; }
                @endphp

                <tr class="hover:bg-slate-50/80 transition duration-150 group">
                    <td class="py-5 px-8">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-xl {{ $iconBg }} flex items-center justify-center font-bold text-sm {{ $iconColor }} border border-slate-100 shadow-sm">
                                {{ substr($symbol, 0, 1) }}
                            </div>
                            <div>
                                <span class="font-bold text-slate-800 block text-base">{{ $item['aset'] }}</span>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="text-xs text-slate-500">{{ $item['nama_lengkap'] }}</span>
                                    <span
                                        class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded border {{ $isCrypto ? 'bg-orange-50 border-orange-100 text-orange-500' : 'bg-blue-50 border-blue-100 text-blue-500' }}">
                                        {{ $isCrypto ? 'CRYPTO' : 'STOCK' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>
                    <td class="py-5 px-8 text-right">
                        <span
                            class="font-mono font-bold text-slate-700 block">{{ number_format($item['jumlah'], 4) }}</span>
                        <span class="text-xs text-slate-400">Unit</span>
                    </td>
                    <td class="py-5 px-8 text-right text-slate-500 font-mono text-xs">
                        {{ $currency }} {{ number_format($item['modal'], $decimal, $dec_point, $thousands_sep) }}
                    </td>
                    <td class="py-5 px-8 text-right">
                        <div class="font-bold text-slate-800 font-mono">{{ $currency }}
                            {{ number_format($item['nilai_sekarang'], $decimal, $dec_point, $thousands_sep) }}</div>
                        @if($isCrypto)
                        <div class="text-[10px] text-slate-400">≈ Rp
                            {{ number_format($item['nilai_idr'], 0, ',', '.') }}</div>
                        @endif
                    </td>
                    <td class="py-5 px-8 text-center">
                        @php
                        $persentase = ($item['modal'] > 0) ? ($item['cuan'] / $item['modal']) * 100 : 0;
                        $isProfit = $item['cuan'] >= 0;
                        @endphp
                        <div class="inline-flex flex-col items-end">
                            <span
                                class="px-2 py-1 rounded-md text-xs font-bold {{ $isProfit ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }}">
                                {{ $isProfit ? '+' : '' }}{{ number_format($persentase, 2) }}%
                            </span>
                            <span
                                class="text-[10px] mt-1 font-mono font-medium {{ $isProfit ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $isProfit ? '+' : '' }}{{ $currency }}
                                {{ number_format($item['cuan'], $decimal, $dec_point, $thousands_sep) }}
                            </span>
                        </div>
                    </td>
                    <td class="py-5 px-8 text-center">
                        <div
                            class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-200">
                            <a href="{{ route('sell', ['symbol' => $item['aset']]) }}"
                                class="p-2 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition tooltip"
                                title="Jual Aset">
                                <i class="fas fa-minus"></i>
                            </a>
                            <a href="{{ route('buy') }}"
                                class="p-2 rounded-lg bg-indigo-50 text-indigo-500 hover:bg-indigo-500 hover:text-white transition tooltip"
                                title="Beli Lagi">
                                <i class="fas fa-plus"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-slate-400">
                        <div class="flex flex-col items-center">
                            <div class="p-4 bg-slate-50 rounded-full mb-3">
                                <i class="fas fa-folder-open text-2xl text-slate-300"></i>
                            </div>
                            <p class="mb-3 font-medium text-slate-600">Portofolio kamu masih kosong.</p>
                            <a href="{{ route('buy') }}"
                                class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-bold transition shadow-md shadow-indigo-200">
                                Mulai Investasi Sekarang
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
// --- 1. DOUGHNUT CHART ---
const ctxAlloc = document.getElementById('allocationChart');
if (ctxAlloc) {
    new Chart(ctxAlloc, {
        type: 'doughnut',
        data: {
            labels: @json($chartLabels ?? []),
            datasets: [{
                data: @json($chartValues ?? []),
                backgroundColor: ['#fb923c', '#a855f7', '#3b82f6', '#10b981', '#f472b6', '#6366f1'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

// --- 2. DYNAMIC LINE CHART ---
function chartHandler() {
    return {
        filterType: '1M',
        selectedMonth: new Date().getMonth() + 1,
        selectedYear: new Date().getFullYear(),
        isLoading: false,
        chartInstance: null,

        init() {
            // Saat pertama load, panggil data 1M
            this.fetchData('1M');
        },

        async fetchData(type) {
            this.filterType = type;
            this.isLoading = true;

            try {
                let url = `{{ route('api.chart') }}?filter=${type}`;
                if (type === 'custom') {
                    url += `&month=${this.selectedMonth}&year=${this.selectedYear}`;
                }

                const response = await fetch(url);
                const data = await response.json();

                if (this.chartInstance) {
                    this.chartInstance.destroy(); // Hancurkan chart lama agar animasi ulang
                }
                this.renderChart(data.labels, data.values);

            } catch (error) {
                console.error("Gagal load chart:", error);
            } finally {
                setTimeout(() => {
                    this.isLoading = false;
                }, 300);
            }
        },

        renderChart(labels, values) {
            const ctx = document.getElementById('growthChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(99, 102, 241, 0.25)');
            gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

            this.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Cashflow',
                        data: values,
                        borderColor: '#6366f1',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#6366f1',
                        pointBorderWidth: 2,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: '#fff',
                            titleColor: '#1e293b',
                            bodyColor: '#6366f1',
                            borderColor: '#e2e8f0',
                            borderWidth: 1,
                            padding: 12,
                            callbacks: {
                                label: function(context) {
                                    return ' Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                color: '#94a3b8',
                                maxTicksLimit: 6
                            }
                        },
                        y: {
                            border: {
                                display: false
                            },
                            grid: {
                                color: '#f1f5f9'
                            },
                            ticks: {
                                color: '#cbd5e1'
                            }
                        }
                    },
                    interaction: {
                        mode: 'nearest',
                        axis: 'x',
                        intersect: false
                    }
                }
            });
        }
    }
}
</script>
@endsection