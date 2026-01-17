@extends('layouts.app')

@section('title', 'Dashboard Investor')
@section('header', 'Ringkasan Aset')

@section('content')

@if(Auth::user()->role == 'admin')
<div class="mb-6">
    <a href="{{ route('admin.dashboard') }}"
        class="bg-gray-800 text-yellow-400 px-4 py-2 rounded-lg font-bold hover:bg-gray-700 shadow-lg inline-flex items-center gap-2">
        👑 Masuk ke Admin Panel
    </a>
</div>
@endif

<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

    <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500 relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Uang Tunai (IDR)</h3>
                <p class="text-3xl font-black text-gray-800 mt-2">
                    Rp {{ number_format($rekap['uang_tunai'], 0, ',', '.') }}
                </p>
            </div>
            <div class="absolute right-4 top-6 text-green-100 text-6xl font-bold opacity-50">Rp</div>
        </div>

        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500 relative overflow-hidden">
            <div class="relative z-10">
                <h3 class="text-gray-500 text-xs font-bold uppercase tracking-wider">Nilai Aset Pasar</h3>
                <p class="text-3xl font-black text-gray-800 mt-2">
                    Rp {{ number_format($rekap['nilai_investasi'], 0, ',', '.') }}
                </p>
            </div>
            <div class="absolute right-4 top-6 text-blue-100 text-6xl font-bold opacity-50">📈</div>
        </div>

        <div
            class="md:col-span-2 bg-gradient-to-r from-purple-600 to-indigo-700 rounded-xl shadow-lg p-6 text-white relative">
            <h3 class="text-purple-200 text-xs font-bold uppercase tracking-wider">Total Kekayaan Bersih</h3>
            <p class="text-4xl font-black mt-2">
                Rp {{ number_format($rekap['total_kekayaan'], 0, ',', '.') }}
            </p>
            <div class="absolute right-6 top-1/2 -translate-y-1/2 text-white opacity-20 text-6xl font-bold">💎</div>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm p-6 flex flex-col items-center justify-center">
        <h3 class="text-gray-500 text-xs font-bold uppercase mb-4">Alokasi Portofolio</h3>
        <div class="w-full h-48 relative">
            <canvas id="myChart"></canvas>
        </div>
    </div>
</div>

<div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-100">
    <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
        <h3 class="font-bold text-gray-700">📊 Aset Saya</h3>
        <a href="{{ route('buy') }}" class="text-sm text-indigo-600 font-bold hover:underline">+ Beli Lagi</a>
    </div>

    <table class="w-full text-left">
        <thead>
            <tr class="bg-white text-gray-400 uppercase text-xs tracking-wider border-b border-gray-100">
                <th class="py-4 px-6 font-medium">Aset</th>
                <th class="py-4 px-6 text-right font-medium">Jumlah</th>
                <th class="py-4 px-6 text-right font-medium">Modal</th>
                <th class="py-4 px-6 text-right font-medium">Nilai Sekarang</th>
                <th class="py-4 px-6 text-center font-medium">P/L</th>
                <th class="py-4 px-6 text-center font-medium">Aksi</th>
            </tr>
        </thead>
        <tbody class="text-gray-600 text-sm">
            @forelse($detail_aset as $item)
            <tr class="hover:bg-gray-50 transition border-b border-gray-100 last:border-0">
                <td class="py-4 px-6">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center font-bold text-xs text-gray-600">
                            {{ substr($item['aset'], 0, 1) }}
                        </div>
                        <div>
                            <span class="font-bold text-gray-800 block">{{ $item['aset'] }}</span>
                            <span class="text-xs text-gray-400">{{ $item['nama_lengkap'] }}</span>
                        </div>
                    </div>
                </td>
                <td class="py-4 px-6 text-right font-mono">{{ number_format($item['jumlah'], 4) }}</td>
                <td class="py-4 px-6 text-right text-gray-400">Rp {{ number_format($item['modal'], 0, ',', '.') }}</td>
                <td class="py-4 px-6 text-right font-bold text-gray-800">Rp
                    {{ number_format($item['nilai_sekarang'], 0, ',', '.') }}</td>

                <td class="py-4 px-6 text-center">
                    @if($item['cuan'] >= 0)
                    <span class="bg-green-100 text-green-700 py-1 px-3 rounded-full text-xs font-bold">
                        +{{ number_format(($item['cuan'] / ($item['modal'] ?: 1)) * 100, 1) }}%
                    </span>
                    @else
                    <span class="bg-red-100 text-red-700 py-1 px-3 rounded-full text-xs font-bold">
                        {{ number_format(($item['cuan'] / ($item['modal'] ?: 1)) * 100, 1) }}%
                    </span>
                    @endif
                    <div class="text-xs text-gray-400 mt-1">Rp {{ number_format($item['cuan'], 0, ',', '.') }}</div>
                </td>

                <td class="py-4 px-6 text-center">
                    <a href="{{ route('sell', ['symbol' => $item['aset']]) }}"
                        class="text-indigo-600 hover:text-indigo-800 font-bold text-xs border border-indigo-200 hover:border-indigo-600 px-3 py-1 rounded transition">
                        Jual
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="py-8 text-center text-gray-400">
                    <p class="mb-2">Belum ada aset.</p>
                    <a href="{{ route('buy') }}" class="text-indigo-500 font-bold hover:underline">Mulai Investasi
                        Sekarang</a>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

@endsection

@section('scripts')
<script>
const ctx = document.getElementById('myChart');
const labels = @json($chartLabels ?? []);
const data = @json($chartValues ?? []);

if (labels.length > 0) {
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: ['#10B981', '#3B82F6', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899'],
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
                } // Sembunyikan legend biar bersih
            },
            cutout: '70%' // Biar bolong tengahnya besar (Donut Style)
        }
    });
}
</script>
@endsection