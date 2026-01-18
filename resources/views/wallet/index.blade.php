@extends('layouts.app')
@section('title', 'Dompet & Aset')

@section('content')
<div class="min-h-screen bg-slate-50 pb-20" x-data="{ showCreateModal: false }">

    {{-- BAGIAN ATAS: TOTAL SALDO --}}
    <div class="bg-slate-900 pt-8 pb-24 rounded-b-[3rem] shadow-xl relative overflow-hidden">
        {{-- Hiasan Background --}}
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0 opacity-20">
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-indigo-500 rounded-full blur-[100px]"></div>
            <div class="absolute top-1/2 right-0 w-64 h-64 bg-emerald-500 rounded-full blur-[80px]"></div>
        </div>

        <div class="relative z-10 max-w-6xl mx-auto px-6">
            <div class="flex justify-between items-center mb-8 text-white">
                <div>
                    <h1 class="text-2xl font-bold flex items-center gap-2">
                        <i class="fas fa-wallet text-indigo-400"></i> Dompet Saya
                    </h1>
                    <p class="text-slate-400 text-sm">Kelola seluruh aset keuanganmu.</p>
                </div>
                <div class="text-right hidden md:block">
                    <p class="text-xs text-slate-400 uppercase tracking-widest font-bold">Total Kekayaan</p>
                    <h2 class="text-3xl font-black tracking-tight">Rp {{ number_format($totalBalance, 0, ',', '.') }}
                    </h2>
                </div>
            </div>

            {{-- Card Total Saldo Mobile (Muncul di HP saja) --}}
            <div class="md:hidden mb-6 text-center text-white">
                <p class="text-xs text-slate-400 uppercase tracking-widest font-bold">Total Estimasi Aset</p>
                <h2 class="text-4xl font-black tracking-tight mt-1">Rp {{ number_format($totalBalance, 0, ',', '.') }}
                </h2>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-6 -mt-16 relative z-20 space-y-8">

        {{-- 1. MENU AKSI CEPAT (QUICK ACTIONS) --}}
        <div class="bg-white p-4 rounded-3xl shadow-lg border border-slate-100">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                {{-- Tombol Beli Aset --}}
                <a href="{{ route('buy') }}"
                    class="flex flex-col items-center justify-center p-4 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition group">
                    <div
                        class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mb-2 group-hover:scale-110 transition text-2xl">
                        🛒
                    </div>
                    <span class="font-bold text-sm">Beli Aset</span>
                </a>

                {{-- Tombol Tukar Valas --}}
                <a href="{{ route('exchange.index') }}"
                    class="flex flex-col items-center justify-center p-4 rounded-2xl bg-amber-50 hover:bg-amber-100 text-amber-700 transition group">
                    <div
                        class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mb-2 group-hover:scale-110 transition text-2xl">
                        💱
                    </div>
                    <span class="font-bold text-sm">Tukar Valas</span>
                </a>

                {{-- Tombol Riwayat --}}
                <a href="{{ route('wallet.history') }}"
                    class="flex flex-col items-center justify-center p-4 rounded-2xl bg-slate-50 hover:bg-slate-100 text-slate-700 transition group">
                    <div
                        class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mb-2 group-hover:scale-110 transition text-2xl">
                        📜
                    </div>
                    <span class="font-bold text-sm">Riwayat</span>
                </a>

                {{-- Tombol Tambah Akun --}}
                <button @click="showCreateModal = true"
                    class="flex flex-col items-center justify-center p-4 rounded-2xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition group">
                    <div
                        class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mb-2 group-hover:scale-110 transition text-2xl">
                        ➕
                    </div>
                    <span class="font-bold text-sm">Buat Akun</span>
                </button>

            </div>
        </div>

        {{-- 2. DAFTAR KARTU DOMPET --}}
        <div>
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-lg text-slate-800">Akun Tersimpan ({{ $wallets->count() }})</h3>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($wallets as $w)
                {{-- Logic Warna Kartu: USD (Hijau/Biru Tua), IDR (Biru/Indigo) --}}
                @php
                $cardGradient = $w->currency == 'USD'
                ? 'bg-gradient-to-br from-teal-600 to-emerald-800'
                : 'bg-gradient-to-br from-blue-600 to-indigo-800';

                $icon = $w->currency == 'USD' ? '🇺🇸' : '🇮🇩';
                @endphp

                <div
                    class="{{ $cardGradient }} rounded-3xl p-6 text-white shadow-lg hover:-translate-y-1 hover:shadow-2xl transition duration-300 relative overflow-hidden group h-56 flex flex-col justify-between">

                    {{-- Dekorasi Kartu --}}
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-10 -mt-10 blur-2xl">
                    </div>

                    {{-- Atas Kartu --}}
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-xs font-bold uppercase opacity-70 tracking-widest">{{ $w->bank_name }}</p>
                            <h4 class="font-bold text-xl mt-1">{{ $w->account_name }}</h4>
                        </div>
                        <span
                            class="text-2xl bg-white/20 w-10 h-10 flex items-center justify-center rounded-full backdrop-blur-sm">
                            {{ $icon }}
                        </span>
                    </div>

                    {{-- Chip Kartu (Hiasan) --}}
                    <div class="relative z-10 flex items-center gap-2 opacity-80 my-2">
                        <div
                            class="w-10 h-7 border border-yellow-400/50 rounded bg-yellow-400/20 flex items-center justify-center">
                            <div class="w-6 h-4 border border-yellow-400/50 rounded-sm"></div>
                        </div>
                        <i class="fas fa-wifi rotate-90"></i>
                    </div>

                    {{-- Bawah Kartu --}}
                    <div class="relative z-10">
                        <p class="font-mono opacity-80 text-sm tracking-widest mb-1">
                            {{ chunk_split($w->account_number ?? '0000000000', 4, ' ') }}
                        </p>
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-xs font-bold uppercase opacity-70">Balance</p>
                                <p class="text-2xl font-black tracking-tight">
                                    {{ $w->currency == 'USD' ? '$' : 'Rp' }}
                                    {{ number_format($w->balance, 0, ',', '.') }}
                                </p>
                            </div>

                            {{-- Tombol Kecil di Kartu --}}
                            <div class="flex gap-2">
                                <a href="{{ route('wallet.edit', $w->id) }}"
                                    class="w-8 h-8 rounded-full bg-white/20 hover:bg-white hover:text-indigo-900 flex items-center justify-center transition backdrop-blur-md">
                                    <i class="fas fa-pencil-alt text-xs"></i>
                                </a>
                                <a href="{{ route('wallet.history', ['wallet_id' => $w->id]) }}"
                                    class="w-8 h-8 rounded-full bg-white/20 hover:bg-white hover:text-indigo-900 flex items-center justify-center transition backdrop-blur-md">
                                    <i class="fas fa-history text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                {{-- State Kosong (Jika belum ada dompet) --}}
                @if($wallets->isEmpty())
                <div class="col-span-full text-center py-12 bg-white rounded-3xl border border-dashed border-slate-300">
                    <p class="text-slate-400 text-lg">Belum ada akun dompet.</p>
                    <button @click="showCreateModal = true" class="text-indigo-600 font-bold hover:underline mt-2">Buat
                        Akun Sekarang</button>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- INCLUDE MODAL --}}
    @include('wallet.partials.create-modal')
</div>
@endsection