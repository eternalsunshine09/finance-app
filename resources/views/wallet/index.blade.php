@extends('layouts.app')

@section('title', 'Dompet Saya')
@section('header', 'Kelola Dompet')
@section('header_description', 'Atur sumber dana dan rekening investasi Anda.')

@section('content')
<div class="max-w-7xl mx-auto pb-20" x-data="{ showCreateModal: false }">

    {{-- 1. TOTAL WEALTH CARD --}}
    <div class="bg-black text-white rounded-2xl p-8 mb-10 shadow-lg relative overflow-hidden">
        <div
            class="absolute top-0 right-0 w-64 h-64 bg-gray-800 rounded-full blur-3xl -mr-16 -mt-16 opacity-30 pointer-events-none">
        </div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-end gap-6">
            <div>
                <p class="text-gray-400 text-xs font-bold uppercase tracking-widest mb-2">Total Estimasi Aset</p>
                <h1 class="text-4xl md:text-5xl font-bold font-mono tracking-tight">
                    Rp {{ number_format($totalBalance, 0, ',', '.') }}
                </h1>
                <p class="text-gray-500 text-xs mt-2">*Gabungan saldo tunai semua dompet</p>
            </div>

            <button @click="showCreateModal = true"
                class="bg-white text-black px-6 py-3 rounded-xl font-bold text-sm hover:bg-gray-200 transition shadow-lg flex items-center gap-2 transform active:scale-95">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Dompet
            </button>
        </div>
    </div>

    {{-- 2. DAFTAR KARTU DOMPET --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-6 px-1 gap-4">
        <div>
            <h3 class="text-lg font-bold text-gray-900">Daftar Akun</h3>
            <p class="text-xs text-gray-500 mt-0.5">Kelola rekening bank dan e-wallet Anda.</p>
        </div>

        {{-- FITUR 1: LINK KE GLOBAL HISTORY --}}
        <a href="{{ route('history') }}"
            class="group flex items-center gap-2 text-sm font-bold text-gray-600 hover:text-black transition">
            Lihat Semua Riwayat Transaksi
            <span
                class="bg-gray-100 group-hover:bg-black group-hover:text-white text-gray-600 rounded-full w-6 h-6 flex items-center justify-center transition">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3">
                    </path>
                </svg>
            </span>
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($wallets as $w)
        <div
            class="bg-white border border-gray-200 rounded-2xl p-6 relative group hover:border-black transition duration-300 shadow-sm hover:shadow-md flex flex-col h-full">

            {{-- Header: Bank Name & Actions --}}
            <div class="flex justify-between items-start mb-4">
                <div class="flex flex-col">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">PLATFORM</span>
                    <div class="flex items-center gap-2">
                        <span class="text-sm font-bold text-black border border-gray-200 bg-gray-50 px-2 py-1 rounded">
                            {{ $w->bank_name }}
                        </span>
                        @if($w->currency == 'USD')
                        <span class="text-[10px] font-bold bg-black text-white px-1.5 py-0.5 rounded">USD</span>
                        @endif
                    </div>
                </div>

                {{-- FITUR 2: ACTION MENU (Detail, Edit, Hapus) --}}
                <div class="flex gap-1">
                    {{-- Detail --}}
                    <a href="{{ route('wallet.show', $w->id) }}"
                        class="p-2 text-gray-400 hover:text-black hover:bg-gray-100 transition rounded-lg"
                        title="Lihat Detail & Mutasi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                            </path>
                        </svg>
                    </a>

                    {{-- Edit --}}
                    <a href="{{ route('wallet.edit', $w->id) }}"
                        class="p-2 text-gray-400 hover:text-black hover:bg-gray-100 transition rounded-lg"
                        title="Edit Informasi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                            </path>
                        </svg>
                    </a>

                    {{-- Hapus (Form) --}}
                    <form action="{{ route('wallet.destroy', $w->id) }}" method="POST"
                        onsubmit="return confirm('Hapus dompet {{ $w->account_name }}? Semua riwayat transaksi dompet ini juga akan terhapus permanen.');">
                        @csrf @method('DELETE')
                        <button type="submit"
                            class="p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 transition rounded-lg"
                            title="Hapus Dompet">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                </path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Body: Account Name --}}
            <div class="mb-auto">
                <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">LABEL AKUN</span>
                <h4 class="text-lg font-bold text-gray-900 leading-tight mt-0.5 truncate">{{ $w->account_name }}</h4>
                <p
                    class="text-xs text-gray-500 font-mono mt-1 bg-gray-50 inline-block px-2 py-0.5 rounded border border-gray-100">
                    {{ $w->account_number ?? '-' }}
                </p>
            </div>

            {{-- Footer: Balance --}}
            <div class="pt-6 mt-4 border-t border-gray-100">
                <span class="text-[10px] font-medium text-gray-400 uppercase">Saldo Tersedia</span>
                <p class="text-2xl font-bold text-gray-900 font-mono tracking-tight">
                    {{ $w->currency == 'USD' ? '$' : 'Rp' }} {{ number_format($w->balance, 2, ',', '.') }}
                </p>
            </div>
        </div>
        @empty
        <div class="col-span-full py-16 text-center border-2 border-dashed border-gray-200 rounded-2xl bg-gray-50">
            <div
                class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-200">
                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                    </path>
                </svg>
            </div>
            <h3 class="text-gray-900 font-bold">Belum Ada Dompet</h3>
            <p class="text-gray-500 text-sm mt-1 mb-6">Tambahkan akun bank atau e-wallet Anda.</p>
            <button @click="showCreateModal = true"
                class="px-6 py-2.5 bg-black text-white rounded-xl font-bold text-sm hover:bg-gray-800 transition shadow-sm">
                Buat Dompet Baru
            </button>
        </div>
        @endforelse
    </div>

    {{-- MODAL CREATE --}}
    <div x-show="showCreateModal" style="display: none;"
        class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showCreateModal = false" x-transition.opacity>
        </div>

        <div class="relative bg-white rounded-2xl w-full max-w-md shadow-2xl p-8 transform transition-all"
            x-transition.scale>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-black">Dompet Baru</h3>
                <button @click="showCreateModal = false"
                    class="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 flex items-center justify-center text-gray-500 transition">✕</button>
            </div>

            <form action="{{ route('wallet.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Platform / Bank</label>
                    <input type="text" name="bank_name" placeholder="BCA, Bibit, GoPay"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-black outline-none transition font-medium"
                        required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Label Akun
                        (Penting)</label>
                    <input type="text" name="account_name" placeholder="RDN Utama, Tabungan Harian"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-black outline-none transition font-medium"
                        required>
                    <p class="text-[10px] text-gray-400 mt-1 ml-1">Untuk membedakan jika ada banyak akun di bank yang
                        sama.</p>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">No. Rekening</label>
                        <input type="text" name="account_number" placeholder="123xxx"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-black outline-none transition font-mono text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Mata Uang</label>
                        <select name="currency"
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-1 focus:ring-black outline-none transition font-bold cursor-pointer">
                            <option value="IDR">IDR (Rp)</option>
                            <option value="USD">USD ($)</option>
                        </select>
                    </div>
                </div>
                <button type="submit"
                    class="w-full bg-black text-white font-bold py-4 rounded-xl hover:bg-gray-800 transition active:scale-[0.98] mt-2 shadow-md">Simpan</button>
            </form>
        </div>
    </div>
</div>
<style>
[x-cloak] {
    display: none !important;
}
</style>
@endsection