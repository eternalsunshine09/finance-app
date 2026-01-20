@extends('layouts.admin')

@section('title', 'Master Aset')
@section('header', 'Master Data Aset')

@section('content')

{{-- Alpine Data Modal --}}
<div x-data="{ 
    showModal: false, 
    editItem: { id: null, symbol: '', price: 0, type: '' },
    openModal(id, symbol, price, type) {
        this.editItem = { id, symbol, price, type };
        this.showModal = true;
    }
}">

    {{-- HEADER & ACTIONS --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h2 class="text-xl font-bold text-gray-900">Daftar Aset Investasi</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola daftar saham, crypto, dan instrumen lainnya.</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <form action="{{ route('admin.assets.sync') }}" method="POST">
                @csrf
                <button type="submit"
                    class="bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 px-4 py-2.5 rounded-lg text-sm font-medium transition shadow-sm flex items-center gap-2">
                    <svg class="w-4 h-4 {{ session('loading') ? 'animate-spin' : '' }}" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                        </path>
                    </svg>
                    Sync API
                </button>
            </form>
            <a href="{{ route('admin.assets.create') }}"
                class="bg-black hover:bg-gray-800 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition shadow-sm flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Tambah Aset
            </a>
        </div>
    </div>

    {{-- SEARCH & FILTER BAR --}}
    <div
        class="bg-white rounded-xl p-4 mb-6 border border-gray-200 shadow-sm flex flex-col md:flex-row gap-4 justify-between items-center">

        {{-- Filter Kategori --}}
        <div class="flex overflow-x-auto pb-2 md:pb-0 gap-2 w-full md:w-auto no-scrollbar">

            {{-- Tombol "Semua" --}}
            <a href="{{ route('admin.assets.index') }}"
                class="px-4 py-2 rounded-md text-sm font-medium whitespace-nowrap transition border
                {{ !request('type') ? 'bg-black text-white border-black' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                Semua
            </a>

            @php
            $categories = [
            'Stock' => 'Saham',
            'Crypto' => 'Crypto',
            'Mutual Fund' => 'Reksadana',
            'Gold' => 'Emas',
            'Currency' => 'Forex',
            ];
            $currentType = request('type');
            @endphp

            @foreach($categories as $key => $label)
            <a href="{{ route('admin.assets.index', ['type' => $key]) }}"
                class="px-4 py-2 rounded-md text-sm font-medium whitespace-nowrap transition border
                {{ $currentType == $key ? 'bg-black text-white border-black' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                {{ $label }}
            </a>
            @endforeach

        </div>

        {{-- Search Input --}}
        <form action="{{ route('admin.assets.index') }}" method="GET" class="w-full md:w-1/3 relative">
            @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Simbol atau Nama..."
                class="w-full bg-gray-50 border border-gray-300 rounded-lg pl-10 pr-4 py-2 text-sm text-gray-900 focus:outline-none focus:border-black focus:ring-black transition placeholder-gray-400">
            <div class="absolute left-3 top-2.5 text-gray-400">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </form>
    </div>

    {{-- TABEL ASET --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead
                    class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold tracking-wider border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">Aset</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Harga Live</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-gray-50 transition group">

                        {{-- ASET --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-10 h-10 rounded-lg flex-shrink-0 overflow-hidden border border-gray-200 bg-white flex items-center justify-center">
                                    @if($asset->logo)
                                    <img src="{{ $asset->logo }}" class="w-full h-full object-cover">
                                    @else
                                    <span
                                        class="font-bold text-gray-400 text-sm">{{ substr($asset->symbol, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="block font-bold text-gray-900 text-base">{{ $asset->symbol }}</span>
                                    <span class="text-gray-500 text-xs">{{ Str::limit($asset->name, 25) }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- KATEGORI --}}
                        <td class="px-6 py-4">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                {{ $asset->type }}
                            </span>
                            @if($asset->subtype)
                            <span
                                class="text-[10px] text-gray-400 ml-1 bg-gray-50 px-2 py-0.5 rounded border border-gray-100">{{ $asset->subtype }}</span>
                            @endif
                        </td>

                        {{-- HARGA --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div>
                                    <span
                                        class="text-gray-400 text-xs font-medium mr-0.5">{{ $asset->type == 'Crypto' ? '$' : 'Rp' }}</span>
                                    <span
                                        class="font-mono text-gray-900 font-bold text-base">{{ number_format($asset->current_price, $asset->type == 'Crypto' ? 2 : 0) }}</span>
                                    <div class="text-[10px] text-gray-400 mt-0.5">Updated:
                                        {{ $asset->updated_at->diffForHumans() }}</div>
                                </div>

                                {{-- Tombol Quick Update Harga --}}
                                <button
                                    @click="openModal('{{ $asset->id }}', '{{ $asset->symbol }}', '{{ $asset->current_price }}', '{{ $asset->type }}')"
                                    class="w-7 h-7 rounded-md bg-gray-100 text-gray-400 hover:bg-black hover:text-white transition flex items-center justify-center border border-gray-200"
                                    title="Quick Update Harga">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </td>

                        {{-- AKSI BUTTONS --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                {{-- EDIT FULL --}}
                                <a href="{{ route('admin.assets.edit', $asset->id) }}"
                                    class="w-8 h-8 rounded-md flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-black transition border border-transparent hover:border-gray-200"
                                    title="Edit Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </a>

                                {{-- HAPUS --}}
                                <form action="{{ route('admin.assets.destroy', $asset->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus aset {{ $asset->symbol }}?')">
                                    @csrf @method('DELETE')
                                    <button
                                        class="w-8 h-8 rounded-md flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-600 transition border border-transparent hover:border-red-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center">
                                <div class="p-3 bg-gray-50 rounded-full mb-3">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                        </path>
                                    </svg>
                                </div>
                                <p class="text-sm">Tidak ada data aset ditemukan.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Link --}}
        <div class="p-4 border-t border-gray-200">
            {{ $assets->links() }}
        </div>
    </div>

    {{-- MODAL QUICK UPDATE HARGA --}}
    <div x-show="showModal" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        x-transition.opacity>
        <div @click.away="showModal = false"
            class="bg-white border border-gray-200 w-full max-w-md rounded-xl shadow-2xl p-6" x-transition.scale>

            <h3 class="text-lg font-bold text-gray-900 mb-4">Update Harga Manual</h3>

            <form :action="'/admin/assets/' + editItem.id + '/update-price'" method="POST">
                @csrf @method('POST')

                <div class="mb-6">
                    <label class="block text-xs font-semibold text-gray-500 uppercase mb-2">Harga Saat Ini</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3 text-gray-500 font-bold text-sm"
                            x-text="editItem.type === 'Crypto' ? '$' : 'Rp'"></span>
                        <input type="number" step="any" name="current_price" x-model="editItem.price"
                            class="w-full bg-gray-50 border border-gray-300 rounded-lg px-4 py-2.5 pl-10 text-gray-900 font-mono text-lg focus:border-black focus:ring-black focus:outline-none transition">
                    </div>
                </div>

                <div class="flex gap-3">
                    <button type="button" @click="showModal = false"
                        class="w-1/2 py-2.5 text-gray-600 font-medium hover:bg-gray-100 rounded-lg transition border border-gray-200">Batal</button>
                    <button type="submit"
                        class="w-1/2 bg-black hover:bg-gray-800 text-white font-bold py-2.5 rounded-lg shadow-sm transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection