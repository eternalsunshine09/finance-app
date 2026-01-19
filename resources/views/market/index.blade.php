@extends('layouts.app')

@section('title', 'Pasar Aset')
@section('header', '💱 Exchange Rate (Pasar)')

@section('content')

<div class="bg-white p-4 rounded-xl shadow-sm border border-gray-100 mb-6 flex items-center gap-4">
    <span class="text-2xl">🔍</span>
    <input type="text" id="searchInput" placeholder="Cari nama aset atau simbol (misal: BTC, BCA)..."
        class="w-full text-lg border-none focus:ring-0 text-gray-700 placeholder-gray-400 h-10">
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold border-b border-gray-100">
            <tr>
                <th class="p-5">Nama Aset</th>
                <th class="p-5 text-center">Tipe</th>
                <th class="p-5 text-right">Harga Saat Ini (IDR)</th>
                <th class="p-5 text-center">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100" id="assetTable">
            @forelse($assets as $asset)
            <tr class="hover:bg-gray-50 transition asset-row">

                <td class="p-5">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md
                                {{ $asset->type == 'crypto' ? 'bg-orange-500' : 'bg-blue-600' }}">
                            {{ substr($asset->symbol, 0, 1) }}
                        </div>
                        <div>
                            <span class="block font-bold text-gray-800 text-lg symbol-text">{{ $asset->symbol }}</span>
                            <span class="text-sm text-gray-500 name-text">{{ $asset->name }}</span>
                        </div>
                    </div>
                </td>

                <td class="p-5 text-center">
                    @if($asset->type == 'crypto')
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-orange-100 text-orange-700">CRYPTO</span>
                    @else
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-700">STOCK</span>
                    @endif
                </td>

                <td class="p-5 text-right">
                    <span class="block font-mono text-lg font-bold text-gray-800">
                        Rp {{ number_format($asset->current_price, 0, ',', '.') }}
                    </span>
                    @if($asset->api_id)
                    <span class="text-[10px] text-green-600 font-bold bg-green-100 px-1 rounded">● LIVE API</span>
                    @else
                    <span class="text-[10px] text-gray-400">Manual</span>
                    @endif
                </td>

                <td class="p-5 text-center">
                    <a href="{{ route('buy') }}?asset={{ $asset->symbol }}"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-bold text-sm shadow transition inline-block">
                        Beli
                    </a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="p-8 text-center text-gray-400">Belum ada aset terdaftar.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div id="noResult" class="hidden p-8 text-center text-gray-400">
        Aset tidak ditemukan 😔
    </div>
</div>

@endsection

@section('scripts')
<script>
// FITUR PENCARIAN INSTAN (JAVASCRIPT)
const searchInput = document.getElementById('searchInput');
const tableRows = document.querySelectorAll('.asset-row');
const noResult = document.getElementById('noResult');

searchInput.addEventListener('keyup', function() {
    const query = this.value.toLowerCase();
    let visibleCount = 0;

    tableRows.forEach(row => {
        const symbol = row.querySelector('.symbol-text').innerText.toLowerCase();
        const name = row.querySelector('.name-text').innerText.toLowerCase();

        if (symbol.includes(query) || name.includes(query)) {
            row.style.display = ''; // Tampilkan
            visibleCount++;
        } else {
            row.style.display = 'none'; // Sembunyikan
        }
    });

    // Tampilkan pesan kosong jika tidak ada hasil
    if (visibleCount === 0) {
        noResult.classList.remove('hidden');
    } else {
        noResult.classList.add('hidden');
    }
});
</script>
@endsection