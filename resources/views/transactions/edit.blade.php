@extends('layouts.app')

@section('title', 'Edit Transaksi')
@section('header', 'Koreksi Data')

@section('content')
<div class="max-w-lg mx-auto py-8">

    <div class="mb-6">
        <a href="{{ route('history') }}"
            class="text-gray-500 hover:text-black text-sm font-bold flex items-center gap-2 transition group">
            <div
                class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center group-hover:border-black transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </div>
            Kembali ke Riwayat
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">

        <form action="{{ route('transactions.update', $transaction->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- TIPE (Readonly) --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Tipe
                    Transaksi</label>
                <div class="relative">
                    <input type="text"
                        value="{{ $transaction->type }} {{ $transaction->asset_symbol ? '- ' . $transaction->asset_symbol : '' }}"
                        readonly
                        class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-gray-500 font-bold cursor-not-allowed focus:outline-none">
                </div>
            </div>

            {{-- NOMINAL (BISA DIEDIT SEKARANG) --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Nominal
                    (Cash)</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">Rp</span>
                    <input type="number" name="amount_cash" value="{{ abs($transaction->amount_cash) }}"
                        class="w-full pl-10 pr-4 py-3 bg-white border border-gray-200 rounded-lg font-bold text-gray-900 text-lg focus:ring-1 focus:ring-black focus:border-black transition"
                        required>
                </div>

                {{-- Peringatan Kecil --}}
                @if($transaction->asset_symbol)
                <div class="mt-2 flex items-start gap-2 bg-yellow-50 p-2 rounded border border-yellow-100">
                    <svg class="w-4 h-4 text-yellow-500 mt-0.5 shrink-0" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-[10px] text-yellow-700 leading-tight">
                        Mengubah nominal pembelian aset akan mempengaruhi perhitungan <strong>Harga Rata-rata (Avg
                            Price)</strong> di portofolio Anda. Pastikan nominal baru sudah benar.
                    </p>
                </div>
                @else
                <p class="text-[11px] text-gray-400 mt-2 ml-1">
                    *Saldo dompet akan otomatis disesuaikan dengan nominal baru.
                </p>
                @endif
            </div>

            {{-- TANGGAL --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Tanggal</label>
                <input type="datetime-local" name="date"
                    value="{{ \Carbon\Carbon::parse($transaction->created_at)->format('Y-m-d\TH:i') }}"
                    class="w-full bg-white border border-gray-200 rounded-lg px-4 py-3 text-gray-900 focus:ring-1 focus:ring-black focus:border-black font-medium transition text-sm">
            </div>

            {{-- DESKRIPSI --}}
            <div>
                <label
                    class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Keterangan</label>
                <textarea name="description" rows="3"
                    class="w-full bg-white border border-gray-200 rounded-lg px-4 py-3 text-gray-900 focus:ring-1 focus:ring-black focus:border-black transition placeholder-gray-400 text-sm">{{ $transaction->description }}</textarea>
            </div>

            {{-- TOMBOL UPDATE --}}
            <div class="pt-4 border-t border-gray-100 flex gap-3">
                <button type="submit"
                    class="flex-1 bg-black hover:bg-gray-800 text-white font-bold py-3.5 rounded-lg shadow-sm transition transform active:scale-[0.98] flex justify-center items-center gap-2">
                    Simpan Perubahan
                </button>
            </div>

        </form>
    </div>
</div>
@endsection