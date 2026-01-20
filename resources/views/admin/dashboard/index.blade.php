@extends('layouts.app')

@section('title', 'Dashboard Investor')
@section('header', 'Ringkasan Portofolio')

@section('content')

{{-- 1. TOMBOL ADMIN (Hanya jika admin) --}}
@if(Auth::user()->role == 'admin')
<div class="mb-8">
    <a href="{{ route('admin.dashboard') }}"
        class="inline-flex items-center gap-3 px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white rounded-xl font-medium transition-all duration-200 shadow-sm hover:shadow-md">
        <span class="text-lg">👑</span>
        Masuk ke Admin Panel
    </a>
</div>
@endif

{{-- 2. STATS CARDS - Minimalis --}}
<div class="grid grid-cols-1 lg:grid-cols-4 gap-6 mb-8">
    {{-- Total Kekayaan --}}
    <div class="lg:col-span-2 bg-white p-8 rounded-2xl border border-gray-200 shadow-sm">
        <div class="mb-4">
            <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Total Kekayaan Bersih</h3>
            <div class="flex items-baseline gap-1">
                <span class="text-gray-500 text-lg font-medium">Rp</span>
                <span class="text-4xl md:text-5xl font-bold text-gray-900 tracking-tight">
                    {{ number_format($rekap['total_kekayaan'], 0, ',', '.') }}
                </span>
            </div>
        </div>
        <div class="flex items-center gap-2 text-sm">
            @php
            // Tentukan warna berdasarkan nilai
            if ($growth_percentage > 0) {
            $bgColor = 'bg-emerald-100';
            $textColor = 'text-emerald-700';
            $icon = '↗';
            } elseif ($growth_percentage < 0) { $bgColor='bg-rose-100' ; $textColor='text-rose-700' ; $icon='↘' ; } else
                { $bgColor='bg-gray-100' ; $textColor='text-gray-700' ; $icon='→' ; } @endphp
                @if(!is_null($growth_percentage)) <span
                class="px-3 py-1 {{ $bgColor }} {{ $textColor }} rounded-full font-medium">
                {{ $icon }} {{ $growth_percentage >= 0 ? '+' : '' }}{{ $growth_percentage }}%
                </span>
                <span class="text-gray-500">vs bulan lalu</span>
                @else
                <span class="px-3 py-1 bg-gray-100 text-gray-500 rounded-full font-medium">
                    Data belum tersedia
                </span>
                @endif
        </div>
    </div>

    {{-- Saldo Tunai --}}
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="p-2 bg-gray-100 rounded-lg">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                    </path>
                </svg>
            </div>
        </div>
        <h3 class="text-xs font-medium text-gray-500 uppercase mb-2">Kas Tunai</h3>
        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($rekap['uang_tunai'], 0, ',', '.') }}</p>
    </div>

    {{-- Nilai Aset --}}
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex items-center justify-between mb-4">
            <div class="p-2 bg-gray-100 rounded-lg">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
        </div>
        <h3 class="text-xs font-medium text-gray-500 uppercase mb-2">Aset Investasi</h3>
        <p class="text-2xl font-bold text-gray-900">Rp {{ number_format($rekap['nilai_investasi'], 0, ',', '.') }}</p>
    </div>
</div>

{{-- 3. CHART SECTION --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8" x-data="chartHandler()">
    {{-- LINE CHART - Minimal --}}
    <div class="lg:col-span-2 bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900">Performa Portfolio</h3>
                <p class="text-sm text-gray-500">Pergerakan nilai investasi</p>
            </div>

            <div class="flex gap-1 bg-gray-100 p-1 rounded-lg">
                <button @click="fetchData('1M')"
                    :class="filterType === '1M' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors duration-200">
                    1M
                </button>
                <button @click="fetchData('3M')"
                    :class="filterType === '3M' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors duration-200">
                    3M
                </button>
                <button @click="fetchData('1Y')"
                    :class="filterType === '1Y' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                    class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors duration-200">
                    1Y
                </button>
            </div>
        </div>

        <div x-show="isLoading" class="flex justify-center items-center h-64">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-gray-900"></div>
        </div>

        <div x-show="!isLoading" class="h-64">
            <canvas id="growthChart"></canvas>
        </div>
    </div>

    {{-- PIE CHART - Alokasi Aset --}}
    <div class="bg-white p-6 rounded-xl border border-gray-200 shadow-sm">
        <h3 class="text-lg font-semibold text-gray-900 mb-6">Alokasi Aset</h3>

        <div class="relative h-44 mb-6">
            <canvas id="allocationChart"></canvas>
        </div>

        <div class="space-y-3">
            @php
            $colors = ['#374151', '#6B7280', '#9CA3AF', '#D1D5DB', '#F3F4F6'];
            $total = array_sum($chartValues);
            @endphp

            @foreach($chartLabels as $index => $label)
            @if($index < 3) <div class="flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full" style="background-color: {{ $colors[$index] }}"></span>
                    <span class="text-sm text-gray-700">{{ $label }}</span>
                </div>
                <span class="text-sm font-semibold text-gray-900">
                    @php
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

{{-- 4. TABEL ASET - Minimalis --}}
<div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex justify-between items-center">
            <h3 class="text-lg font-semibold text-gray-900">Portofolio Aset</h3>
            <a href="{{ route('buy') }}"
                class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                + Beli Aset
            </a>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wider font-medium">
                    <th class="py-3 px-6 text-left">Instrument</th>
                    <th class="py-3 px-6 text-right">Balance</th>
                    <th class="py-3 px-6 text-right">Avg. Price</th>
                    <th class="py-3 px-6 text-right">Market Value</th>
                    <th class="py-3 px-6 text-right">P/L</th>
                    <th class="py-3 px-6 text-center">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($detail_aset as $item)
                @php
                $isCrypto = isset($item['type']) && $item['type'] == 'Crypto';
                $currency = $isCrypto ? '$' : 'Rp';
                $decimal = $isCrypto ? 2 : 0;
                $dec_point = $isCrypto ? '.' : ',';
                $thousands_sep = $isCrypto ? ',' : '.';
                $symbol = strtoupper($item['aset']);
                $profitClass = $item['cuan'] >= 0 ? 'text-emerald-600' : 'text-rose-600';
                @endphp

                <tr class="hover:bg-gray-50 transition-colors duration-150">
                    <td class="py-4 px-6">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-lg bg-gray-100 flex items-center justify-center font-medium text-gray-700">
                                {{ substr($symbol, 0, 1) }}
                            </div>
                            <div>
                                <span class="font-medium text-gray-900 block">{{ $item['aset'] }}</span>
                                <span class="text-xs text-gray-500">{{ $item['nama_lengkap'] }}</span>
                            </div>
                        </div>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <span class="font-mono text-gray-900">{{ number_format($item['jumlah'], 4) }}</span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <span class="text-gray-700 font-mono">
                            {{ $currency }} {{ number_format($item['modal'], $decimal, $dec_point, $thousands_sep) }}
                        </span>
                    </td>
                    <td class="py-4 px-6 text-right">
                        <div class="font-medium text-gray-900 font-mono">
                            {{ $currency }}
                            {{ number_format($item['nilai_sekarang'], $decimal, $dec_point, $thousands_sep) }}
                        </div>
                    </td>
                    <td class="py-4 px-6 text-right">
                        @php
                        $persentase = ($item['modal'] > 0) ? ($item['cuan'] / $item['modal']) * 100 : 0;
                        @endphp
                        <span class="{{ $profitClass }} font-medium">
                            {{ $item['cuan'] >= 0 ? '+' : '' }}{{ number_format($persentase, 1) }}%
                        </span>
                    </td>
                    <td class="py-4 px-6 text-center">
                        <div class="flex justify-center gap-1">
                            <a href="{{ route('sell', ['symbol' => $item['aset']]) }}"
                                class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </a>
                            <a href="{{ route('buy') }}"
                                class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-300 mb-4" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                            <p class="text-gray-500 mb-3">Portofolio kamu masih kosong.</p>
                            <a href="{{ route('buy') }}"
                                class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-lg text-sm font-medium transition-colors duration-200">
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
                backgroundColor: ['#374151', '#6B7280', '#9CA3AF', '#D1D5DB', '#F3F4F6'],
                borderWidth: 0,
                hoverOffset: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

// --- 2. LINE CHART HANDLER ---
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

                if (this.chartInstance) {
                    this.chartInstance.destroy();
                }
                this.renderChart(data.labels, data.values);

            } catch (error) {
                console.error("Gagal load chart:", error);
            } finally {
                this.isLoading = false;
            }
        },

        renderChart(labels, values) {
            const ctx = document.getElementById('growthChart').getContext('2d');

            this.chartInstance = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        borderColor: '#374151',
                        backgroundColor: 'rgba(55, 65, 81, 0.05)',
                        borderWidth: 2,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#374151',
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
                            backgroundColor: '#ffffff',
                            titleColor: '#111827',
                            bodyColor: '#374151',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
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
                                color: '#6b7280'
                            }
                        },
                        y: {
                            grid: {
                                color: '#f3f4f6'
                            },
                            ticks: {
                                color: '#6b7280'
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