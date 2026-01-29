@extends('layouts.app')

@section('title', 'Dashboard Investor')
@section('header', 'Ringkasan Portofolio')

@section('content')

{{-- 1. HEADER & QUICK ACTIONS --}}
<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-10">
    <div>
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight">Halo, {{ Auth::user()->name }}! 👋</h1>
        <p class="text-gray-500 mt-1">Berikut adalah ringkasan performa investasi Anda hari ini.</p>
    </div>

    <div class="flex flex-wrap gap-3">
        @if(Auth::user()->role == 'admin')
        <a href="{{ route('admin.dashboard') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-indigo-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                </path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
            </svg>
            Admin Panel
        </a>
        @endif
        <a href="{{ route('buy') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-black hover:bg-gray-800 text-white rounded-xl font-bold text-sm transition-all shadow-lg shadow-gray-200">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6">
                </path>
            </svg>
            Investasi Baru
        </a>
    </div>
</div>

{{-- 2. HERO STATS --}}
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-10">
    {{-- Total Kekayaan (Prominent) --}}
    <div
        class="lg:col-span-2 relative overflow-hidden bg-gradient-to-br from-gray-900 to-black p-8 rounded-[2rem] shadow-2xl shadow-gray-300">
        <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full -mr-16 -mt-16 blur-3xl"></div>

        <h3 class="text-gray-400 text-xs font-black uppercase tracking-[0.2em] mb-4">Total Kekayaan Bersih</h3>
        <div class="flex items-baseline gap-2 mb-6">
            <span class="text-indigo-400 text-xl font-bold font-mono">Rp</span>
            <span class="text-4xl md:text-5xl font-black text-white tracking-tighter">
                {{ number_format($rekap['total_kekayaan'], 0, ',', '.') }}
            </span>
        </div>

        <div class="inline-flex items-center gap-2">
            @php
            $isPositive = ($growth_percentage ?? 0) >= 0;
            $colorClass = $isPositive ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400';
            @endphp
            @if(!is_null($growth_percentage))
            <span class="px-3 py-1.5 {{ $colorClass }} rounded-lg font-bold text-xs flex items-center gap-1">
                {{ $isPositive ? '↗' : '↘' }} {{ $growth_percentage }}%
            </span>
            <span class="text-gray-500 text-xs font-medium">dibandingkan bulan lalu</span>
            @else
            <span class="text-gray-500 text-xs">Menunggu data pertumbuhan...</span>
            @endif
        </div>
    </div>

    {{-- Kas Tunai --}}
    <div
        class="bg-white p-7 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-100/50 flex flex-col justify-between">
        <div>
            <div class="w-10 h-10 bg-orange-50 text-orange-600 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
            </div>
            <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-wider">Kas Tunai</h3>
            <p class="text-2xl font-black text-gray-900 mt-1">Rp {{ number_format($rekap['uang_tunai'], 0, ',', '.') }}
            </p>
        </div>
        <div class="mt-4">
            <a href="{{ route('wallet.index') }}"
                class="text-xs font-bold text-indigo-600 hover:text-indigo-800 transition">Detail Wallet →</a>
        </div>
    </div>

    {{-- Aset Investasi --}}
    <div
        class="bg-white p-7 rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-100/50 flex flex-col justify-between">
        <div>
            <div class="w-10 h-10 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-4">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
            <h3 class="text-gray-400 text-[10px] font-black uppercase tracking-wider">Aset Investasi</h3>
            <p class="text-2xl font-black text-gray-900 mt-1">Rp
                {{ number_format($rekap['nilai_investasi'], 0, ',', '.') }}</p>
        </div>
        <div class="mt-4 text-xs text-gray-400 font-medium">
            Tersebar di {{ count($detail_aset) }} Instrumen
        </div>
    </div>
</div>

{{-- 3. ANALYTICS SECTION --}}
{{-- 3. ANALYTICS SECTION --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-10" x-data="chartHandler()">
    {{-- LINE CHART --}}
    <div class="lg:col-span-2 bg-white p-8 rounded-[2rem] border border-gray-100 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h3 class="text-xl font-black text-gray-900">Performa Portfolio</h3>
                <p class="text-sm text-gray-500 font-medium">Visualisasi pertumbuhan aset Anda.</p>
            </div>

            {{-- Timeframe Buttons - Lebih Banyak Pilihan --}}
            <div class="flex gap-1 bg-gray-50 p-1.5 rounded-xl border border-gray-100 overflow-x-auto max-w-full">
                @foreach(['1W', '1M', '3M', '6M', '1Y', 'ALL'] as $period)
                <button @click="fetchData('{{ $period }}')"
                    :class="filterType === '{{ $period }}' ? 'bg-black text-white shadow-md' : 'text-gray-400 hover:text-black'"
                    class="px-3 py-2 text-[10px] font-black rounded-lg transition-all duration-300 min-w-[40px]">
                    {{ $period }}
                </button>
                @endforeach
            </div>
        </div>

        <div class="relative h-72">
            <div x-show="isLoading" class="absolute inset-0 flex justify-center items-center bg-white/50 z-10">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            </div>
            <canvas id="growthChart"></canvas>
        </div>
    </div>

    {{-- (Bagian Alokasi Aset tetap sama seperti sebelumnya) --}}
</div>

{{-- Script Update --}}
<script>
function chartHandler() {
    return {
        filterType: '1M',
        isLoading: false,
        chartInstance: null,

        init() {
            this.fetchData('1M');
        },

        async fetchData(type) {
            this.filterType = type;
            this.isLoading = true;
            try {
                const response = await fetch(`{{ route('api.chart') }}?filter=${type}`);
                const data = await response.json();
                if (this.chartInstance) this.chartInstance.destroy();
                this.renderChart(data.labels, data.values);
            } catch (error) {
                console.error("Gagal load chart:", error);
            } finally {
                this.isLoading = false;
            }
        },

        renderChart(labels, values) {
            const ctx = document.getElementById('growthChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.1)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

            this.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels, // Label waktu dari backend
                    datasets: [{
                        data: values,
                        borderColor: '#4F46E5',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointRadius: 2, // Munculkan titik kecil agar mudah dibaca
                        pointBackgroundColor: '#4F46E5',
                        fill: true,
                        tension: 0.3
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
                            backgroundColor: '#111827',
                            padding: 12,
                            cornerRadius: 10,
                            displayColors: false,
                            callbacks: {
                                label: (context) => 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed
                                    .y)
                            }
                        }
                    },
                    scales: {
                        x: {
                            display: true, // Pastikan muncul
                            grid: {
                                display: false
                            },
                            ticks: {
                                autoSkip: true, // Melewatkan label jika terlalu rapat
                                maxRotation: 0,
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                },
                                color: '#9CA3AF'
                            }
                        },
                        y: {
                            display: true,
                            border: {
                                dash: [5, 5]
                            },
                            grid: {
                                color: '#F3F4F6'
                            },
                            ticks: {
                                font: {
                                    size: 10,
                                    weight: 'bold'
                                },
                                color: '#9CA3AF',
                                callback: (value) => 'Rp' + value.toLocaleString('id-ID')
                            }
                        }
                    }
                }
            });
        }
    }
}
</script>

{{-- 4. ASSET TABLE --}}
<div class="bg-white rounded-[2rem] border border-gray-100 shadow-xl shadow-gray-100/50 overflow-hidden">
    <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-white">
        <h3 class="text-xl font-black text-gray-900 tracking-tight">Portfolio Aset</h3>
        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Update Terakhir:
            {{ now()->format('H:i') }}</span>
    </div>

    <div class="overflow-x-auto px-4 pb-4">
        <table class="w-full">
            <thead>
                <tr class="text-[10px] text-gray-400 uppercase font-black tracking-[0.15em]">
                    <th class="py-5 px-6 text-left">Instrumen</th>
                    <th class="py-5 px-6 text-right">Saldo Unit</th>
                    <th class="py-5 px-6 text-right">Harga Rata-Rata</th>
                    <th class="py-5 px-6 text-right">Nilai Pasar</th>
                    <th class="py-5 px-6 text-right">P/L (%)</th>
                    <th class="py-5 px-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($detail_aset as $item)
                @php
                $isCrypto = isset($item['type']) && $item['type'] == 'Crypto';
                $isPositive = $item['cuan'] >= 0;
                $symbol = strtoupper($item['aset']);
                @endphp
                <tr class="hover:bg-gray-50/50 transition-all group">
                    <td class="py-5 px-6">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-11 h-11 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center font-black text-gray-400 group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-all duration-300">
                                {{ substr($symbol, 0, 1) }}
                            </div>
                            <div>
                                <span class="font-bold text-gray-900 block tracking-tight">{{ $item['aset'] }}</span>
                                <span
                                    class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ $item['nama_lengkap'] }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-5 px-6 text-right">
                        <span
                            class="font-mono text-sm font-bold text-gray-900">{{ number_format($item['jumlah'], 4) }}</span>
                    </td>
                    <td class="py-5 px-6 text-right">
                        <span class="text-xs font-bold text-gray-500 font-mono">
                            Rp {{ number_format($item['modal'], 0, ',', '.') }}
                        </span>
                    </td>
                    <td class="py-5 px-6 text-right">
                        <div class="font-black text-gray-900 font-mono">
                            Rp {{ number_format($item['nilai_sekarang'], 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="py-5 px-6 text-right">
                        @php
                        $persentase = ($item['modal'] > 0) ? ($item['cuan'] / $item['modal']) * 100 : 0;
                        @endphp
                        <div
                            class="inline-flex items-center px-2.5 py-1 rounded-lg {{ $isPositive ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} text-xs font-black">
                            {{ $isPositive ? '▲' : '▼' }} {{ number_format(abs($persentase), 1) }}%
                        </div>
                    </td>
                    <td class="py-5 px-6 text-center">
                        <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <a href="{{ route('sell', ['symbol' => $item['aset']]) }}"
                                class="p-2 bg-rose-50 text-rose-600 rounded-lg hover:bg-rose-600 hover:text-white transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4">
                                    </path>
                                </svg>
                            </a>
                            <a href="{{ route('buy', ['symbol' => $item['aset']]) }}"
                                class="p-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                {{-- Empty state (Sama seperti sebelumnya) --}}
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Logic Chart tetap sama, hanya styling chart diperhalus di bagian renderChart
function chartHandler() {
    return {
        filterType: '1M',
        isLoading: false,
        chartInstance: null,

        init() {
            this.fetchData('1M');
        },

        async fetchData(type) {
            this.filterType = type;
            this.isLoading = true;
            try {
                const response = await fetch(`{{ route('api.chart') }}?filter=${type}`);
                const data = await response.json();
                if (this.chartInstance) this.chartInstance.destroy();
                this.renderChart(data.labels, data.values);
            } catch (error) {
                console.error("Gagal load chart:", error);
            } finally {
                this.isLoading = false;
            }
        },

        renderChart(labels, values) {
            const ctx = document.getElementById('growthChart').getContext('2d');
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.15)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0)');

            this.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        borderColor: '#4F46E5',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#4F46E5',
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
                            backgroundColor: '#111827',
                            padding: 12,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            cornerRadius: 10,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    weight: 'bold'
                                },
                                color: '#9CA3AF'
                            }
                        },
                        y: {
                            border: {
                                dash: [5, 5]
                            },
                            grid: {
                                color: '#F3F4F6'
                            },
                            ticks: {
                                font: {
                                    weight: 'bold'
                                },
                                color: '#9CA3AF'
                            }
                        }
                    }
                }
            });
        }
    }
}

// Doughnut Chart styling
const ctxAlloc = document.getElementById('allocationChart');
if (ctxAlloc) {
    new Chart(ctxAlloc, {
        type: 'doughnut',
        data: {
            labels: @json($chartLabels ?? []),
            datasets: [{
                data: @json($chartValues ?? []),
                backgroundColor: ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#6366F1'],
                borderWidth: 5,
                borderColor: '#ffffff',
                hoverOffset: 20
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '80%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}
</script>
@endsection