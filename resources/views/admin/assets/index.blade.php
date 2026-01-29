@extends('layouts.admin')

@section('title', 'Data ' . $type)
@section('header', 'Master Data ' . $type)

@section('content')

<div x-data="{ 
    showModal: false, 
    editItem: { id: null, symbol: '', price: 0, type: '' },
    openModal(id, symbol, price, type) {
        this.editItem = { id: id, symbol: symbol, price: price, type: type };
        this.showModal = true;
    }
}">

    {{-- HEADER DINAMIS --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-900 tracking-tight">
                @if($type == 'Stock') 🏢 Saham Indonesia
                @elseif($type == 'US Stock') 🗽 Saham Amerika
                @elseif($type == 'Mutual Fund') 📈 Reksadana
                @elseif($type == 'Crypto') ₿ Crypto Assets
                @else Data {{ $type }}
                @endif
            </h2>
            <p class="text-sm text-gray-500 mt-1">Kelola data master untuk kategori <span
                    class="font-bold">{{ $type }}</span>.</p>
        </div>

        <div class="flex items-center gap-3">
            {{-- Tombol Sync --}}
            <form action="{{ route('admin.assets.sync') }}" method="POST">
                @csrf
                <button type="submit"
                    class="bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 hover:text-black px-4 py-2.5 rounded-lg text-sm font-bold transition shadow-sm flex items-center gap-2 group">
                    <svg class="w-4 h-4 text-gray-500 group-hover:text-black transition group-hover:animate-spin"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Sync Harga
                </button>
            </form>

            {{-- Tombol Tambah --}}
            <a href="{{ route('admin.assets.create', ['type' => $type]) }}"
                class="bg-black hover:bg-gray-800 text-white px-5 py-2.5 rounded-lg text-sm font-bold transition shadow-lg flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah {{ $type == 'Mutual Fund' ? 'Reksadana' : $type }}
            </a>
        </div>
    </div>

    {{-- TABEL DATA --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead
                    class="bg-gray-50 text-gray-500 text-xs uppercase font-bold tracking-wider border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">Aset</th>
                        @if($type == 'Mutual Fund')
                        <th class="px-6 py-4">Jenis</th>
                        <th class="px-6 py-4">Manajer</th>
                        <th class="px-6 py-4">Risiko</th>
                        @endif
                        <th class="px-6 py-4">Harga Live</th>
                        <th class="px-6 py-4">Terakhir Update</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-gray-50 transition group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center font-bold text-gray-500 text-xs shrink-0 overflow-hidden border border-gray-200">
                                    @if($asset->logo_url)
                                    <img src="{{ $asset->logo_url }}" class="w-full h-full object-cover p-1"
                                        onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22%3E%3Crect width=%22100%22 height=%22100%22 fill=%22%23f3f4f6%22/%3E%3Ctext x=%2250%22 y=%2255%22 font-family=%22Arial%22 font-size=%2230%22 text-anchor=%22middle%22 fill=%229ca3af%22%3E{{ substr($asset->symbol, 0, 2) }}%3C/text%3E%3C/svg%3E'"
                                        alt="{{ $asset->symbol }} logo">
                                    @else
                                    <div
                                        class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-100 to-gray-200">
                                        <span
                                            class="text-gray-500 font-bold text-sm">{{ substr($asset->symbol, 0, 2) }}</span>
                                    </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">{{ $asset->symbol }}</div>
                                    <div class="text-xs text-gray-500 max-w-[200px] truncate">{{ $asset->name }}</div>
                                    @if($type == 'Mutual Fund' && $asset->management_fee)
                                    <div class="text-[10px] text-gray-400 font-medium mt-0.5">
                                        Fee: {{ number_format($asset->management_fee, 2) }}%
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>

                        @if($type == 'Mutual Fund')
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-1">
                                <span class="px-2.5 py-1 rounded-md text-[10px] font-bold border 
                                    @if($asset->mutual_fund_type == 'Saham') bg-red-50 text-red-700 border-red-200
                                    @elseif($asset->mutual_fund_type == 'Pasar Uang') bg-green-50 text-green-700 border-green-200
                                    @elseif($asset->mutual_fund_type == 'Pendapatan Tetap') bg-blue-50 text-blue-700 border-blue-200
                                    @elseif($asset->mutual_fund_type == 'Campuran') bg-purple-50 text-purple-700 border-purple-200
                                    @elseif($asset->mutual_fund_type == 'ETF') bg-yellow-50 text-yellow-700 border-yellow-200
                                    @else bg-gray-50 text-gray-700 border-gray-200 @endif">
                                    {{ $asset->mutual_fund_type ?? '-' }}
                                </span>
                                @if($asset->category)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-600">
                                    {{ $asset->category }}
                                </span>
                                @endif
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="text-xs">
                                <div class="font-bold text-gray-700">{{ $asset->investment_manager ?? '-' }}</div>
                                @if($asset->manager_website)
                                <a href="{{ $asset->manager_website }}" target="_blank"
                                    class="text-blue-500 hover:text-blue-700 hover:underline truncate block max-w-[150px]">
                                    {{ parse_url($asset->manager_website, PHP_URL_HOST) }}
                                </a>
                                @endif
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            @if($asset->risk_level)
                            <span class="px-2.5 py-1 rounded-md text-[10px] font-bold border 
                                @if($asset->risk_level == 'Rendah') bg-green-50 text-green-700 border-green-200
                                @elseif($asset->risk_level == 'Sedang') bg-yellow-50 text-yellow-700 border-yellow-200
                                @elseif($asset->risk_level == 'Tinggi') bg-orange-50 text-orange-700 border-orange-200
                                @elseif($asset->risk_level == 'Sangat Tinggi') bg-red-50 text-red-700 border-red-200
                                @else bg-gray-50 text-gray-700 border-gray-200 @endif">
                                {{ $asset->risk_level }}
                            </span>
                            @else
                            <span class="text-gray-400 text-xs">-</span>
                            @endif
                        </td>
                        @endif

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 group/edit">
                                <span class="font-mono font-bold text-gray-900">
                                    {{-- 🔥 LOGIKA MATA UANG 🔥 --}}
                                    {{ ($asset->type == 'Crypto' || $asset->type == 'US Stock') ? '$' : 'Rp' }}
                                    {{ number_format($asset->current_price, ($asset->type == 'Crypto' || $asset->type == 'US Stock') ? 2 : 0, ',', '.') }}
                                </span>
                                @if($type == 'Mutual Fund')
                                <div class="text-xs text-gray-500 font-medium">/unit</div>
                                @endif
                                <button
                                    @click="openModal('{{ $asset->id }}', '{{ $asset->symbol }}', '{{ $asset->current_price }}', '{{ $asset->type }}')"
                                    class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-black">
                                    <i class="fas fa-pen text-xs"></i>
                                </button>
                            </div>
                        </td>

                        <td class="px-6 py-4">
                            <div class="text-xs text-gray-500 font-medium">
                                {{ \Carbon\Carbon::parse($asset->price_updated_at)->format('d/m/Y') }}
                            </div>
                            @if($type == 'Mutual Fund' && $asset->launch_date)
                            <div class="text-[10px] text-gray-400 mt-0.5">
                                Launch: {{ \Carbon\Carbon::parse($asset->launch_date)->format('M Y') }}
                            </div>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center items-center gap-1">
                                <a href="{{ route('admin.assets.edit', $asset->id) }}"
                                    class="p-2 text-gray-400 hover:text-black" title="Edit data master">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.assets.destroy', $asset->id) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600"
                                        title="Hapus data">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @if($type == 'Mutual Fund' && $asset->notes)
                                <button type="button" onclick="showNotes('{{ $asset->name }}', `{{ $asset->notes }}`)"
                                    class="p-2 text-gray-400 hover:text-blue-600" title="Lihat catatan">
                                    <i class="fas fa-sticky-note"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ $type == 'Mutual Fund' ? 7 : 4 }}" class="px-6 py-16 text-center text-gray-400">
                            <div class="flex flex-col items-center gap-2">
                                <i class="fas fa-database text-2xl"></i>
                                <p>Belum ada data {{ $type == 'Mutual Fund' ? 'reksadana' : $type }}.</p>
                                <a href="{{ route('admin.assets.create', ['type' => $type]) }}"
                                    class="text-sm text-blue-500 hover:text-blue-700 hover:underline font-medium">
                                    + Tambah data pertama
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($assets->hasPages())
        <div class="p-4 border-t border-gray-100 bg-gray-50">
            {{ $assets->appends(['type' => $type])->links() }}
        </div>
        @endif
    </div>

    {{-- Modal untuk Update Harga --}}
    <div x-show="showModal" x-cloak
        class="fixed inset-0 bg-black bg-opacity-30 flex items-center justify-center p-4 z-50">
        <div @click.away="showModal = false" class="bg-white rounded-xl w-full max-w-md p-6 shadow-2xl">
            <h3 class="text-lg font-bold text-gray-900 mb-4">
                Update Harga <span x-text="editItem.symbol"></span>
            </h3>
            <form :action="`/admin/assets/${editItem.id}/update-price`" method="POST">
                @csrf @method('PUT')
                <input type="hidden" name="type" x-model="editItem.type">

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Harga Baru <span
                                x-text="editItem.type == 'Crypto' || editItem.type == 'US Stock' ? '($)' : '(Rp)'"></span>
                        </label>
                        <input type="number" name="current_price" x-model="editItem.price" step="0.0001" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Tanggal Update
                        </label>
                        <input type="date" name="price_updated_at" value="{{ date('Y-m-d') }}" required
                            class="w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
                    </div>
                </div>

                <div class="flex justify-end gap-3 mt-6 pt-6 border-t border-gray-100">
                    <button type="button" @click="showModal = false"
                        class="px-5 py-2.5 rounded-xl text-gray-500 hover:bg-gray-100 font-bold transition">
                        Batal
                    </button>
                    <button type="submit"
                        class="px-5 py-2.5 rounded-xl bg-black text-white font-bold hover:bg-gray-800 transition">
                        Update Harga
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal untuk Catatan --}}
<div id="notesModal" class="fixed inset-0 bg-black bg-opacity-30 hidden items-center justify-center p-4 z-50">
    <div class="bg-white rounded-xl w-full max-w-lg p-6 shadow-2xl">
        <div class="flex justify-between items-center mb-4">
            <h3 id="notesTitle" class="text-lg font-bold text-gray-900"></h3>
            <button onclick="closeNotes()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div id="notesContent" class="text-gray-700 whitespace-pre-line max-h-64 overflow-y-auto"></div>
        <div class="flex justify-end mt-6 pt-6 border-t border-gray-100">
            <button onclick="closeNotes()"
                class="px-5 py-2.5 rounded-xl text-gray-500 hover:bg-gray-100 font-bold transition">
                Tutup
            </button>
        </div>
    </div>
</div>

<script>
function showNotes(name, notes) {
    document.getElementById('notesTitle').textContent = 'Catatan: ' + name;
    document.getElementById('notesContent').textContent = notes || '(Tidak ada catatan)';
    document.getElementById('notesModal').classList.remove('hidden');
    document.getElementById('notesModal').classList.add('flex');
}

function closeNotes() {
    document.getElementById('notesModal').classList.add('hidden');
    document.getElementById('notesModal').classList.remove('flex');
}
</script>

<style>
[x-cloak] {
    display: none !important;
}
</style>
@endsection