@extends('layouts.app')

@section('title', 'Edit Transaksi')

@section('content')
<div class="min-h-screen bg-slate-50 py-12">
    <div class="max-w-lg mx-auto px-4">

        <div class="mb-6">
            <a href="{{ route('history') }}"
                class="text-slate-500 hover:text-indigo-600 text-sm font-bold flex items-center gap-1 transition">
                ← Kembali ke Riwayat
            </a>
        </div>

        <div class="bg-white rounded-[2rem] shadow-xl p-8 border border-slate-100">
            <h2 class="text-2xl font-black text-slate-800 mb-6">✏️ Edit Transaksi</h2>

            <form action="{{ route('transactions.update', $transaction->id) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- TIPE (Readonly) --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Tipe Transaksi</label>
                    <input type="text" value="{{ $transaction->type }}" readonly
                        class="w-full bg-slate-100 border border-slate-200 rounded-xl px-4 py-3 text-slate-500 font-bold cursor-not-allowed">
                </div>

                {{-- NOMINAL --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nominal (Rp)</label>
                    <input type="number" name="amount_cash" value="{{ abs($transaction->amount_cash) }}"
                        class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 font-bold text-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <p class="text-[10px] text-orange-500 mt-1">*Mengubah nominal akan otomatis menyesuaikan saldo
                        dompet.</p>
                </div>

                {{-- TANGGAL --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Tanggal</label>
                    <input type="datetime-local" name="date"
                        value="{{ \Carbon\Carbon::parse($transaction->date)->format('Y-m-d\TH:i') }}"
                        class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:ring-indigo-500 focus:border-indigo-500">
                </div>

                {{-- DESKRIPSI --}}
                <div class="mb-6">
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Keterangan</label>
                    <textarea name="description" rows="3"
                        class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 focus:ring-indigo-500 focus:border-indigo-500">{{ $transaction->description }}</textarea>
                </div>

                {{-- Di dalam file edit.blade.php --}}

                @php
                // Cek apakah ini transaksi konversi (Buy/Sell tanpa simbol aset)
                $isExchange = in_array($transaction->type, ['BUY', 'SELL']) && $transaction->asset_symbol == null;
                @endphp

                {{-- NOMINAL --}}
                <div class="mb-4">
                    <label class="block text-xs font-bold text-slate-600 uppercase mb-1">Nominal (Rp)</label>

                    <input type="number" name="amount_cash" value="{{ abs($transaction->amount_cash) }}"
                        class="w-full bg-white border border-slate-200 rounded-xl px-4 py-3 text-slate-800 font-bold text-lg focus:ring-indigo-500 focus:border-indigo-500 {{ $isExchange ? 'bg-slate-100 text-slate-500 cursor-not-allowed' : '' }}"
                        {{ $isExchange ? 'readonly' : '' }}>

                    @if($isExchange)
                    <p class="text-[10px] text-slate-400 mt-1">
                        🔒 Nominal konversi tidak dapat diedit untuk menjaga konsistensi kurs.
                        Jika salah input, silakan hubungi admin atau lakukan transaksi koreksi.
                    </p>
                    @else
                    <p class="text-[10px] text-orange-500 mt-1">*Mengubah nominal akan otomatis menyesuaikan saldo
                        dompet.</p>
                    @endif
                </div>
            </form>
        </div>
    </div>
</div>
@endsection