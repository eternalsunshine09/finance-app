@extends('layouts.admin')

@section('title', 'Master Aset')

@section('content')

{{-- Alpine Data Modal (Sama seperti sebelumnya) --}}
<div x-data="{ 
    showModal: false, 
    editItem: { id: null, symbol: '', price: 0, type: '' },
    openModal(id, symbol, price, type) {
        this.editItem = { id, symbol, price, type };
        this.showModal = true;
    }
}">

    {{-- HEADER & ACTIONS --}}
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-black text-white">Master Data Aset</h2>
            <p class="text-slate-400 mt-1">Kelola daftar aset, harga, dan integrasi API.</p>
        </div>

        <div class="flex flex-wrap gap-3">
            <form action="{{ route('admin.assets.sync') }}" method="POST">
                @csrf
                <button type="submit"
                    class="bg-slate-700 hover:bg-slate-600 text-white px-5 py-3 rounded-xl font-bold transition shadow-lg flex items-center gap-2 border border-slate-600">
                    <i class="fas fa-sync-alt {{ session('loading') ? 'animate-spin' : '' }}"></i> Sync API
                </button>
            </form>
            <a href="{{ route('admin.assets.create') }}"
                class="bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-3 rounded-xl font-bold transition shadow-lg shadow-indigo-500/20 flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Aset
            </a>
        </div>
    </div>

    {{-- SEARCH & FILTER BAR --}}
    <div
        class="bg-slate-800 rounded-2xl p-4 mb-6 border border-slate-700 shadow-lg flex flex-col md:flex-row gap-4 justify-between items-center">

        {{-- Filter Kategori (Tabs Style - Updated) --}}
        <div class="flex overflow-x-auto pb-2 md:pb-0 gap-2 w-full md:w-auto no-scrollbar">

            {{-- Tombol "Semua" --}}
            <a href="{{ route('admin.assets.index') }}"
                class="px-4 py-2 rounded-lg text-sm font-bold whitespace-nowrap transition {{ !request('type') ? 'bg-indigo-600 text-white shadow-md' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' }}">
                Semua
            </a>

            {{-- Daftar Kategori --}}
            @php
            $categories = [
            'Stock' => ['label' => 'Saham', 'color' => 'blue'],
            'Crypto' => ['label' => 'Crypto', 'color' => 'orange'],
            'Mutual Fund' => ['label' => 'Reksadana', 'color' => 'purple'],
            'Gold' => ['label' => 'Emas', 'color' => 'yellow'], // Tambahan
            'Currency' => ['label' => 'Forex', 'color' => 'emerald'], // Tambahan
            'Bond' => ['label' => 'Obligasi', 'color' => 'pink'], // Ide Tambahan
            ];
            $currentType = request('type');
            @endphp

            @foreach($categories as $key => $val)
            <a href="{{ route('admin.assets.index', ['type' => $key]) }}"
                class="px-4 py-2 rounded-lg text-sm font-bold whitespace-nowrap transition {{ $currentType == $key ? 'bg-'.$val['color'].'-600 text-white shadow-md' : 'bg-slate-700 text-slate-300 hover:bg-slate-600' }}">
                {{ $val['label'] }}
            </a>
            @endforeach

        </div>

        {{-- Search Input --}}
        <form action="{{ route('admin.assets.index') }}" method="GET" class="w-full md:w-1/3 relative">
            @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari Simbol atau Nama..."
                class="w-full bg-slate-900 border border-slate-600 rounded-xl pl-10 pr-4 py-2.5 text-white focus:outline-none focus:border-indigo-500 transition">
            <div class="absolute left-3 top-3 text-slate-500"><i class="fas fa-search"></i></div>
        </form>
    </div>

    {{-- TABEL ASET --}}
    <div class="bg-slate-800 rounded-3xl border border-slate-700 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-900/50 text-slate-400 text-xs uppercase font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Aset</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Harga Live</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700 text-sm">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-slate-700/50 transition group">

                        {{-- ASET --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-12 h-12 rounded-xl flex-shrink-0 overflow-hidden border border-slate-700 bg-slate-900 flex items-center justify-center">
                                    @if($asset->logo)
                                    <img src="{{ $asset->logo }}" class="w-full h-full object-cover">
                                    @else
                                    <span
                                        class="font-black text-slate-500 text-lg">{{ substr($asset->symbol, 0, 1) }}</span>
                                    @endif
                                </div>
                                <div>
                                    <span class="block font-black text-white text-lg">{{ $asset->symbol }}</span>
                                    <span class="text-slate-400 text-xs">{{ Str::limit($asset->name, 20) }}</span>
                                </div>
                            </div>
                        </td>

                        {{-- KATEGORI --}}
                        <td class="px-6 py-4">
                            @php
                            $colors = [
                            'Stock' => 'text-blue-400 bg-blue-400/10 border-blue-400/20',
                            'Crypto' => 'text-orange-400 bg-orange-400/10 border-orange-400/20',
                            'Mutual Fund' => 'text-purple-400 bg-purple-400/10 border-purple-400/20',
                            ];
                            $cls = $colors[$asset->type] ?? 'text-slate-400 bg-slate-400/10 border-slate-400/20';
                            @endphp
                            <span
                                class="px-3 py-1 rounded-full text-xs font-bold border {{ $cls }}">{{ $asset->type }}</span>
                            @if($asset->subtype)
                            <span
                                class="text-[10px] text-slate-400 ml-1 bg-slate-900 px-2 py-0.5 rounded">{{ $asset->subtype }}</span>
                            @endif
                        </td>

                        {{-- HARGA --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <span
                                        class="text-slate-400 text-xs font-bold mr-1">{{ $asset->type == 'Crypto' ? '$' : 'Rp' }}</span>
                                    <span
                                        class="font-mono text-white text-base">{{ number_format($asset->current_price, $asset->type == 'Crypto' ? 2 : 0) }}</span>
                                    <div class="text-[10px] text-slate-500">{{ $asset->updated_at->diffForHumans() }}
                                    </div>
                                </div>

                                {{-- Tombol Quick Update Harga (Pensil Kecil) --}}
                                <button
                                    @click="openModal('{{ $asset->id }}', '{{ $asset->symbol }}', '{{ $asset->current_price }}', '{{ $asset->type }}')"
                                    class="w-8 h-8 rounded-lg bg-slate-700 text-slate-400 hover:bg-emerald-600 hover:text-white transition flex items-center justify-center"
                                    title="Quick Update Harga">
                                    <i class="fas fa-tag text-xs"></i>
                                </button>
                            </div>
                        </td>

                        {{-- AKSI BUTTONS --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">

                                {{-- EDIT FULL DATA (Kuning) --}}
                                <a href="{{ route('admin.assets.edit', $asset->id) }}"
                                    class="w-9 h-9 rounded-xl flex items-center justify-center bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-white border border-amber-500/20 transition"
                                    title="Edit Data Lengkap (Logo, Nama, dll)">
                                    <i class="fas fa-edit"></i>
                                </a>

                                {{-- HAPUS (Merah) --}}
                                <form action="{{ route('admin.assets.destroy', $asset->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus aset {{ $asset->symbol }}?')">
                                    @csrf @method('DELETE')
                                    <button
                                        class="w-9 h-9 rounded-xl flex items-center justify-center bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white border border-rose-500/20 transition">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                            Tidak ada data aset ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Link --}}
        <div class="p-4 border-t border-slate-700">
            {{ $assets->links() }}
        </div>
    </div>

    {{-- MODAL QUICK UPDATE HARGA --}}
    <div x-show="showModal" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4"
        x-transition.opacity>
        <div @click.away="showModal = false"
            class="bg-slate-800 border border-slate-700 w-full max-w-md rounded-2xl shadow-2xl p-6" x-transition.scale>
            <h3 class="text-xl font-bold text-white mb-4">Quick Update Harga</h3>
            <form :action="'/admin/assets/' + editItem.id + '/update-price'" method="POST">
                @csrf @method('POST')
                <div class="mb-6 relative">
                    <span class="absolute left-4 top-3.5 text-slate-400 font-bold"
                        x-text="editItem.type === 'Crypto' ? '$' : 'Rp'"></span>
                    <input type="number" step="any" name="current_price" x-model="editItem.price"
                        class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 pl-10 text-white font-mono text-lg focus:border-indigo-500 focus:outline-none transition">
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="showModal = false"
                        class="w-1/2 py-3 text-slate-400 font-bold hover:bg-slate-700 rounded-xl transition">Batal</button>
                    <button type="submit"
                        class="w-1/2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 rounded-xl shadow-lg transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection