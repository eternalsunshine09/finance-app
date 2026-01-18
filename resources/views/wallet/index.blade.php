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
        <div class="bg-white p-6 rounded-3xl shadow-lg border border-slate-100 mb-8">
            <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">Menu Transaksi</h3>

            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">

                {{-- 1. TOMBOL TOP UP (Gantikan Posisi Lama) --}}
                <a href="{{ route('topup') }}"
                    class="flex flex-col items-center justify-center p-4 rounded-2xl bg-emerald-50 hover:bg-emerald-100 text-emerald-700 transition group hover:-translate-y-1">
                    <div
                        class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition text-2xl text-emerald-600">
                        📥
                    </div>
                    <span class="font-bold text-sm">Isi Saldo</span>
                </a>

                {{-- 2. TOMBOL WITHDRAW (Baru) --}}
                <a href="{{ route('withdraw') }}"
                    class="flex flex-col items-center justify-center p-4 rounded-2xl bg-rose-50 hover:bg-rose-100 text-rose-700 transition group hover:-translate-y-1">
                    <div
                        class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition text-2xl text-rose-600">
                        📤
                    </div>
                    <span class="font-bold text-sm">Tarik Dana</span>
                </a>

                {{-- 3. TOMBOL BELI ASET (Tetap) --}}
                <a href="{{ route('buy') }}"
                    class="flex flex-col items-center justify-center p-4 rounded-2xl bg-indigo-50 hover:bg-indigo-100 text-indigo-700 transition group hover:-translate-y-1">
                    <div
                        class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition text-2xl text-indigo-600">
                        🛒
                    </div>
                    <span class="font-bold text-sm">Beli Aset</span>
                </a>

                {{-- 4. TOMBOL TUKAR VALAS (Tetap Penting) --}}
                <a href="{{ route('exchange.index') }}"
                    class="flex flex-col items-center justify-center p-4 rounded-2xl bg-amber-50 hover:bg-amber-100 text-amber-700 transition group hover:-translate-y-1">
                    <div
                        class="w-12 h-12 bg-white rounded-full shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition text-2xl text-amber-600">
                        💱
                    </div>
                    <span class="font-bold text-sm">Tukar Valas</span>
                </a>

            </div>

            {{-- Tombol Tambahan Kecil (Opsional) --}}
            <div class="flex justify-center gap-4 mt-6 pt-4 border-t border-slate-50">
                <a href="{{ route('wallet.history') }}"
                    class="text-xs font-bold text-slate-400 hover:text-indigo-600 flex items-center gap-1 transition">
                    <i class="fas fa-history"></i> Lihat Riwayat Lengkap
                </a>
                <span class="text-slate-300">|</span>
                <button @click="showCreateModal = true"
                    class="text-xs font-bold text-slate-400 hover:text-indigo-600 flex items-center gap-1 transition">
                    <i class="fas fa-plus"></i> Buat Akun Baru
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
                @php
                $cardGradient = $w->currency == 'USD'
                ? 'bg-gradient-to-br from-teal-600 to-emerald-800'
                : 'bg-gradient-to-br from-blue-600 to-indigo-800';
                @endphp

                <div
                    class="{{ $cardGradient }} rounded-3xl p-6 text-white shadow-lg hover:-translate-y-1 hover:shadow-2xl transition duration-300 relative overflow-hidden flex flex-col justify-between h-64 group">

                    {{-- Dekorasi --}}
                    <div
                        class="absolute top-0 right-0 w-32 h-32 bg-white opacity-10 rounded-full -mr-10 -mt-10 blur-2xl">
                    </div>

                    {{-- Bagian Atas --}}
                    <div class="relative z-10">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-xs font-bold uppercase opacity-70 tracking-widest">{{ $w->bank_name }}
                                </p>
                                <h4 class="font-bold text-xl mt-1">{{ $w->account_name }}</h4>
                            </div>
                            <span class="bg-white/20 px-3 py-1 rounded-lg text-xs font-bold backdrop-blur-sm">
                                {{ $w->currency }}
                            </span>
                        </div>

                        <div class="mt-6">
                            <p class="font-mono opacity-80 text-sm tracking-widest">
                                {{ chunk_split($w->account_number ?? '0000', 4, ' ') }}
                            </p>
                            <p class="text-2xl font-black tracking-tight mt-1">
                                {{-- 🔥 GANTI JADI INI (Angka 2 desimal) --}}
                                {{ $w->currency == 'USD' ? '$' : 'Rp' }} {{ number_format($w->balance, 2, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    {{-- 🔥 UPDATE: Bagian Tombol Aksi --}}
                    <div class="relative z-10 mt-auto pt-4 border-t border-white/20 flex justify-between items-center">

                        {{-- Tombol Lihat Detail --}}
                        <a href="{{ route('wallet.show', $w->id) }}"
                            class="flex items-center gap-2 text-xs font-bold bg-white/20 hover:bg-white hover:text-indigo-900 py-2 px-4 rounded-full transition backdrop-blur-md">
                            <i class="fas fa-eye"></i> Lihat Mutasi
                        </a>

                        <div class="flex gap-2">
                            {{-- Tombol Edit --}}
                            <a href="{{ route('wallet.edit', $w->id) }}"
                                class="w-8 h-8 flex items-center justify-center rounded-full bg-white/10 hover:bg-yellow-400 hover:text-yellow-900 transition"
                                title="Edit">
                                <i class="fas fa-pencil-alt text-xs"></i>
                            </a>

                            {{-- Tombol Hapus --}}
                            <form action="{{ route('wallet.destroy', $w->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus dompet ini? Riwayat transaksi mungkin akan hilang.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="w-8 h-8 flex items-center justify-center rounded-full bg-white/10 hover:bg-red-500 hover:text-white transition"
                                    title="Hapus">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
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