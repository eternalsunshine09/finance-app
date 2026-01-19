@extends('layouts.app')
@section('title', 'Dompet & Aset')
@section('header', 'My Wallet')

@section('content')
<div class="min-h-screen" x-data="{ showCreateModal: false }">

    {{-- BAGIAN ATAS: TOTAL ESTIMASI KEKAYAAN --}}
    {{-- Menggunakan Gradient Pastel Indigo-Purple --}}
    <div
        class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-600 to-purple-700 shadow-xl shadow-indigo-200 mb-10 group text-white">

        {{-- Animated Background Blobs --}}
        <div
            class="absolute top-0 right-0 -mr-20 -mt-20 w-80 h-80 bg-white/20 rounded-full blur-[80px] group-hover:bg-white/30 transition duration-1000">
        </div>
        <div
            class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-pink-500/30 rounded-full blur-[60px] group-hover:bg-pink-500/40 transition duration-1000">
        </div>

        <div class="relative z-10 p-8 md:p-10 flex flex-col md:flex-row justify-between items-center gap-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <div class="p-1.5 rounded bg-white/20 backdrop-blur-sm border border-white/10">
                        <svg class="w-4 h-4 text-indigo-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <span class="text-indigo-100 text-sm font-bold tracking-widest uppercase">Total Estimasi Aset</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-extrabold text-white tracking-tight font-mono">
                    Rp {{ number_format($totalBalance, 0, ',', '.') }}
                </h1>
                <p class="text-indigo-100 text-sm mt-2 font-medium">Gabungan seluruh saldo tunai & valuasi aset saat
                    ini.</p>
            </div>

            {{-- Action Button Utama --}}
            <button @click="showCreateModal = true"
                class="group relative px-6 py-3 bg-white text-indigo-700 font-bold rounded-xl transition hover:shadow-lg hover:shadow-indigo-900/20 hover:-translate-y-0.5">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Tambah Dompet
                </span>
            </button>
        </div>
    </div>

    {{-- 1. MENU AKSI CEPAT (QUICK ACTIONS - Light Version) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">

        {{-- Top Up --}}
        <a href="{{ route('topup') }}"
            class="group bg-white border border-slate-200 hover:border-emerald-200 p-5 rounded-2xl flex flex-col items-center justify-center gap-3 transition-all hover:-translate-y-1 hover:shadow-lg hover:shadow-emerald-100">
            <div
                class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center group-hover:bg-emerald-500 group-hover:text-white transition duration-300">
                <svg class="w-6 h-6 text-emerald-600 group-hover:text-white" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
            </div>
            <span class="text-slate-500 font-bold text-sm group-hover:text-emerald-600">Deposit</span>
        </a>

        {{-- Withdraw --}}
        <a href="{{ route('withdraw') }}"
            class="group bg-white border border-slate-200 hover:border-rose-200 p-5 rounded-2xl flex flex-col items-center justify-center gap-3 transition-all hover:-translate-y-1 hover:shadow-lg hover:shadow-rose-100">
            <div
                class="w-12 h-12 rounded-2xl bg-rose-50 flex items-center justify-center group-hover:bg-rose-500 group-hover:text-white transition duration-300">
                <svg class="w-6 h-6 text-rose-500 group-hover:text-white" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
            </div>
            <span class="text-slate-500 font-bold text-sm group-hover:text-rose-600">Withdraw</span>
        </a>

        {{-- Beli Aset --}}
        <a href="{{ route('buy') }}"
            class="group bg-white border border-slate-200 hover:border-blue-200 p-5 rounded-2xl flex flex-col items-center justify-center gap-3 transition-all hover:-translate-y-1 hover:shadow-lg hover:shadow-blue-100">
            <div
                class="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white transition duration-300">
                <svg class="w-6 h-6 text-blue-500 group-hover:text-white" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
            <span class="text-slate-500 font-bold text-sm group-hover:text-blue-600">Trade</span>
        </a>

        {{-- Exchange (PERBAIKAN LINK DISINI) --}}
        {{-- Pastikan route 'exchange.index' ada di web.php --}}
        <a href="{{ route('exchange.index') }}"
            class="group bg-white border border-slate-200 hover:border-orange-200 p-5 rounded-2xl flex flex-col items-center justify-center gap-3 transition-all hover:-translate-y-1 hover:shadow-lg hover:shadow-orange-100">
            <div
                class="w-12 h-12 rounded-2xl bg-orange-50 flex items-center justify-center group-hover:bg-orange-500 group-hover:text-white transition duration-300">
                <svg class="w-6 h-6 text-orange-500 group-hover:text-white" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </div>
            <span class="text-slate-500 font-bold text-sm group-hover:text-orange-600">Exchange</span>
        </a>
    </div>

    {{-- 2. DAFTAR KARTU DOMPET --}}
    <div>
        <div class="flex items-end justify-between mb-6">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">Akun Dompet</h3>
                <p class="text-slate-500 text-sm">Kelola sumber dana transaksi Anda.</p>
            </div>
            {{-- Perbaiki route jika perlu, misal 'history' atau 'wallet.index' --}}
            <a href="#"
                class="text-sm font-bold text-indigo-600 hover:text-indigo-500 flex items-center gap-1 transition">
                Lihat Riwayat
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3">
                    </path>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($wallets as $w)

            @php
            // Styles untuk Kartu (Tetap Gradient agar elegan)
            // USD = Emerald/Teal, IDR = Blue/Indigo
            $cardStyle = $w->currency == 'USD'
            ? 'bg-gradient-to-bl from-emerald-500 to-teal-700 shadow-emerald-200'
            : 'bg-gradient-to-bl from-blue-500 to-indigo-700 shadow-blue-200';
            @endphp

            <div
                class="group relative {{ $cardStyle }} rounded-3xl p-6 h-64 flex flex-col justify-between shadow-xl hover:scale-[1.02] transition duration-300 overflow-hidden text-white">

                {{-- Decorative Circles --}}
                <div class="absolute -right-6 -top-6 w-40 h-40 bg-white/20 rounded-full blur-2xl"></div>
                <div class="absolute -left-6 -bottom-6 w-32 h-32 bg-black/10 rounded-full blur-xl"></div>

                {{-- Header Kartu --}}
                <div class="relative z-10 flex justify-between items-start">
                    <div class="flex flex-col">
                        <span
                            class="text-xs font-bold uppercase tracking-widest text-white/80">{{ $w->bank_name }}</span>
                        <span class="text-lg font-bold text-white tracking-wide mt-1">{{ $w->account_name }}</span>
                    </div>
                    <span
                        class="px-3 py-1 rounded-lg bg-white/20 backdrop-blur-md text-white font-bold text-xs border border-white/20">
                        {{ $w->currency }}
                    </span>
                </div>

                {{-- Chip & Number --}}
                <div class="relative z-10 my-4">
                    <div
                        class="w-10 h-8 rounded-md bg-gradient-to-br from-yellow-200 to-yellow-500 shadow-sm mb-4 opacity-90 flex items-center justify-center border border-yellow-600/30">
                        <div class="w-full h-[1px] bg-black/20"></div>
                        <div class="absolute h-full w-[1px] bg-black/20"></div>
                    </div>

                    <p class="font-mono text-xl text-white tracking-widest drop-shadow-md">
                        {{ chunk_split($w->account_number ?? '0000', 4, ' ') }}
                    </p>
                </div>

                {{-- Footer Kartu --}}
                <div class="relative z-10 flex justify-between items-end border-t border-white/20 pt-4">
                    <div>
                        <span class="text-[10px] text-white/80 uppercase font-bold">Balance</span>
                        <p class="text-2xl font-bold text-white font-mono">
                            {{ $w->currency == 'USD' ? '$' : 'Rp' }} {{ number_format($w->balance, 2, ',', '.') }}
                        </p>
                    </div>

                    {{-- Actions (Hover Reveal) --}}
                    <div class="flex gap-2">
                        <a href="{{ route('wallet.show', $w->id) }}"
                            class="w-8 h-8 rounded-full bg-white/20 hover:bg-white hover:text-indigo-900 flex items-center justify-center transition backdrop-blur-sm"
                            title="Lihat Detail">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                        </a>
                        <a href="{{ route('wallet.edit', $w->id) }}"
                            class="w-8 h-8 rounded-full bg-white/20 hover:bg-yellow-400 hover:text-yellow-900 flex items-center justify-center transition backdrop-blur-sm"
                            title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                </path>
                            </svg>
                        </a>
                        <form action="{{ route('wallet.destroy', $w->id) }}" method="POST"
                            onsubmit="return confirm('Hapus dompet ini?');">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="w-8 h-8 rounded-full bg-white/20 hover:bg-rose-500 hover:text-white flex items-center justify-center transition backdrop-blur-sm"
                                title="Hapus">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div
                class="col-span-full py-16 text-center border-2 border-dashed border-slate-300 rounded-3xl bg-slate-50">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-200 mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-slate-800 font-bold text-lg">Belum Ada Dompet</h3>
                <p class="text-slate-500 mb-6">Tambahkan akun bank atau e-wallet untuk mulai bertransaksi.</p>
                <button @click="showCreateModal = true"
                    class="px-6 py-2 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg font-bold transition shadow-lg shadow-indigo-200">
                    + Buat Baru
                </button>
            </div>
            @endforelse
        </div>
    </div>

    {{-- INCLUDE MODAL --}}
    @include('wallet.partials.create-modal')
</div>
@endsection