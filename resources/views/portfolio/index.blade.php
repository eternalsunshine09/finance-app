@extends('layouts.app')

@section('title', 'Analisis Portofolio')
@section('header', '📊 Portofolio Detail')

@section('content')

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Modal Dikeluarkan</span>
        <div class="text-2xl font-bold text-gray-800 mt-1">
            Rp {{ number_format($totalModal, 0, ',', '.') }}
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nilai Aset Sekarang</span>
        <div class="text-2xl font-bold text-blue-600 mt-1">
            Rp {{ number_format($totalNilaiSekarang, 0, ',', '.') }}
        </div>
    </div>

    <div
        class="p-6 rounded-xl shadow-sm border border-gray-100 {{ $totalProfitRp >= 0 ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
        <span
            class="text-xs font-bold {{ $totalProfitRp >= 0 ? 'text-green-600' : 'text-red-600' }} uppercase tracking-wider">
            Keuntungan / Kerugian Total
        </span>
        <div class="flex items-center gap-2 mt-1">
            <span class="text-2xl font-black {{ $totalProfitRp >= 0 ? 'text-green-700' : 'text-red-700' }}">
                {{ $totalProfitRp >= 0 ? '+' : '' }} Rp {{ number_format($totalProfitRp, 0, ',', '.') }}
            </span>
            <span
                class="text-sm font-bold px-2 py-1 rounded {{ $totalProfitRp >= 0 ? 'bg-green-200 text-green-800' : 'bg-red-200 text-red-800' }}">
                {{ number_format($totalProfitPct, 2) }}%
            </span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 border-b border-gray-100 flex justify-between items-center">
            <h3 class="font-bold text-gray-700">Rincian Aset</h3>
            <span class="text-xs bg-gray-100 text-gray-500 px-2 py-1 rounded">{{ count($portfolioList) }} Aset</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="p-4">Aset</th>
                        <th class="p-4 text-right">Avg Price</th>
                        <th class="p-4 text-right">Market Price</th>
                        <th class="p-4 text-right">Nilai Total</th>
                        <th class="p-4 text-center">Profit/Loss</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($portfolioList as $item)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-4">
                            <div class="font-bold text-gray-800">{{ $item->symbol }}</div>
                            <div class="text-xs text-gray-400">{{ $item->name }}</div>
                            <div class="text-xs text-indigo-500 mt-1 font-mono">{{ number_format($item->quantity, 4) }}
                                Unit</div>
                        </td>

                        <td class="p-4 text-right text-gray-500">
                            Rp {{ number_format($item->avg_price, 0, ',', '.') }}
                        </td>

                        <td class="p-4 text-right font-medium">
                            Rp {{ number_format($item->current_price, 0, ',', '.') }}
                        </td>

                        <td class="p-4 text-right font-bold text-gray-800">
                            Rp {{ number_format($item->current_value, 0, ',', '.') }}
                        </td>

                        <td class="p-4 text-center">
                            @if($item->profit_loss_rp >= 0)
                            <div class="text-green-600 font-bold">+{{ number_format($item->profit_loss_pct, 2) }}%</div>
                            <div class="text-xs text-green-500">+Rp
                                {{ number_format($item->profit_loss_rp, 0, ',', '.') }}</div>
                            @else
                            <div class="text-red-600 font-bold">{{ number_format($item->profit_loss_pct, 2) }}%</div>
                            <div class="text-xs text-red-500">Rp {{ number_format($item->profit_loss_rp, 0, ',', '.') }}
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-400">Belum ada investasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-700 mb-6 text-center">Alokasi Aset</h3>
        <div class="h-64 relative">
            <canvas id="portfolioChart"></canvas>
        </div>

        <div class="mt-6 space-y-2">
            @foreach($portfolioList as $index => $item)
            <div class="flex justify-between text-sm">
                <span class="flex items-center gap-2">
                    <span class="w-3 h-3 rounded-full"
                        style="background-color: {{ ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'][$index % 5] }}"></span>
                    {{ $item->symbol }}
                </span>
                <span
                    class="font-bold">{{ number_format(($item->current_value / ($totalNilaiSekarang ?: 1)) * 100, 1) }}%</span>
            </div>
            @endforeach
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
// Data dari Controller
const labels = {
    !!json_encode(array_column($portfolioList, 'symbol')) !!
};
const values = {
    !!json_encode(array_column($portfolioList, 'current_value')) !!
};

const ctx = document.getElementById('portfolioChart');

if (labels.length > 0) {
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: values,
                backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6'],
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                } // Kita sudah buat legend manual di bawah
            },
            cutout: '65%'
        }
    });
}
</script>
@endsection