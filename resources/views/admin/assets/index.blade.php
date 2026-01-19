@extends('layouts.admin')

@section('title', 'Master Aset')

@section('content')

{{-- Alpine Data untuk Modal Manual Update --}}
<div x-data="{ 
    showModal: false, 
    editItem: { id: null, symbol: '', price: 0, type: '' },
    openModal(id, symbol, price, type) {
        this.editItem = { id, symbol, price, type };
        this.showModal = true;
    }
}">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4 mb-8">
        <div>
            <h2 class="text-3xl font-black text-white">Master Data Aset</h2>
            <p class="text-slate-400 mt-1">Kelola harga pasar dan sinkronisasi API.</p>
        </div>

        <div class="flex gap-3">
            {{-- Tombol Sync API --}}
            <form action="{{ route('admin.assets.sync') }}" method="POST">
                @csrf
                <button type="submit"
                    class="bg-slate-700 hover:bg-slate-600 text-white px-5 py-3 rounded-xl font-bold transition shadow-lg flex items-center gap-2 border border-slate-600">
                    <i class="fas fa-sync-alt {{ session('loading') ? 'animate-spin' : '' }}"></i>
                    Sync API
                </button>
            </form>

            {{-- Tombol Tambah --}}
            <a href="{{ route('admin.assets.create') }}"
                class="bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-3 rounded-xl font-bold transition shadow-lg shadow-indigo-500/20 flex items-center gap-2">
                <i class="fas fa-plus"></i> Tambah Aset
            </a>
        </div>
    </div>

    {{-- TABEL ASET --}}
    <div class="bg-slate-800 rounded-3xl border border-slate-700 shadow-xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead class="bg-slate-900/50 text-slate-400 text-xs uppercase font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Aset</th>
                        <th class="px-6 py-4">Kategori</th>
                        <th class="px-6 py-4">Sumber Harga</th>
                        <th class="px-6 py-4 text-right">Harga Saat Ini</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700 text-sm">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-slate-700/50 transition">

                        {{-- 1. ASET --}}
                        <td class="px-6 py-4">
                            <span class="block font-black text-white text-lg">{{ $asset->symbol }}</span>
                            <span class="text-slate-400">{{ $asset->name }}</span>
                        </td>

                        {{-- 2. KATEGORI --}}
                        <td class="px-6 py-4">
                            @php
                            $colors = [
                            'Stock' => 'text-blue-400 bg-blue-400/10 border-blue-400/20',
                            'Crypto' => 'text-orange-400 bg-orange-400/10 border-orange-400/20',
                            'Mutual Fund' => 'text-purple-400 bg-purple-400/10 border-purple-400/20',
                            ];
                            $cls = $colors[$asset->type] ?? 'text-slate-400 bg-slate-400/10 border-slate-400/20';
                            @endphp
                            <div class="flex flex-col items-start gap-1">
                                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $cls }}">
                                    {{ $asset->type }}
                                </span>
                                @if($asset->subtype)
                                <span
                                    class="text-[10px] text-slate-400 font-mono bg-slate-700/50 px-2 py-0.5 rounded ml-1 border border-slate-600">
                                    {{ $asset->subtype }}
                                </span>
                                @endif
                            </div>
                        </td>

                        {{-- 3. SUMBER HARGA --}}
                        <td class="px-6 py-4">
                            @if($asset->api_id)
                            <div class="flex items-center gap-2 text-emerald-400">
                                <i class="fas fa-link text-xs"></i>
                                <span class="font-mono text-xs text-slate-300">{{ $asset->api_id }}</span>
                            </div>
                            @else
                            <span class="text-xs text-orange-400 italic flex items-center gap-1">
                                <i class="fas fa-pen"></i> Manual
                            </span>
                            @endif
                        </td>

                        {{-- 4. HARGA (ADA TOMBOL UPDATE) --}}
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-3 group">
                                <div>
                                    @if($asset->type == 'Crypto')
                                    <span class="text-emerald-400 font-bold">$</span>
                                    <span
                                        class="font-mono text-white">{{ number_format($asset->current_price, 2, '.', ',') }}</span>
                                    @else
                                    <span class="text-slate-400 font-bold">Rp</span>
                                    <span
                                        class="font-mono text-white">{{ number_format($asset->current_price, 0, ',', '.') }}</span>
                                    @endif
                                </div>

                                {{-- TOMBOL TRIGGER MODAL (PENSIL) --}}
                                <button
                                    @click="openModal('{{ $asset->id }}', '{{ $asset->symbol }}', '{{ $asset->current_price }}', '{{ $asset->type }}')"
                                    class="w-8 h-8 rounded-lg bg-slate-700 text-slate-400 hover:bg-indigo-600 hover:text-white transition flex items-center justify-center shadow-sm"
                                    title="Update Harga Manual">
                                    <i class="fas fa-pencil-alt text-xs"></i>
                                </button>
                            </div>
                            <div class="text-[10px] text-slate-500 mt-1">
                                Update: {{ $asset->updated_at->diffForHumans() }}
                            </div>
                        </td>

                        {{-- 5. AKSI HAPUS --}}
                        <td class="px-6 py-4 text-center">
                            <form action="{{ route('admin.assets.destroy', $asset->id) }}" method="POST"
                                onsubmit="return confirm('Hapus aset {{ $asset->symbol }}?')">
                                @csrf @method('DELETE')
                                <button
                                    class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada data aset.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MODAL UPDATE HARGA MANUAL (OVERLAY) --}}
    <div x-show="showModal" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 backdrop-blur-sm p-4"
        x-transition.opacity>

        <div @click.away="showModal = false"
            class="bg-slate-800 border border-slate-700 w-full max-w-md rounded-2xl shadow-2xl overflow-hidden transform transition-all"
            x-transition.scale>

            <div class="p-6">
                <h3 class="text-xl font-bold text-white mb-1">Update Harga Manual</h3>
                <p class="text-slate-400 text-sm mb-6">Ubah harga pasar untuk <span class="text-indigo-400 font-bold"
                        x-text="editItem.symbol"></span>.</p>

                {{-- Form Update --}}
                {{-- Kita gunakan trik Form Action dinamis via JS --}}
                <form :action="'/admin/assets/' + editItem.id + '/update-price'" method="POST">
                    @csrf
                    @method('POST') {{-- Route updatePrice menggunakan POST di controller Anda --}}

                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Harga Baru</label>
                        <div class="relative">
                            <span class="absolute left-4 top-3.5 text-slate-400 font-bold"
                                x-text="editItem.type === 'Crypto' ? '$' : 'Rp'"></span>
                            <input type="number" step="any" name="current_price" x-model="editItem.price"
                                class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 pl-10 text-white font-mono text-lg focus:border-indigo-500 focus:outline-none transition"
                                required>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button type="button" @click="showModal = false"
                            class="w-1/2 py-3 text-slate-400 font-bold hover:bg-slate-700 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="w-1/2 bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 rounded-xl shadow-lg shadow-indigo-500/20 transition">
                            Simpan Harga
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection