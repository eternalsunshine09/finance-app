@extends('layouts.app')

@section('title', 'Isi Saldo')

@section('content')
<div class="min-h-screen bg-slate-50 py-12">
    <div class="max-w-xl mx-auto px-4">

        {{-- Header --}}
        <div class="mb-8 text-center">
            <div
                class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl shadow-sm border border-indigo-50">
                📥
            </div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Isi Saldo</h1>
            <p class="text-slate-500 mt-2 font-medium">Catat penambahan saldo dompet secara instan.</p>
        </div>

        {{-- CARD FORM --}}
        <div
            class="bg-white p-8 rounded-[2rem] shadow-xl shadow-slate-200/60 border border-slate-100 relative overflow-hidden">

            {{-- Hiasan Background --}}
            <div
                class="absolute top-0 right-0 w-40 h-40 bg-indigo-50 rounded-full blur-3xl -mr-10 -mt-10 pointer-events-none">
            </div>

            <form action="{{ route('topup.process') }}" method="POST" class="space-y-6 relative z-10">
                @csrf

                {{-- 1. PILIH DOMPET --}}
                <div>
                    <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 ml-1">Ke Akun Mana?</label>
                    <div class="relative">
                        <select name="wallet_id" id="walletSelect"
                            class="w-full pl-12 pr-10 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 font-bold text-slate-700 appearance-none cursor-pointer transition hover:bg-slate-100/50"
                            required>
                            <option value="" disabled selected>-- Pilih Dompet --</option>
                            @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}" data-currency="{{ $wallet->currency }}">
                                {{ $wallet->bank_name }} - {{ $wallet->account_name }} ({{ $wallet->currency }})
                            </option>
                            @endforeach
                        </select>
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-xl">💳</div>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- 2. NOMINAL --}}
                <div x-data="{ amount: '' }">
                    <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 ml-1">Jumlah
                        Penambahan</label>
                    <div class="relative mb-4">
                        <span id="currencySymbol"
                            class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold text-lg">Rp</span>
                        <input type="number" step="any" name="amount" x-model="amount"
                            class="w-full pl-14 pr-4 py-4 text-3xl font-black text-slate-800 bg-white border border-slate-200 rounded-2xl focus:ring-4 focus:ring-indigo-500/10 focus:border-indigo-500 transition placeholder-slate-200"
                            placeholder="0" required>
                    </div>

                    {{-- Preset Tombol (IDR) --}}
                    <div id="idrPresets" class="grid grid-cols-3 gap-3 hidden">
                        <button type="button" @click="amount = 100000"
                            class="py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 font-bold text-xs hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition">+100rb</button>
                        <button type="button" @click="amount = 500000"
                            class="py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 font-bold text-xs hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition">+500rb</button>
                        <button type="button" @click="amount = 1000000"
                            class="py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-slate-600 font-bold text-xs hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition">+1
                            Juta</button>
                    </div>
                </div>

                {{-- 3. TANGGAL TRANSAKSI (OPTIONAL) --}}
                <div class="pt-2">
                    <label
                        class="flex items-center gap-2 text-slate-400 text-xs font-bold cursor-pointer w-fit hover:text-indigo-500 transition select-none"
                        onclick="document.getElementById('dateContainer').classList.toggle('hidden')">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        Atur Tanggal Transaksi (Backdate)
                    </label>
                    <div id="dateContainer" class="hidden mt-3 animate-fade-in-down">
                        <input type="datetime-local" name="created_at"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-600 text-sm rounded-xl p-3 focus:ring-indigo-500 focus:border-indigo-500 font-medium">
                        <p class="text-[10px] text-slate-400 mt-1.5 ml-1 font-medium">*Biarkan kosong untuk menggunakan
                            waktu sekarang.</p>
                    </div>
                </div>

                <div class="border-t border-slate-100 pt-2"></div>

                {{-- TOMBOL SUBMIT --}}
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-2xl shadow-xl shadow-indigo-500/20 transition transform active:scale-[0.98] flex justify-center items-center gap-2 group">
                    <span>Tambah Saldo Sekarang</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </form>
        </div>

        <p class="text-center text-slate-400 text-xs mt-8 font-medium">Saldo akan langsung ditambahkan ke portofolio
            Anda.</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const walletSelect = document.getElementById('walletSelect');
    const currencySymbol = document.getElementById('currencySymbol');
    const idrPresets = document.getElementById('idrPresets');

    function updateCurrency() {
        const selectedOption = walletSelect.options[walletSelect.selectedIndex];
        // Pastikan option terpilih memiliki data-currency
        if (selectedOption && selectedOption.hasAttribute('data-currency')) {
            const currency = selectedOption.getAttribute('data-currency');
            currencySymbol.innerText = (currency === 'USD') ? '$' : 'Rp';

            if (currency === 'IDR') {
                idrPresets.classList.remove('hidden');
            } else {
                idrPresets.classList.add('hidden');
            }
        }
    }

    if (walletSelect) {
        walletSelect.addEventListener('change', updateCurrency);
        // Panggil sekali saat load jika ada nilai old input atau default
        updateCurrency();
    }
});
</script>
@endsection