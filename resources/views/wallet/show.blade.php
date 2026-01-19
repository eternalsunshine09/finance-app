@extends('layouts.app')

@section('title', 'Detail Dompet')

@section('content')
<div class="min-h-screen bg-slate-50 py-10">
    <div class="max-w-4xl mx-auto px-4">

        {{-- Tombol Kembali --}}
        <a href="{{ route('wallet.index') }}"
            class="inline-flex items-center gap-2 text-slate-500 hover:text-indigo-600 font-bold mb-6 transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Dashboard
        </a>

        {{-- Header Kartu Info --}}
        <div
            class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 mb-8 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <span
                    class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-2 inline-block">{{ $wallet->bank_name }}</span>
                <h1 class="text-3xl font-black text-slate-800">{{ $wallet->account_name }}</h1>
                <p class="text-slate-500 font-mono mt-1 text-sm">{{ $wallet->currency }} Wallet</p>
            </div>
            <div class="text-right">
                <p class="text-sm text-slate-400 font-bold uppercase">Sisa Saldo</p>
                <h2 class="text-4xl font-black text-indigo-600">
                    {{ $wallet->currency == 'USD' ? '$' : 'Rp' }} {{ number_format($wallet->balance, 2, ',', '.') }}
                </h2>
            </div>
        </div>

        {{-- Tabel Riwayat --}}
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                <h3 class="font-bold text-lg text-slate-800">📜 Riwayat Transaksi</h3>
                <span class="text-xs text-slate-400">Terakhir diperbarui: {{ now()->format('d M Y') }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                        <tr>
                            <th class="px-6 py-4">Tanggal</th>
                            <th class="px-6 py-4">Tipe</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4 text-right">Nominal</th>
                            <th class="px-6 py-4 text-center">Aksi</th> {{-- KOLOM BARU --}}
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-sm">
                        @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50 transition group">
                            <td class="px-6 py-4 text-slate-500">
                                {{ $trx->created_at->format('d M Y H:i') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($trx->type == 'TOPUP' || $trx->type == 'SELL')
                                <span class="text-emerald-600 bg-emerald-50 px-2 py-1 rounded font-bold text-xs">Uang
                                    Masuk</span>
                                @else
                                <span class="text-rose-600 bg-rose-50 px-2 py-1 rounded font-bold text-xs">Uang
                                    Keluar</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-700">
                                {{ $trx->description ?? $trx->type }}
                            </td>
                            <td
                                class="px-6 py-4 text-right font-bold {{ $trx->amount_cash >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ $wallet->currency == 'USD' ? '$' : 'Rp' }}
                                {{ number_format(abs($trx->amount_cash), 2, ',', '.') }}
                            </td>

                            {{-- TOMBOL AKSI (BARU) --}}
                            {{-- TOMBOL AKSI (REVISI: SELALU TERLIHAT JELAS) --}}
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-3"> {{-- Hapus opacity --}}

                                    @if(in_array($trx->type, ['TOPUP', 'WITHDRAW']))
                                    {{-- Edit --}}
                                    <a href="{{ route('transactions.edit', $trx->id) }}"
                                        class="flex items-center justify-center w-8 h-8 rounded-lg bg-amber-100 text-amber-600 hover:bg-amber-500 hover:text-white transition border border-amber-200"
                                        title="Edit">
                                        <i class="fas fa-pencil-alt text-sm"></i>
                                    </a>

                                    {{-- Hapus --}}
                                    <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus? Saldo akan dikembalikan.');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-rose-100 text-rose-600 hover:bg-rose-500 hover:text-white transition border border-rose-200"
                                            title="Hapus">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                    @else
                                    {{-- Gembok --}}
                                    <div
                                        class="flex items-center justify-center w-8 h-8 rounded-lg bg-slate-100 text-slate-400 cursor-not-allowed border border-slate-200">
                                        <i class="fas fa-lock text-sm"></i>
                                    </div>

                                    {{-- Hapus (Tetap dimunculkan) --}}
                                    <form action="{{ route('transactions.destroy', $trx->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus?');">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="flex items-center justify-center w-8 h-8 rounded-lg bg-rose-100 text-rose-600 hover:bg-rose-500 hover:text-white transition border border-rose-200">
                                            <i class="fas fa-trash-alt text-sm"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                Belum ada transaksi pada dompet ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6">
                {{ $transactions->links() }}
            </div>
        </div>

    </div>
</div>
@endsection