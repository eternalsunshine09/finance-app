@extends('layouts.app')

@section('title', 'Reksadana & ETF - MyInvest')
@section('header', 'Pasar Reksadana')
@section('header_description', 'Investasi kolektif terjangkau dan dikelola manajer investasi.')

@section('content')

{{-- INFO CARDS (Tetap Sama) --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase">Total Produk</p>
                <h3 class="text-xl font-black text-gray-900">{{ $funds->count() }} Produk</h3>
            </div>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-green-50 flex items-center justify-center text-green-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase">Harga Terendah</p>
                <h3 class="text-xl font-black text-gray-900">
                    Rp {{ number_format($funds->min('current_price') ?? 0, 0, ',', '.') }}
                </h3>
            </div>
        </div>
    </div>
    <div class="bg-white p-5 rounded-2xl border border-gray-100 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-purple-50 flex items-center justify-center text-purple-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                    </path>
                </svg>
            </div>
            <div>
                <p class="text-xs text-gray-500 font-bold uppercase">Jenis Terbanyak</p>
                <h3 class="text-xl font-black text-gray-900">
                    {{ $funds->mode('subtype')[0] ?? '-' }}
                </h3>
            </div>
        </div>
    </div>
</div>

{{-- 2. TABEL INTERAKTIF (SEARCH & FILTER) --}}
{{-- Kita bungkus dengan Alpine.js untuk fitur pencarian --}}
<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ 
        search: '', 
        activeTab: 'all',
        filterData(name, symbol, subtype) {
            const query = this.search.toLowerCase();
            const matchesSearch = name.toLowerCase().includes(query) || symbol.toLowerCase().includes(query);
            const matchesTab = this.activeTab === 'all' || subtype === this.activeTab;
            return matchesSearch && matchesTab;
        }
     }">

    {{-- Header Tabel & Search --}}
    <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
        <h3 class="text-lg font-bold text-gray-900">Daftar Produk Investasi</h3>
        <div class="relative w-full md:w-72">
            {{-- Input Search terikat dengan x-model="search" --}}
            <input type="text" x-model="search" placeholder="Cari nama reksadana..."
                class="w-full pl-10 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-black focus:border-black outline-none transition placeholder-gray-400">
            <svg class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>
    </div>

    {{-- Tabs Filter Kategori --}}
    <div class="flex border-b border-gray-100 px-6 overflow-x-auto no-scrollbar gap-6">
        @foreach(['all' => 'Semua', 'RDPU' => 'Pasar Uang', 'RDPT' => 'Pendapatan Tetap', 'RDS' => 'Saham', 'Campuran'
        => 'Campuran'] as $key => $label)
        <button @click="activeTab = '{{ $key }}'"
            :class="activeTab === '{{ $key }}' ? 'text-black border-black' : 'text-gray-500 border-transparent hover:text-gray-900 hover:border-gray-200'"
            class="px-1 py-3 text-sm font-bold border-b-2 transition whitespace-nowrap">
            {{ $label }}
        </button>
        @endforeach
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold tracking-wider">
                <tr>
                    <th class="px-6 py-4 text-left">Produk</th>
                    <th class="px-6 py-4 text-left">Kategori</th>
                    <th class="px-6 py-4 text-right">NAV / Unit</th>
                    <th class="px-6 py-4 text-right">Update Terakhir</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 bg-white text-sm">
                @forelse($funds as $fund)
                {{-- Row Tabel dengan Logika Filter AlpineJS --}}
                <tr class="hover:bg-gray-50 transition group"
                    x-show="filterData('{{ $fund->name }}', '{{ $fund->symbol }}', '{{ $fund->subtype }}')"
                    x-transition>

                    {{-- Kolom Produk --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div
                                class="w-10 h-10 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center font-bold text-gray-500 text-xs mr-3 overflow-hidden">
                                @if($fund->logo)
                                <img src="{{ $fund->logo }}" class="w-full h-full object-contain p-1">
                                @else
                                {{ substr($fund->symbol, 0, 2) }}
                                @endif
                            </div>
                            <div>
                                <p class="font-bold text-gray-900">{{ $fund->name }}</p>
                                <p class="text-xs text-gray-500 font-mono">{{ $fund->symbol }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Kolom Kategori (Warna Warni) --}}
                    <td class="px-6 py-4">
                        @php
                        $typeColors = [
                        'RDPU' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                        'RDPT' => 'bg-green-50 text-green-700 border-green-200',
                        'RDS' => 'bg-red-50 text-red-700 border-red-200',
                        'Campuran' => 'bg-blue-50 text-blue-700 border-blue-200',
                        ];
                        $colorClass = $typeColors[$fund->subtype] ?? 'bg-gray-50 text-gray-600 border-gray-200';

                        $labelMap = [
                        'RDPU' => 'Pasar Uang',
                        'RDPT' => 'Pendapatan Tetap',
                        'RDS' => 'Saham',
                        'Campuran' => 'Campuran',
                        ];
                        $label = $labelMap[$fund->subtype] ?? ($fund->subtype ?: 'Reksadana');
                        @endphp

                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold border {{ $colorClass }}">
                            {{ $label }}
                        </span>
                    </td>

                    {{-- Kolom Harga --}}
                    <td class="px-6 py-4 text-right font-mono font-bold text-gray-900 text-base">
                        Rp {{ number_format($fund->current_price, 2, ',', '.') }}
                    </td>

                    {{-- Kolom Update --}}
                    <td class="px-6 py-4 text-right text-gray-500 text-xs">
                        {{ $fund->updated_at->diffForHumans() }}
                    </td>

                    {{-- Kolom Aksi --}}
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('buy', ['symbol' => $fund->symbol]) }}"
                            class="bg-black hover:bg-gray-800 text-white px-5 py-2 rounded-lg text-xs font-bold transition shadow-sm inline-flex items-center gap-1">
                            Beli
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                            <p>Belum ada data reksadana.</p>
                            <p class="text-xs mt-1">Silakan tambahkan data di menu Admin.</p>
                        </div>
                    </td>
                </tr>
                @endforelse

                {{-- Pesan jika pencarian tidak ditemukan --}}
                <tr x-show="!$el.parentNode.querySelectorAll('tr[x-show=\'true\']').length" style="display: none;">
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 text-sm">
                        Produk tidak ditemukan untuk filter ini.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection