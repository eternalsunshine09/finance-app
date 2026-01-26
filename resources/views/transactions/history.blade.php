@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('header', 'Riwayat Transaksi')
@section('header_description', 'Rekam jejak seluruh aktivitas keuangan Anda.')

@section('content')
<div class="max-w-7xl mx-auto py-8">

    {{-- 1. HEADER & FILTER SECTION --}}
    <div class="flex flex-col md:flex-row justify-between items-end md:items-center gap-4 mb-6">

        {{-- Total Count Badge --}}
        <div class="bg-white px-5 py-3 rounded-xl border border-gray-200 shadow-sm flex items-center gap-3">
            <div class="p-2 bg-black rounded-lg text-white">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
            </div>
            <div>
                <p class="text-[10px] text-gray-500 font-bold uppercase tracking-wider">Total Data</p>
                <p class="text-lg font-black text-gray-900 leading-none">{{ $transactions->total() }}</p>
            </div>
        </div>

        {{-- Filter Dropdown --}}
        <form method="GET" action="{{ route('history') }}" class="w-full md:w-auto">
            <div class="relative group">
                <select name="wallet_id" onchange="this.form.submit()"
                    class="w-full md:w-64 appearance-none bg-white border border-gray-200 text-gray-700 py-3 px-4 pr-10 rounded-xl font-bold focus:outline-none focus:ring-1 focus:ring-black focus:border-black cursor-pointer shadow-sm transition hover:border-gray-300">
                    <option value="">Semua Dompet</option>
                    @foreach($wallets as $w)
                    <option value="{{ $w->id }}" {{ request('wallet_id') == $w->id ? 'selected' : '' }}>
                        {{ $w->account_name }} ({{ $w->bank_name }})
                    </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 text-gray-500">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </div>
            </div>
        </form>
    </div>

    {{-- 2. TABEL TRANSAKSI --}}
    <div class="bg-white shadow-sm rounded-xl overflow-hidden border border-gray-200">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr
                        class="bg-gray-50 text-gray-500 uppercase text-xs font-bold tracking-wider border-b border-gray-200">
                        <th class="p-5">Waktu</th>
                        <th class="p-5">Dompet</th>
                        <th class="p-5">Tipe</th>
                        <th class="p-5 text-right">Nominal</th>
                        <th class="p-5 text-center">Status</th>
                        <th class="p-5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm divide-y divide-gray-100">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-gray-50 transition group">

                        {{-- Waktu --}}
                        <td class="p-5 whitespace-nowrap">
                            <div class="font-bold text-gray-900">{{ $trx->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $trx->created_at->format('H:i') }}
                            </div>
                        </td>

                        {{-- Dompet --}}
                        <td class="p-5 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span
                                    class="font-bold text-gray-900">{{ $trx->wallet->account_name ?? 'Terhapus' }}</span>
                                <span
                                    class="text-[10px] text-gray-500 uppercase tracking-wide">{{ $trx->wallet->bank_name ?? '-' }}</span>
                            </div>
                        </td>

                        {{-- Tipe & Deskripsi --}}
                        <td class="p-5">
                            <div class="flex items-start gap-3">
                                {{-- Icon Type --}}
                                <div
                                    class="p-2 rounded-lg border flex-shrink-0 
                                    {{ in_array($trx->type, ['TOPUP', 'SELL']) ? 'bg-white border-gray-200 text-gray-700' : 'bg-gray-50 border-gray-200 text-gray-500' }}">
                                    @if($trx->type == 'TOPUP') <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                    @elseif($trx->type == 'WITHDRAW') <svg class="w-4 h-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    @elseif($trx->type == 'BUY') <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    @elseif($trx->type == 'SELL') <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    @endif
                                </div>

                                <div>
                                    <span
                                        class="text-xs font-bold text-gray-900 uppercase block">{{ $trx->type }}</span>
                                    <span class="text-xs text-gray-500 line-clamp-1">
                                        {{ $trx->asset_symbol ? 'Aset: '.$trx->asset_symbol : ($trx->description ?? '-') }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        {{-- Nominal --}}
                        <td class="p-5 text-right whitespace-nowrap">
                            @php
                            $isPositive = in_array($trx->type, ['TOPUP', 'SELL']);
                            // Warna Angka: Hijau/Merah agar jelas secara finansial, tapi soft
                            $colorClass = $isPositive ? 'text-green-600' : 'text-gray-900';
                            $symbol = optional($trx->wallet)->currency == 'USD' ? '$' : 'Rp';
                            @endphp
                            <span class="font-mono font-bold {{ $colorClass }}">
                                {{ $isPositive ? '+' : '-' }} {{ $symbol }}
                                {{ number_format(abs($trx->amount_cash), 2, ',', '.') }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td class="p-5 text-center">
                            @if($trx->status == 'approved' || $trx->status == 'success')
                            <div class="inline-flex justify-center items-center w-6 h-6 rounded-full bg-green-50 text-green-600 border border-green-100"
                                title="Berhasil">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            @else
                            <span
                                class="text-[10px] font-bold text-gray-400 uppercase border border-gray-200 px-2 py-1 rounded">{{ $trx->status }}</span>
                            @endif
                        </td>

                        {{-- Aksi --}}
                        <td class="p-5 text-center whitespace-nowrap">
                            <div class="flex justify-center gap-2">

                                {{-- Tombol Edit --}}
                                @if(in_array($trx->type, ['TOPUP', 'WITHDRAW']))
                                <a href="{{ route('transactions.edit', $trx->id) }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-black hover:text-white transition text-gray-500 shadow-sm"
                                    title="Edit">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                        </path>
                                    </svg>
                                </a>
                                @else
                                <div class="w-8 h-8 flex items-center justify-center rounded-lg border border-transparent bg-gray-50 text-gray-300 cursor-not-allowed"
                                    title="Edit Terkunci (Transaksi Aset)">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                        </path>
                                    </svg>
                                </div>
                                @endif

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST"
                                    onsubmit="return confirm('PERINGATAN: Menghapus transaksi akan mengembalikan saldo/aset ke kondisi semula. Lanjutkan?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition text-gray-400 shadow-sm"
                                        title="Hapus">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                        <td colspan="6" class="px-6 py-16 text-center text-gray-400 bg-white">
                            <div class="flex flex-col items-center">
                                <div class="p-4 bg-gray-50 rounded-full mb-3 border border-gray-100">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                        </path>
                                    </svg>
                                </div>
                                <p class="text-sm font-medium">Belum ada data transaksi.</p>
                                <p class="text-xs text-gray-400 mt-1">Transaksi Anda akan muncul di sini.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="p-6 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection