@extends('layouts.app')

@section('title', 'Dompet & Aset')
@section('header', 'My Wallet')

@section('content')
<div class="min-h-screen" x-data="{ showCreateModal: false }">
    {{-- SECTION 1: TOTAL WEALTH ESTIMATION --}}
    <div
        class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900 mb-8 text-white shadow-lg">
        {{-- Animated Background Lines --}}
        <div class="absolute inset-0 opacity-5">
            <div class="absolute inset-0"
                style="background-image: linear-gradient(90deg, transparent 50%, rgba(255,255,255,.1) 50%); background-size: 20px 20px;">
            </div>
        </div>

        <div class="relative z-10 p-8 flex flex-col justify-between">
            <div class="mb-6">
                <div class="flex items-center gap-3 mb-3">
                    <div
                        class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-sm flex items-center justify-center border border-white/20">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div>
                        <span class="text-gray-300 text-sm font-medium uppercase tracking-wider">
                            Total Estimasi Aset
                        </span>
                        <h1 class="text-4xl md:text-5xl font-bold text-white tracking-tight font-mono mt-1">
                            Rp {{ number_format($totalBalance, 0, ',', '.') }}
                        </h1>
                    </div>
                </div>
                <p class="text-gray-400 text-sm">
                    Gabungan seluruh saldo tunai & valuasi aset saat ini.
                </p>
            </div>

            {{-- Primary Action Button --}}
            <button @click="showCreateModal = true"
                class="self-start px-6 py-3 bg-white text-gray-900 font-medium rounded-xl transition hover:bg-gray-100 hover:shadow-lg active:scale-95">
                <span class="flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4">
                        </path>
                    </svg>
                    Tambah Dompet
                </span>
            </button>
        </div>
    </div>

    {{-- SECTION 2: QUICK ACTION MENU --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-12">
        {{-- Deposit --}}
        <a href="{{ route('topup') }}"
            class="group bg-white border border-gray-200 p-5 rounded-xl flex flex-col items-center justify-center gap-3 transition-all hover:border-gray-300 hover:shadow-md">
            <div
                class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center group-hover:bg-gray-900 group-hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
            </div>
            <span class="text-gray-600 font-medium text-sm group-hover:text-gray-900">Deposit</span>
        </a>

        {{-- Withdraw --}}
        <a href="{{ route('withdraw') }}"
            class="group bg-white border border-gray-200 p-5 rounded-xl flex flex-col items-center justify-center gap-3 transition-all hover:border-gray-300 hover:shadow-md">
            <div
                class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center group-hover:bg-gray-900 group-hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                </svg>
            </div>
            <span class="text-gray-600 font-medium text-sm group-hover:text-gray-900">Withdraw</span>
        </a>

        {{-- Trade --}}
        <a href="{{ route('buy') }}"
            class="group bg-white border border-gray-200 p-5 rounded-xl flex flex-col items-center justify-center gap-3 transition-all hover:border-gray-300 hover:shadow-md">
            <div
                class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center group-hover:bg-gray-900 group-hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                </svg>
            </div>
            <span class="text-gray-600 font-medium text-sm group-hover:text-gray-900">Trade</span>
        </a>

        {{-- Exchange --}}
        <a href="{{ route('exchange.index') }}"
            class="group bg-white border border-gray-200 p-5 rounded-xl flex flex-col items-center justify-center gap-3 transition-all hover:border-gray-300 hover:shadow-md">
            <div
                class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center group-hover:bg-gray-900 group-hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                </svg>
            </div>
            <span class="text-gray-600 font-medium text-sm group-hover:text-gray-900">Exchange</span>
        </a>
    </div>

    {{-- SECTION 3: WALLET CARDS --}}
    <div>
        <div class="flex items-end justify-between mb-8">
            <div>
                <h3 class="text-2xl font-bold text-gray-900">Akun Dompet</h3>
                <p class="text-gray-500 text-sm mt-1">Kelola sumber dana transaksi Anda</p>
            </div>
            <div class="flex items-center gap-4">
                <a href="#"
                    class="text-sm font-medium text-gray-600 hover:text-gray-900 transition flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    Riwayat
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($wallets as $w)
            <div
                class="group relative bg-white border border-gray-200 rounded-2xl p-6 flex flex-col justify-between hover:border-gray-300 hover:shadow-lg transition-all duration-300 h-80 overflow-hidden">
                {{-- Subtle pattern background --}}
                <div class="absolute inset-0 opacity-5 pointer-events-none">
                    <div class="absolute inset-0"
                        style="background-image: radial-gradient(circle at 2px 2px, #000 1px, transparent 1px); background-size: 20px 20px;">
                    </div>
                </div>

                {{-- Card Header --}}
                <div class="relative z-10 flex justify-between items-start mb-4">
                    <div class="flex flex-col flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span
                                class="text-xs font-medium uppercase tracking-wider text-gray-500">{{ $w->bank_name }}</span>
                            <span
                                class="px-2 py-1 rounded-md bg-gray-100 text-gray-800 font-medium text-xs border border-gray-200">
                                {{ $w->currency }}
                            </span>
                        </div>
                        <span class="text-lg font-semibold text-gray-900 truncate">{{ $w->account_name }}</span>
                    </div>

                    {{-- Actions - Always Visible --}}
                    <div class="flex gap-1 flex-shrink-0">
                        <a href="{{ route('wallet.show', $w->id) }}"
                            class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition"
                            title="Lihat Detail">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                </path>
                            </svg>
                        </a>
                        <a href="{{ route('wallet.edit', $w->id) }}"
                            class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 flex items-center justify-center transition"
                            title="Edit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                                </path>
                            </svg>
                        </a>
                    </div>
                </div>

                {{-- Chip & Number --}}
                <div class="relative z-10 my-4 flex-1">
                    <div
                        class="w-10 h-8 rounded bg-gradient-to-br from-gray-300 to-gray-400 shadow-sm mb-4 relative overflow-hidden">
                        <div class="absolute inset-1 rounded-sm bg-gradient-to-br from-gray-100 to-gray-300"></div>
                        <div
                            class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-4 h-0.5 bg-gray-400">
                        </div>
                    </div>
                    <p class="font-mono text-xl text-gray-900 tracking-widest bg-gray-50 px-3 py-2 rounded-lg">
                        {{ chunk_split($w->account_number ?? '0000', 4, ' ') }}
                    </p>
                </div>

                {{-- Balance Section --}}
                <div class="relative z-10 mt-4 pt-4 border-t border-gray-100">
                    <div class="flex justify-between items-center">
                        <div class="flex-1 min-w-0">
                            <span class="text-xs text-gray-500 uppercase font-medium block">Saldo Tersedia</span>
                            <p class="text-2xl font-bold text-gray-900 font-mono mt-1 truncate">
                                {{ $w->currency == 'USD' ? '$' : 'Rp' }} {{ number_format($w->balance, 2, ',', '.') }}
                            </p>
                        </div>

                        {{-- Delete Button - Fixed Position --}}
                        <div class="flex-shrink-0 ml-3">
                            <form action="{{ route('wallet.destroy', $w->id) }}" method="POST"
                                onsubmit="return confirm('Hapus dompet ini?');">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-8 h-8 rounded-lg bg-gray-100 hover:bg-red-50 text-gray-700 hover:text-red-600 flex items-center justify-center transition border border-gray-200 hover:border-red-200"
                                    title="Hapus Dompet">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                        </path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Additional Info --}}
                    <div class="mt-3 flex items-center gap-2 text-sm text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-xs">Update: {{ now()->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full py-16 text-center border-2 border-dashed border-gray-300 rounded-2xl bg-gray-50">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-200 mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                        </path>
                    </svg>
                </div>
                <h3 class="text-gray-900 font-bold text-lg">Belum Ada Dompet</h3>
                <p class="text-gray-600 mb-6 mt-2">Tambahkan akun bank atau e-wallet untuk mulai bertransaksi.</p>
                <button @click="showCreateModal = true"
                    class="px-6 py-3 bg-gray-900 hover:bg-gray-800 text-white rounded-xl font-medium transition shadow-sm hover:shadow-md">
                    <span class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        Buat Dompet Baru
                    </span>
                </button>
            </div>
            @endforelse
        </div>
    </div>

    {{-- SECTION 4: CREATE WALLET MODAL --}}
    <div x-show="showCreateModal" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4"
        @click.away="showCreateModal = false">
        <div class="bg-white rounded-2xl w-full max-w-md p-8 shadow-xl" @click.stop>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-bold text-gray-900">Tambah Dompet Baru</h3>
                <button @click="showCreateModal = false" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <form action="{{ route('wallet.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Bank</label>
                        <input type="text" name="bank_name" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-gray-500 focus:ring-1 focus:ring-gray-500 outline-none transition"
                            placeholder="Contoh: BCA, Mandiri, etc.">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nama Akun</label>
                        <input type="text" name="account_name" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-gray-500 focus:ring-1 focus:ring-gray-500 outline-none transition"
                            placeholder="Nama pemilik akun">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Akun</label>
                        <input type="text" name="account_number" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-gray-500 focus:ring-1 focus:ring-gray-500 outline-none transition"
                            placeholder="Nomor rekening">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Mata Uang</label>
                        <select name="currency" required
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:border-gray-500 focus:ring-1 focus:ring-gray-500 outline-none transition bg-white">
                            <option value="IDR">IDR (Rupiah)</option>
                            <option value="USD">USD (Dollar)</option>
                        </select>
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="button" @click="showCreateModal = false"
                            class="flex-1 px-4 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-3 bg-gray-900 text-white rounded-xl font-medium hover:bg-gray-800 transition">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- SECTION 5: FOOTER SPACER --}}
    <div class="h-20"></div>
</div>

<style>
[x-cloak] {
    display: none !important;
}
</style>
@endsection