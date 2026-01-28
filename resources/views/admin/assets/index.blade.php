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
                        @if($type == 'Mutual Fund') <th class="px-6 py-4">Jenis</th> @endif
                        <th class="px-6 py-4">Harga Live</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($assets as $asset)
                    <tr class="hover:bg-gray-50 transition group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-4">
                                <div
                                    class="w-10 h-10 rounded-lg bg-gray-100 flex items-center justify-center font-bold text-gray-500 text-xs shrink-0 overflow-hidden">
                                    @if($asset->logo) <img src="{{ $asset->logo }}"
                                        class="w-full h-full object-contain"> @else {{ substr($asset->symbol, 0, 2) }}
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-gray-900">{{ $asset->symbol }}</div>
                                    <div class="text-xs text-gray-500 max-w-[200px] truncate">{{ $asset->name }}</div>
                                </div>
                            </div>
                        </td>

                        @if($type == 'Mutual Fund')
                        <td class="px-6 py-4">
                            <span
                                class="px-2.5 py-1 rounded-md text-[10px] font-bold border bg-green-50 text-green-700 border-green-200">{{ $asset->subtype ?? '-' }}</span>
                        </td>
                        @endif

                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2 group/edit">
                                <span class="font-mono font-bold text-gray-900">
                                    {{-- 🔥 LOGIKA MATA UANG 🔥 --}}
                                    {{ ($asset->type == 'Crypto' || $asset->type == 'US Stock') ? '$' : 'Rp' }}
                                    {{ number_format($asset->current_price, ($asset->type == 'Crypto' || $asset->type == 'US Stock') ? 2 : 0, ',', '.') }}
                                </span>
                                <button
                                    @click="openModal('{{ $asset->id }}', '{{ $asset->symbol }}', '{{ $asset->current_price }}', '{{ $asset->type }}')"
                                    class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-black"><i
                                        class="fas fa-pen"></i></button>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center items-center gap-1">
                                <a href="{{ route('admin.assets.edit', $asset->id) }}"
                                    class="p-2 text-gray-400 hover:text-black"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('admin.assets.destroy', $asset->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-600"><i
                                            class="fas fa-trash"></i></button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center text-gray-400">Belum ada data {{ $type }}.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($assets->hasPages()) <div class="p-4 border-t border-gray-100 bg-gray-50">
            {{ $assets->appends(['type' => $type])->links() }}</div> @endif
    </div>

    {{-- MODAL (Copy paste dari sebelumnya, tidak berubah) --}}
    {{-- ... --}}
</div>
@endsection