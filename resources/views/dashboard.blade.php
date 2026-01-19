@extends('layouts.app')

@section('title', 'Dashboard Investor')
@section('header', 'Ringkasan Portofolio')

@section('content')

{{-- 1. TOMBOL ADMIN (Hanya muncul jika admin) --}}
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

    {{-- Total Kekayaan (Kartu Utama - Gradient Modern) --}}
    <div class="md:col-span-1 relative group">
        <div
            class="absolute inset-0 bg-gradient-to-r from-indigo-400 to-purple-400 rounded-2xl blur opacity-20 group-hover:opacity-40 transition duration-500">
        </div>

        <div
            class="relative bg-white border border-slate-100 p-6 rounded-2xl h-full flex flex-col justify-between overflow-hidden shadow-sm hover:shadow-md transition-shadow">
            <div>
                <h3 class="text-slate-500 text-xs font-bold uppercase tracking-widest mb-2">Total Kekayaan Bersih</h3>
                <div class="flex items-baseline gap-1">
                    <span class="text-slate-500 text-lg font-medium">Rp</span>
                    <span class="text-3xl md:text-4xl font-extrabold text-slate-800 tracking-tight">
                        {{ number_format($rekap['total_kekayaan'], 0, ',', '.') }}
                    </span>
                </div>
                {{-- Badge Kenaikan --}}
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

    {{-- Kartu Pecahan (Cash & Invest) --}}
    <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">

        {{-- Saldo Tunai --}}
        <div
            class="bg-white p-6 rounded-2xl relative overflow-hidden shadow-sm border border-slate-100 group hover:border-emerald-200 transition">
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
                <p class="text-2xl font-bold text-slate-800 mt-1">
                    Rp {{ number_format($rekap['uang_tunai'], 0, ',', '.') }}
                </p>
            </div>
            {{-- Hiasan Background --}}
            <div
                class="absolute -right-6 -bottom-6 w-24 h-24 bg-emerald-50 rounded-full blur-xl group-hover:bg-emerald-100 transition">
            </div>
        </div>

        {{-- Nilai Aset Investasi --}}
        <div
            class="bg-white p-6 rounded-2xl relative overflow-hidden shadow-sm border border-slate-100 group hover:border-blue-200 transition">
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
                <p class="text-2xl font-bold text-slate-800 mt-1">
                    Rp {{ number_format($rekap['nilai_investasi'], 0, ',', '.') }}
                </p>
            </div>
            <div
                class="absolute -right-6 -bottom-6 w-24 h-24 bg-blue-50 rounded-full blur-xl group-hover:bg-blue-100 transition">
            </div>
        </div>

    </div>
</div>

{{-- 3. SECTION CHART (PERTUMBUHAN & ALOKASI) --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    {{-- LINE CHART (Pertumbuhan Portfolio) --}}
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <span class="w-1.5 h-6 bg-indigo-500 rounded-full"></span>
                Pertumbuhan Portfolio
            </h3>
            <div class="flex bg-slate-100 rounded-lg p-1">
                <button class="px-3 py-1 text-xs font-bold text-indigo-600 bg-white rounded shadow-sm">1B</button>
                <button class="px-3 py-1 text-xs font-bold text-slate-500 hover:text-slate-800">3B</button>
                <button class="px-3 py-1 text-xs font-bold text-slate-500 hover:text-slate-800">YTD</button>
            </div>
        </div>
        <div class="w-full h-64">
            <canvas id="growthChart"></canvas>
        </div>
    </div>

    {{-- DOUGHNUT CHART (Alokasi) --}}
    <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm flex flex-col items-center justify-center">
        <h3 class="font-bold text-slate-800 mb-4 w-full text-left flex items-center gap-2">
            <span class="w-1.5 h-6 bg-purple-500 rounded-full"></span>
            Alokasi Aset
        </h3>
        <div class="w-full h-56 relative">
            <canvas id="allocationChart"></canvas>
            {{-- Text Tengah Donut --}}
            <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="text-center">
                    <span class="text-xs text-slate-400 block font-medium">Total</span>
                    <span class="text-lg font-bold text-slate-800">100%</span>
                </div>
            </div>
        </div>
        {{-- Legend Placeholder (Otak atik via JS jika perlu) --}}
        <div class="mt-4 flex flex-wrap gap-2 justify-center"></div>
    </div>
</div>

{{-- 4. TABEL ASET (Clean Light Version) --}}
<div class="bg-white rounded-2xl overflow-hidden border border-slate-200 shadow-sm">
    <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
        <h3 class="font-bold text-slate-800 text-lg">Portofolio Aset</h3>
        <a href="{{ route('buy') }}"
            class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition shadow-lg shadow-indigo-200 flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Beli Aset
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead>
                <tr
                    class="bg-slate-50 text-slate-500 uppercase text-xs tracking-wider border-b border-slate-100 font-semibold">
                    <th class="py-4 px-6">Instrument</th>
                    <th class="py-4 px-6 text-right">Balance</th>
                    <th class="py-4 px-6 text-right">Avg. Price</th>
                    <th class="py-4 px-6 text-right">Market Value</th>
                    <th class="py-4 px-6 text-center">Profit/Loss</th>
                    <th class="py-4 px-6 text-center">Action</th>
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
                $iconBg = 'bg-slate-100';
                $iconColor = 'text-slate-600';

                if($symbol == 'BTC') { $iconBg = 'bg-orange-50'; $iconColor = 'text-orange-500'; }
                elseif($symbol == 'ETH') { $iconBg = 'bg-purple-50'; $iconColor = 'text-purple-500'; }
                elseif($symbol == 'USDT') { $iconBg = 'bg-emerald-50'; $iconColor = 'text-emerald-500'; }
                elseif($symbol == 'BBCA') { $iconBg = 'bg-blue-50'; $iconColor = 'text-blue-600'; }
                elseif($symbol == 'GOTO') { $iconBg = 'bg-green-50'; $iconColor = 'text-green-600'; }
                @endphp

                <tr class="hover:bg-slate-50 transition duration-150 group">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-xl {{ $iconBg }} flex items-center justify-center font-bold text-sm {{ $iconColor }} border border-slate-100">
                                {{ substr($symbol, 0, 1) }}
                            </div>
                            <div>
                                <span class="font-bold text-slate-800 block text-base">{{ $item['aset'] }}</span>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs text-slate-500">{{ $item['nama_lengkap'] }}</span>
                                    <span
                                        class="text-[10px] uppercase font-bold px-1.5 py-0.5 rounded border {{ $isCrypto ? 'bg-orange-50 border-orange-100 text-orange-500' : 'bg-blue-50 border-blue-100 text-blue-500' }}">
                                        {{ $isCrypto ? 'CRYPTO' : 'STOCK' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Jumlah Unit --}}
                    <td class="py-4 px-6 text-right">
                        <span
                            class="font-mono font-medium text-slate-700">{{ number_format($item['jumlah'], 4) }}</span>
                        <span class="text-xs text-slate-400 ml-1">Unit</span>
                    </td>

                    {{-- Modal --}}
                    <td class="py-4 px-6 text-right text-slate-500 font-mono text-xs">
                        {{ $currency }} {{ number_format($item['modal'], $decimal, $dec_point, $thousands_sep) }}
                    </td>

                    {{-- Nilai Sekarang --}}
                    <td class="py-4 px-6 text-right">
                        <div class="font-bold text-slate-800 font-mono">
                            {{ $currency }}
                            {{ number_format($item['nilai_sekarang'], $decimal, $dec_point, $thousands_sep) }}
                        </div>
                        @if($isCrypto)
                        <div class="text-[10px] text-slate-400">
                            ≈ Rp {{ number_format($item['nilai_idr'], 0, ',', '.') }}
                        </div>
                        @endif
                    </td>

                    {{-- Profit Loss --}}
                    <td class="py-4 px-6 text-center">
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

                    {{-- Aksi --}}
                    <td class="py-4 px-6 text-center">
                        <div class="flex justify-center gap-2 opacity-60 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('sell', ['symbol' => $item['aset']]) }}"
                                class="p-2 rounded-lg bg-rose-50 text-rose-500 hover:bg-rose-500 hover:text-white transition tooltip"
                                title="Jual Aset">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </a>
                            <a href="{{ route('buy') }}"
                                class="p-2 rounded-lg bg-indigo-50 text-indigo-500 hover:bg-indigo-500 hover:text-white transition tooltip"
                                title="Beli Lagi">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-slate-400">
                        <div class="flex flex-col items-center">
                            <div class="p-4 bg-slate-50 rounded-full mb-3">
                                <svg class="w-8 h-8 text-slate-300" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M20 12H4"></path>
                                </svg>
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
{{-- Script Chart telah disesuaikan warnanya ke Tema Indigo/Pastel --}}
<script>
// --- 1. DOUGHNUT CHART (ALOKASI) ---
const ctxAlloc = document.getElementById('allocationChart');
// Ambil Data dengan aman
const labels = @json($chartLabels ?? []);
const dataValues = @json($chartValues ?? []);

if (labels.length > 0) {
    new Chart(ctxAlloc, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: dataValues,
                backgroundColor: [
                    '#fb923c', // Orange (BTC) - Pastel
                    '#a855f7', // Purple (ETH) - Pastel
                    '#3b82f6', // Blue (Stock)
                    '#10b981', // Emerald (Cash)
                    '#f472b6', // Pink
                    '#6366f1' // Indigo
                ],
                borderWidth: 2,
                borderColor: '#ffffff', // Border putih agar menyatu dengan background
                hoverOffset: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '75%',
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
                    padding: 12,
                    boxPadding: 4,
                    displayColors: true,
                    usePointStyle: true,
                }
            }
        }
    });
}

// --- 2. LINE CHART (REAL DATA) ---
const ctxGrowth = document.getElementById('growthChart').getContext('2d');

// Gradient Warna Indigo untuk Chart
const gradient = ctxGrowth.createLinearGradient(0, 0, 0, 400);
gradient.addColorStop(0, 'rgba(99, 102, 241, 0.4)'); // Indigo pudar
gradient.addColorStop(1, 'rgba(99, 102, 241, 0)'); // Transparent

const historyLabels = @json($chartLabels);
const historyValues = @json($chartValues);

new Chart(ctxGrowth, {
    type: 'line',
    data: {
        labels: historyLabels.length ? historyLabels : ['Hari Ini'],
        datasets: [{
            label: 'Total Aset',
            data: historyValues.length ? historyValues : [{
                {
                    $rekap['total_kekayaan']
                }
            }],
            borderColor: '#6366f1', // Indigo Primary
            backgroundColor: gradient,
            borderWidth: 3,
            pointBackgroundColor: '#fff',
            pointBorderColor: '#6366f1',
            pointBorderWidth: 2,
            pointRadius: 4,
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
                padding: 10,
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        label += new Intl.NumberFormat('id-ID', {
                            style: 'currency',
                            currency: 'IDR'
                        }).format(context.parsed.y);
                        return label;
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
                    color: '#94a3b8', // Slate-400
                    maxTicksLimit: 7
                }
            },
            y: {
                grid: {
                    color: '#f1f5f9', // Slate-100 (Garis halus)
                    borderDash: [5, 5]
                },
                ticks: {
                    display: false
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
</script>
@endsection