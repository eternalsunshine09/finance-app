@extends('layouts.app')

@section('title', 'Laporan Keuangan')
@section('header', 'Laporan & Analisis')
@section('header_description', 'Tinjauan menyeluruh performa investasi dan arus kas Anda.')

@section('content')

{{-- TOP BAR --}}
<div class="flex justify-end mb-6">
    <a href="{{ route('report.export') }}"
        class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-all">
        <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
        </svg>
        Download CSV
    </a>
</div>

{{-- 1. SUMMARY CARDS --}}
<div class="mb-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        {{-- Realized --}}
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 uppercase font-semibold">Realized Profit</p>
            <p class="text-xl font-bold {{ $realizedProfit >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                Rp {{ number_format($realizedProfit, 0, ',', '.') }}
            </p>
        </div>
        {{-- Unrealized --}}
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 uppercase font-semibold">Unrealized Profit</p>
            <p class="text-xl font-bold {{ $unrealizedProfit >= 0 ? 'text-blue-600' : 'text-orange-600' }}">
                Rp {{ number_format($unrealizedProfit, 0, ',', '.') }}
            </p>
        </div>
        {{-- Dividen --}}
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-500 uppercase font-semibold">Total Dividen</p>
            <p class="text-xl font-bold text-purple-600">
                Rp {{ number_format($totalDividends, 0, ',', '.') }}
            </p>
        </div>
        {{-- Net Worth --}}
        <div class="bg-black p-4 rounded-xl border border-gray-900 shadow-sm text-white">
            <p class="text-xs text-gray-400 uppercase font-semibold">Total Nilai Aset</p>
            <p class="text-xl font-bold">
                Rp {{ number_format($totalCurrentValue, 0, ',', '.') }}
            </p>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- 2. ARUS KAS --}}
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h3 class="font-bold text-gray-900 mb-4">Arus Kas (Bulan Ini)</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Uang Masuk</span>
                    <span class="font-bold text-green-600">+ Rp {{ number_format($moneyIn, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-500">Uang Keluar</span>
                    <span class="font-bold text-red-600">- Rp {{ number_format($moneyOut, 0, ',', '.') }}</span>
                </div>
                <div class="border-t border-gray-100 pt-2 flex justify-between">
                    <span class="text-sm font-medium">Net Flow</span>
                    <span class="font-black text-gray-900">Rp
                        {{ number_format($moneyIn - $moneyOut, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Allocation Chart --}}
        <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
            <h3 class="font-bold text-gray-900 mb-4">Alokasi Aset</h3>
            @if($allocation->isEmpty())
            <p class="text-center text-gray-400 text-sm py-10">Belum ada aset.</p>
            @else
            <div class="relative h-48">
                <canvas id="allocationChart"></canvas>
            </div>
            <div class="mt-4 space-y-2">
                @foreach($allocation as $type => $val)
                <div class="flex justify-between text-xs">
                    <span class="font-medium text-gray-600">{{ $type }}</span>
                    <span class="font-bold">Rp {{ number_format($val, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>

    {{-- 3. GRAFIK TREN --}}
    <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 p-6 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-bold text-gray-900">Tren Transaksi</h3>
            <div class="flex bg-gray-100 p-1 rounded-lg gap-1">
                @foreach(['1W', '1M', '1Y', 'ALL'] as $f)
                <button onclick="updateChart('{{$f}}')"
                    class="filter-btn px-3 py-1 text-xs font-medium rounded text-gray-500 hover:text-black transition"
                    data-filter="{{$f}}">{{$f}}</button>
                @endforeach
            </div>
        </div>
        <div id="trendChart" style="min-height: 350px;"></div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

{{-- 
    SOLUSI: Kita proses datanya di blok PHP ini.
    Formatter tidak akan merusak ini karena berada di luar tag <script> 
--}}
@php
$chartLabels = $allocation->keys();
$chartData = $allocation->values();
@endphp

<script>
document.addEventListener('DOMContentLoaded', function() {

    // --- 1. PIE CHART (ALOKASI ASET) ---
    // Sekarang kita tinggal panggil variabel yang sudah disiapkan di atas
    const allocLabels = @json($chartLabels);
    const allocData = @json($chartData);
    const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6'];

    if (allocData.length > 0) {
        const ctx = document.getElementById('allocationChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: allocLabels,
                datasets: [{
                    data: allocData,
                    backgroundColor: colors,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += new Intl.NumberFormat('id-ID', {
                                    style: 'currency',
                                    currency: 'IDR'
                                }).format(context.raw);
                                return label;
                            }
                        }
                    }
                }
            }
        });
    } else {
        const chartContainer = document.getElementById('allocationChart').parentElement;
        chartContainer.innerHTML =
            '<div class="flex items-center justify-center h-full text-gray-400 text-xs">Belum ada aset</div>';
    }

    // --- 2. BAR CHART (TREN TRANSAKSI) ---
    const trendOptions = {
        series: [],
        chart: {
            type: 'bar',
            height: 350,
            toolbar: {
                show: false
            },
            fontFamily: 'Inter, sans-serif'
        },
        colors: ['#10b981', '#ef4444'],
        plotOptions: {
            bar: {
                borderRadius: 4,
                columnWidth: '55%'
            }
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            show: true,
            width: 2,
            colors: ['transparent']
        },
        xaxis: {
            categories: [],
            labels: {
                style: {
                    fontSize: '11px',
                    colors: '#64748b'
                }
            }
        },
        yaxis: {
            labels: {
                style: {
                    colors: '#64748b'
                },
                formatter: (val) => {
                    if (val >= 1000000) return (val / 1000000).toFixed(1) + 'Jt';
                    if (val >= 1000) return (val / 1000).toFixed(0) + 'Rb';
                    return val;
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(val) {
                    return "Rp " + new Intl.NumberFormat('id-ID').format(val)
                }
            }
        },
        noData: {
            text: 'Memuat Data...',
            style: {
                color: '#94a3b8',
                fontSize: '14px'
            }
        }
    };

    window.trendChart = new ApexCharts(document.querySelector("#trendChart"), trendOptions);
    window.trendChart.render();

    updateChart('1M');
});

function updateChart(filter) {
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('bg-white', 'text-black', 'shadow-sm');
        btn.classList.add('text-gray-500', 'hover:text-black');
        if (btn.dataset.filter === filter) {
            btn.classList.remove('text-gray-500', 'hover:text-black');
            btn.classList.add('bg-white', 'text-black', 'shadow-sm');
        }
    });

    fetch(`{{ route('report.data') }}?filter=${filter}`)
        .then(response => response.json())
        .then(data => {
            window.trendChart.updateOptions({
                xaxis: {
                    categories: data.labels
                }
            });
            window.trendChart.updateSeries([{
                    name: 'Uang Masuk (Sell)',
                    data: data.sell
                },
                {
                    name: 'Uang Keluar (Buy)',
                    data: data.buy
                }
            ]);
        })
        .catch(error => console.error('Gagal memuat data grafik:', error));
}
</script>
@endsection