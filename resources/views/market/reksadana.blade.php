@extends('layouts.app')

@section('title', 'Reksadana & ETF - MyInvest')
@section('header', 'Pasar Reksadana & ETF')
@section('header_description', 'Investasi kolektif dan Exchange Traded Funds')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Chart Section -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-lg font-semibold text-gray-900">Performa Reksadana & ETF</h2>
            <div class="flex items-center space-x-2">
                <select class="text-sm border border-gray-300 rounded-lg px-3 py-1.5 bg-white">
                    <option>1 Bulan</option>
                    <option>3 Bulan</option>
                    <option>6 Bulan</option>
                    <option>1 Tahun</option>
                </select>
            </div>
        </div>
        <div class="chart-container">
            <div id="reksadanaChart"></div>
        </div>
    </div>

    <!-- Info Panel -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Kategori Reksadana</h3>
        <div class="space-y-4">
            <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <span class="text-blue-600 font-medium">MM</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Money Market</p>
                        <p class="text-xs text-gray-500">Risiko Rendah</p>
                    </div>
                </div>
                <span class="text-sm font-medium text-green-600">+2.3%</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <span class="text-green-600 font-medium">PF</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Pendapatan Tetap</p>
                        <p class="text-xs text-gray-500">Risiko Sedang</p>
                    </div>
                </div>
                <span class="text-sm font-medium text-green-600">+4.8%</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-yellow-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <span class="text-yellow-600 font-medium">CS</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Campuran</p>
                        <p class="text-xs text-gray-500">Risiko Sedang-Tinggi</p>
                    </div>
                </div>
                <span class="text-sm font-medium text-green-600">+7.2%</span>
            </div>

            <div class="flex items-center justify-between p-3 bg-red-50 rounded-lg">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <span class="text-red-600 font-medium">SS</span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-900">Saham</p>
                        <p class="text-xs text-gray-500">Risiko Tinggi</p>
                    </div>
                </div>
                <span class="text-sm font-medium text-green-600">+12.5%</span>
            </div>
        </div>
    </div>
</div>

<!-- ETF List -->
<div class="mt-6 bg-white rounded-xl shadow-sm border border-gray-200">
    <div class="px-6 py-4 border-b border-gray-200">
        <h3 class="text-lg font-semibold text-gray-900">Daftar ETF & Reksadana</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Harga
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Perubahan
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AUM</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expense
                        Ratio</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <!-- Sample data -->
                <tr>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <span class="text-blue-600 font-medium text-sm">E</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">IDX ETF I-Grade</p>
                                <p class="text-xs text-gray-500">IDXETF001</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-900">Rp 1,250</p>
                    </td>
                    <td class="px-6 py-4">
                        <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            +2.3%
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-900">Rp 1.2T</p>
                    </td>
                    <td class="px-6 py-4">
                        <p class="text-sm text-gray-900">0.45%</p>
                    </td>
                    <td class="px-6 py-4">
                        <button
                            class="text-sm bg-blue-600 text-white px-4 py-1.5 rounded-lg hover:bg-blue-700 transition-colors">
                            Beli
                        </button>
                    </td>
                </tr>
                <!-- Add more rows as needed -->
            </tbody>
        </table>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize chart for reksadana/ETF
    var options = {
        series: [{
            name: 'Reksadana Saham',
            data: [30, 40, 35, 50, 49, 60, 70, 91, 125]
        }, {
            name: 'Reksadana Pendapatan Tetap',
            data: [23, 32, 27, 41, 32, 44, 52, 41, 32]
        }, {
            name: 'ETF IDX',
            data: [20, 25, 30, 35, 40, 45, 50, 60, 70]
        }],
        chart: {
            height: 350,
            type: 'line',
            zoom: {
                enabled: false
            },
            toolbar: {
                show: true
            }
        },
        colors: ['#3B82F6', '#10B981', '#8B5CF6'],
        stroke: {
            width: [3, 3, 3],
            curve: 'smooth'
        },
        xaxis: {
            categories: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep'],
        },
        yaxis: {
            labels: {
                formatter: function(val) {
                    return val + "%"
                }
            }
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            floating: true,
            offsetY: -25,
            offsetX: -5
        }
    };

    var chart = new ApexCharts(document.querySelector("#reksadanaChart"), options);
    chart.render();
});
</script>
@endsection