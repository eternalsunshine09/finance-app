@extends('layouts.app')
@section('title', 'Dompet Saya')

@section('content')
{{-- x-data membungkus seluruh area agar modal bisa berfungsi --}}
<div class="min-h-screen bg-slate-50 pt-6 pb-12" x-data="{ showCreateModal: false }">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        {{-- HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-black text-slate-800 tracking-tight flex items-center gap-3">
                    <span
                        class="bg-white text-indigo-600 p-2 rounded-2xl border border-slate-100 shadow-sm text-2xl">💳</span>
                    Dompet & RDN
                </h1>
                <p class="text-slate-500 font-medium mt-2 ml-1">Kelola akun dan sumber dana Anda.</p>
            </div>

            {{-- TOMBOL AKSI --}}
            <div class="flex flex-wrap gap-3">
                {{-- 🔥 TOMBOL TUKAR VALAS (BARU) 🔥 --}}
                <a href="{{ route('exchange.index') }}"
                    class="bg-amber-100 text-amber-700 border border-amber-200 px-5 py-3 rounded-2xl font-bold hover:bg-amber-200 transition flex items-center gap-2 shadow-sm">
                    💱 Tukar Valas
                </a>

                {{-- Tombol Riwayat --}}
                <a href="{{ route('wallet.history') }}"
                    class="bg-white text-slate-600 border border-slate-200 px-5 py-3 rounded-2xl font-bold hover:bg-slate-50 transition flex items-center gap-2 shadow-sm">
                    📜 Riwayat
                </a>

                {{-- Tombol Tambah Akun --}}
                <button @click="showCreateModal = true"
                    class="bg-indigo-600 text-white px-5 py-3 rounded-2xl font-bold shadow-lg shadow-indigo-200 hover:bg-indigo-700 hover:scale-105 transition flex items-center gap-2">
                    ➕ Tambah Akun
                </button>
            </div>
        </div>

        {{-- TOTAL SALDO CARD --}}
        <div
            class="relative overflow-hidden bg-gradient-to-br from-slate-800 to-slate-900 rounded-[2.5rem] p-8 md:p-10 text-white shadow-2xl shadow-slate-200">
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-indigo-500 rounded-full opacity-20 blur-3xl">
            </div>
            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-teal-500 rounded-full opacity-20 blur-3xl">
            </div>

            <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
                <div class="flex items-center gap-6">
                    <div
                        class="w-20 h-20 rounded-3xl bg-white/10 backdrop-blur-md flex items-center justify-center text-4xl shadow-inner border border-white/20">
                        💰
                    </div>
                    <div>
                        <p class="text-slate-300 text-sm font-bold uppercase tracking-wider mb-1">Estimasi Total Aset
                            (IDR)</p>
                        <h2 class="text-4xl md:text-5xl font-black text-white tracking-tight">
                            Rp {{ number_format($totalBalance, 0, ',', '.') }}
                        </h2>
                    </div>
                </div>
                <div class="bg-white/10 backdrop-blur-sm px-6 py-3 rounded-2xl border border-white/10">
                    <span class="text-slate-200 font-medium text-sm">Tersebar di <b>{{ $wallets->count() }}</b>
                        Akun</span>
                </div>
            </div>
        </div>

        {{-- GRID KARTU DOMPET --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($wallets as $w)
            <div
                class="bg-white rounded-3xl p-6 shadow-sm border border-slate-100 hover:shadow-xl hover:-translate-y-1 transition duration-300 flex flex-col justify-between h-60 relative overflow-hidden group">

                <div class="relative z-10">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <span
                                class="text-xs font-black text-slate-400 uppercase tracking-widest">{{ $w->bank_name }}</span>
                            <h3 class="font-bold text-slate-800 text-xl">{{ $w->account_name }}</h3>
                        </div>
                        <span
                            class="bg-slate-100 text-slate-600 px-2 py-1 rounded text-xs font-bold">{{ $w->currency }}</span>
                    </div>
                    <p class="font-mono text-slate-500 tracking-wider">
                        {{ chunk_split($w->account_number ?? '****', 4, ' ') }}
                    </p>
                </div>

                <div class="relative z-10 flex justify-between items-end mt-auto pt-4 border-t border-slate-50">
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase">Saldo</p>
                        <p class="text-2xl font-black text-slate-800">
                            {{ $w->currency == 'USD' ? '$' : 'Rp' }} {{ number_format($w->balance, 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="flex gap-2">
                        {{-- Tombol Edit --}}
                        <a href="{{ route('wallet.edit', $w->id) }}"
                            class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center hover:bg-indigo-600 hover:text-white transition"
                            title="Edit Dompet">
                            ✏️
                        </a>
                        {{-- Tombol Lihat History Spesifik --}}
                        <a href="{{ route('wallet.history', ['wallet_id' => $w->id]) }}"
                            class="w-10 h-10 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center hover:bg-slate-800 hover:text-white transition"
                            title="Lihat Mutasi">
                            📜
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- INCLUDE MODAL (Pastikan file partial ini ada) --}}
    @include('wallet.partials.create-modal')
</div>
@endsection