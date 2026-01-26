@extends('layouts.app')
@section('title', 'Detail Dompet')

@section('content')
<div class="max-w-5xl mx-auto py-8">

    {{-- Tombol Kembali --}}
    <div class="flex justify-between items-center mb-6">
        <a href="{{ route('wallet.index') }}"
            class="inline-flex items-center gap-2 text-gray-500 hover:text-black font-bold text-sm transition group">
            <div
                class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center group-hover:bg-gray-200 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </div>
            Kembali ke Daftar
        </a>
    </div>

    {{-- Info Card Dompet --}}
    <div
        class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 mb-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 relative overflow-hidden">
        <div
            class="absolute top-0 right-0 w-48 h-48 bg-gray-50 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none">
        </div>

        <div class="relative z-10">
            <div class="flex items-center gap-3 mb-2">
                <span
                    class="bg-black text-white px-3 py-1 rounded-md text-xs font-bold uppercase tracking-wider">{{ $wallet->bank_name }}</span>
                <span
                    class="text-xs font-mono text-gray-500 bg-gray-100 px-2 py-1 rounded border border-gray-200">{{ $wallet->account_number ?? '-' }}</span>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">{{ $wallet->account_name }}</h1>
            <p class="text-gray-500 text-sm mt-1">Mata Uang: <span
                    class="font-bold text-black">{{ $wallet->currency }}</span></p>
        </div>

        <div class="text-left md:text-right relative z-10">
            <p class="text-xs text-gray-400 font-bold uppercase tracking-widest mb-1">Sisa Saldo</p>
            <h2 class="text-4xl font-mono font-bold text-black">
                {{ $wallet->currency == 'USD' ? '$' : 'Rp' }} {{ number_format($wallet->balance, 2, ',', '.') }}
            </h2>
            <div class="mt-4 flex gap-2 md:justify-end">
                <a href="{{ route('wallet.edit', $wallet->id) }}"
                    class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-lg transition border border-gray-200">
                    Edit Akun
                </a>
            </div>
        </div>
    </div>

    {{-- Tabel Riwayat Transaksi Dompet Ini --}}
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-900 text-sm uppercase tracking-wide">Riwayat Mutasi Dompet</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead
                    class="bg-white text-gray-500 text-xs uppercase font-semibold tracking-wider border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4">Waktu</th>
                        <th class="px-6 py-4">Tipe</th>
                        <th class="px-6 py-4 text-right">Nominal</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-gray-50 transition group">

                        {{-- Waktu --}}
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $trx->created_at->format('d M Y') }}</div>
                            <div class="text-xs text-gray-400 font-mono mt-0.5">{{ $trx->created_at->format('H:i') }}
                            </div>
                        </td>

                        {{-- Tipe --}}
                        <td class="px-6 py-4">
                            @if(in_array($trx->type, ['TOPUP', 'SELL']))
                            <span
                                class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-white text-gray-700 border border-gray-300 shadow-sm">
                                <svg class="w-3 h-3 mr-1 text-green-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                                Masuk
                            </span>
                            @else
                            <span
                                class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-gray-100 text-gray-600 border border-gray-200">
                                <svg class="w-3 h-3 mr-1 text-red-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                </svg>
                                Keluar
                            </span>
                            @endif
                            <div class="text-xs text-gray-500 mt-1 truncate max-w-[200px]">
                                {{ $trx->asset_symbol ? 'Aset: '.$trx->asset_symbol : ($trx->description ?? '-') }}
                            </div>
                        </td>

                        {{-- Nominal --}}
                        <td class="px-6 py-4 text-right">
                            @php
                            $isPositive = in_array($trx->type, ['TOPUP', 'SELL']);
                            $colorClass = $isPositive ? 'text-green-600' : 'text-gray-900';
                            @endphp
                            <span class="font-mono font-bold {{ $colorClass }}">
                                {{ $isPositive ? '+' : '-' }}
                                {{ $wallet->currency == 'USD' ? '$' : 'Rp' }}
                                {{ number_format(abs($trx->amount_cash), 2, ',', '.') }}
                            </span>
                        </td>

                        {{-- Aksi --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                @if(in_array($trx->type, ['TOPUP', 'WITHDRAW']))
                                <a href="{{ route('transactions.edit', $trx->id) }}"
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-black hover:text-white transition text-gray-500 shadow-sm">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                        </path>
                                    </svg>
                                </a>
                                @else
                                <div
                                    class="w-8 h-8 flex items-center justify-center rounded-lg border border-transparent bg-gray-50 text-gray-300 cursor-not-allowed">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                        </path>
                                    </svg>
                                </div>
                                @endif

                                <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus transaksi ini? Saldo akan dikembalikan.');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition text-gray-400 shadow-sm">
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
                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">Belum ada transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-6 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection