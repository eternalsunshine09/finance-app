@extends('layouts.app')

@section('title', 'Riwayat Transaksi')
@section('header', '📜 Riwayat Transaksi')

@section('content')

<div class="max-w-6xl mx-auto">

    {{-- Info Card --}}
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100 mb-6 flex justify-between items-center">
        <div>
            <h3 class="font-bold text-gray-700 text-lg">Aktivitas Keuangan</h3>
            <p class="text-sm text-gray-500">Pantau semua aliran dana masuk dan keluar.</p>
        </div>
        <div class="text-right">
            <span class="text-xs font-bold text-gray-400 uppercase">Total Transaksi</span>
            <p class="text-2xl font-black text-indigo-600">{{ $transactions->total() }}</p>
        </div>
    </div>

    {{-- Tabel Transaksi --}}
    <div class="bg-white shadow-xl rounded-2xl overflow-hidden border border-gray-100">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-500 uppercase text-xs font-bold tracking-wider">
                        <th class="p-4">Waktu</th>
                        <th class="p-4">Tipe</th>
                        <th class="p-4">Aset</th>
                        <th class="p-4 text-right">Nominal (Cash)</th>
                        <th class="p-4 text-right">Jumlah Unit</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Aksi</th> {{-- KOLOM BARU --}}
                    </tr>
                </thead>
                <tbody class="text-gray-700 text-sm">
                    @forelse($transactions as $trx)
                    <tr class="border-b border-gray-100 hover:bg-gray-50 transition group">

                        {{-- WAKTU --}}
                        <td class="p-4">
                            <div class="font-bold text-gray-800">
                                {{ $trx->created_at->format('d M Y') }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $trx->created_at->format('H:i') }} WIB
                            </div>
                        </td>

                        {{-- TIPE TRANSAKSI --}}
                        <td class="p-4">
                            @if($trx->type == 'TOPUP')
                            <span
                                class="bg-emerald-100 text-emerald-700 py-1 px-3 rounded-full text-xs font-bold border border-emerald-200 inline-flex items-center gap-1">
                                <i class="fas fa-arrow-down"></i> TOP UP
                            </span>
                            @elseif($trx->type == 'WITHDRAW')
                            <span
                                class="bg-orange-100 text-orange-700 py-1 px-3 rounded-full text-xs font-bold border border-orange-200 inline-flex items-center gap-1">
                                <i class="fas fa-arrow-up"></i> TARIK
                            </span>
                            @elseif($trx->type == 'BUY')
                            <span
                                class="bg-rose-100 text-rose-700 py-1 px-3 rounded-full text-xs font-bold border border-rose-200 inline-flex items-center gap-1">
                                <i class="fas fa-shopping-cart"></i> BELI
                            </span>
                            @elseif($trx->type == 'SELL')
                            <span
                                class="bg-blue-100 text-blue-700 py-1 px-3 rounded-full text-xs font-bold border border-blue-200 inline-flex items-center gap-1">
                                <i class="fas fa-hand-holding-usd"></i> JUAL
                            </span>
                            @else
                            <span
                                class="bg-gray-100 text-gray-700 py-1 px-3 rounded-full text-xs font-bold border border-gray-200">
                                {{ $trx->type }}
                            </span>
                            @endif
                        </td>

                        {{-- ASET --}}
                        <td class="p-4 font-bold text-gray-600">
                            {{ $trx->asset_symbol ?? '-' }}
                        </td>

                        {{-- NOMINAL UANG --}}
                        <td class="p-4 text-right">
                            @php
                            $isNegative = $trx->amount_cash < 0; $color=$isNegative ? 'text-rose-600'
                                : 'text-emerald-600' ; $currencyCode=optional($trx->wallet)->currency ?? 'IDR';
                                $symbol = ($currencyCode == 'USD') ? '$' : 'Rp';
                                @endphp
                                <span class="font-mono font-bold {{ $color }}">
                                    {{ $isNegative ? '-' : '+' }} {{ $symbol }}
                                    {{ number_format(abs($trx->amount_cash), 2) }}
                                </span>
                        </td>

                        {{-- JUMLAH UNIT --}}
                        <td class="p-4 text-right text-gray-600">
                            @if($trx->amount && $trx->amount != 0)
                            <span class="font-mono">{{ number_format(abs($trx->amount), 4) }}</span>
                            @else
                            -
                            @endif
                        </td>

                        {{-- STATUS --}}
                        <td class="p-4 text-center">
                            @if($trx->status == 'approved' || $trx->status == 'success')
                            <i class="fas fa-check-circle text-emerald-500 text-lg" title="Berhasil"></i>
                            @elseif($trx->status == 'pending')
                            <i class="fas fa-clock text-yellow-500 text-lg" title="Menunggu Konfirmasi"></i>
                            @elseif($trx->status == 'failed')
                            <i class="fas fa-times-circle text-rose-500 text-lg" title="Gagal/Ditolak"></i>
                            @else
                            <span class="text-xs font-bold text-gray-400">{{ $trx->status }}</span>
                            @endif
                        </td>

                        {{-- TOMBOL AKSI (REVISI: SELALU TERLIHAT JELAS) --}}
                        <td class="p-4 text-center">
                            <div class="flex items-center justify-center gap-3">
                                {{-- Hapus opacity-50 dan group-hover --}}

                                @if(in_array($trx->type, ['TOPUP', 'WITHDRAW']))
                                {{-- Tombol Edit --}}
                                <a href="{{ route('transactions.edit', $trx->id) }}"
                                    class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 text-amber-600 hover:bg-amber-500 hover:text-white transition shadow-sm border border-amber-200"
                                    title="Edit">
                                    <i class="fas fa-pencil-alt text-sm"></i>
                                </a>

                                {{-- Tombol Hapus --}}
                                <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST"
                                    onsubmit="return confirm('PERINGATAN: Jika ini transaksi KONVERSI, pastikan Anda menghapus KEDUA transaksi (Masuk & Keluar) agar saldo seimbang. Lanjutkan?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="flex items-center justify-center w-8 h-8 rounded-lg bg-rose-100 text-rose-600 hover:bg-rose-500 hover:text-white transition shadow-sm border border-rose-200"
                                        title="Hapus Permanen">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </form>
                                @else
                                {{-- Icon Gembok (Untuk Transaksi Aset/Konversi) --}}
                                <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200"
                                    title="Edit dikunci demi keamanan">
                                    <i class="fas fa-lock text-sm"></i>
                                </div>

                                {{-- Tombol Hapus Tetap Ada (Sesuai request kamu sebelumnya) --}}
                                <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST"
                                    onsubmit="return confirm('PERINGATAN: Saldo akan dikembalikan. Lanjutkan?');">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="flex items-center justify-center w-8 h-8 rounded-lg bg-rose-100 text-rose-600 hover:bg-rose-500 hover:text-white transition shadow-sm border border-rose-200"
                                        title="Hapus Permanen">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </form>
                                @endif

                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="7"
                            class="p-12 text-center text-gray-400 flex flex-col items-center justify-center">
                            <div class="bg-gray-100 p-4 rounded-full mb-3">
                                <i class="fas fa-receipt text-3xl text-gray-300"></i>
                            </div>
                            <p>Belum ada riwayat transaksi.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
        <div class="p-4 bg-gray-50 border-t border-gray-100">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>

@endsection