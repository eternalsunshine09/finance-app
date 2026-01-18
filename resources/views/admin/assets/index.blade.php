@extends('layouts.admin')

@section('title', 'Master Aset')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4 mb-8">
    <div>
        <h2 class="text-3xl font-black text-white">Master Data Aset</h2>
        <p class="text-slate-400 mt-1">Kelola daftar saham, crypto, dan instrumen investasi lainnya.</p>
    </div>

    <div class="flex gap-3">
        {{-- Tombol Sync API --}}
        <form action="{{ route('admin.assets.sync') }}" method="POST">
            @csrf
            <button type="submit"
                class="bg-slate-700 hover:bg-slate-600 text-white px-5 py-3 rounded-xl font-bold transition shadow-lg shadow-slate-900/50 flex items-center gap-2 border border-slate-600">
                <i class="fas fa-sync-alt {{ session('loading') ? 'animate-spin' : '' }}"></i>
                Sync Harga Live
            </button>
        </form>

        {{-- Tombol Tambah Aset --}}
        <a href="{{ route('admin.assets.create') }}"
            class="bg-indigo-600 hover:bg-indigo-500 text-white px-6 py-3 rounded-xl font-bold transition shadow-lg shadow-indigo-500/20 flex items-center gap-2">
            <i class="fas fa-plus"></i> Tambah Aset
        </a>
    </div>
</div>

{{-- Tabel Data Aset (Full Width) --}}
<div class="bg-slate-800 rounded-3xl border border-slate-700 shadow-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-900/50 text-slate-400 text-xs uppercase font-bold tracking-wider">
                <tr>
                    <th class="px-6 py-4">Aset</th>
                    <th class="px-6 py-4">Kategori</th>
                    <th class="px-6 py-4">API ID</th>
                    <th class="px-6 py-4 text-right">Harga Saat Ini</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700 text-sm">
                @forelse($assets as $asset)
                <tr class="hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4">
                        <span class="block font-black text-white text-lg">{{ $asset->symbol }}</span>
                        <span class="text-slate-400">{{ $asset->name }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                        $colors = [
                        'Stock' => 'text-blue-400 bg-blue-400/10 border-blue-400/20',
                        'Crypto' => 'text-orange-400 bg-orange-400/10 border-orange-400/20',
                        'Currency' => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20',
                        'Gold' => 'text-yellow-400 bg-yellow-400/10 border-yellow-400/20',
                        'Mutual Fund' => 'text-purple-400 bg-purple-400/10 border-purple-400/20',
                        ];
                        $cls = $colors[$asset->type] ?? 'text-slate-400 bg-slate-400/10 border-slate-400/20';
                        @endphp

                        <div class="flex flex-col items-start gap-1">
                            <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $cls }}">
                                {{ $asset->type }}
                            </span>

                            {{-- 🔥 Tampilkan Subtype jika ada --}}
                            @if($asset->subtype)
                            <span
                                class="text-[10px] text-slate-400 font-mono bg-slate-700/50 px-2 py-0.5 rounded ml-1 border border-slate-600">
                                {{ $asset->subtype }}
                            </span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-slate-500 font-mono text-xs">
                        {{ $asset->api_id ?? '-' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
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
                    </td>
                    <td class="px-6 py-4 text-center">
                        <form action="{{ route('admin.assets.destroy', $asset->id) }}" method="POST"
                            onsubmit="return confirm('Hapus aset {{ $asset->symbol }}?')">
                            @csrf @method('DELETE')
                            <button
                                class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition"
                                title="Hapus">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-slate-500">Belum ada data aset. Silakan tambah
                        baru.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection