@extends('layouts.app')

@section('title', 'Isi Saldo')

@section('content')
<div class="min-h-screen bg-slate-50 py-12">
    <div class="max-w-xl mx-auto px-4">

        {{-- Header --}}
        <div class="mb-8 text-center">
            <div
                class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl shadow-sm">
                📥
            </div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Isi Saldo</h1>
            <p class="text-slate-500 mt-2">Catat penambahan saldo dompet secara instan.</p>
        </div>

        {{-- CARD FORM --}}
        <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100 relative overflow-hidden">

            {{-- Hiasan Background --}}
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-bl-full -mr-10 -mt-10 z-0"></div>

            <form action="{{ route('topup.process') }}" method="POST" class="space-y-6 relative z-10">
                @csrf

                {{-- 1. PILIH DOMPET --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wider">Ke Akun
                        Mana?</label>
                    <div class="relative">
                        <select name="wallet_id" id="walletSelect"
                            class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-slate-700 appearance-none cursor-pointer transition hover:bg-slate-100"
                            required>
                            <option value="" disabled selected>-- Pilih Dompet --</option>
                            @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}" data-currency="{{ $wallet->currency }}">
                                {{ $wallet->bank_name }} - {{ $wallet->account_name }} ({{ $wallet->currency }})
                            </option>
                            @endforeach
                        </select>
                        <div class="absolute left-4 top-4 text-xl">💳</div>
                        <div class="absolute right-4 top-4 text-slate-400"><i class="fas fa-chevron-down"></i></div>
                    </div>
                </div>

                {{-- 2. NOMINAL --}}
                <div x-data="{ amount: '' }">
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wider">Jumlah
                        Penambahan</label>
                    <div class="relative mb-4">
                        <span id="currencySymbol" class="absolute left-4 top-4 text-slate-400 font-bold">Rp</span>
                        <input type="number" step="any" name="amount" x-model="amount"
                            class="w-full pl-12 pr-4 py-4 text-3xl font-black text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500 transition placeholder-slate-300"
                            placeholder="0" required>
                    </div>

                    {{-- Preset Tombol (IDR) --}}
                    <div id="idrPresets" class="grid grid-cols-3 gap-2 hidden">
                        <button type="button" @click="amount = 100000"
                            class="py-2 rounded-lg bg-slate-50 border border-slate-200 text-slate-600 font-bold text-xs hover:bg-indigo-50 hover:text-indigo-600 transition">+100rb</button>
                        <button type="button" @click="amount = 500000"
                            class="py-2 rounded-lg bg-slate-50 border border-slate-200 text-slate-600 font-bold text-xs hover:bg-indigo-50 hover:text-indigo-600 transition">+500rb</button>
                        <button type="button" @click="amount = 1000000"
                            class="py-2 rounded-lg bg-slate-50 border border-slate-200 text-slate-600 font-bold text-xs hover:bg-indigo-50 hover:text-indigo-600 transition">+1
                            Juta</button>
                    </div>
                </div>

                {{-- 3. TANGGAL TRANSAKSI (BARU) --}}
                <div class="pt-2">
                    <label
                        class="flex items-center gap-2 text-slate-400 text-xs font-bold cursor-pointer w-fit hover:text-indigo-500 transition"
                        onclick="document.getElementById('dateContainer').classList.toggle('hidden')">
                        <i class="fas fa-calendar-alt"></i> Atur Tanggal Transaksi (Backdate)
                    </label>
                    <div id="dateContainer" class="hidden mt-2">
                        <input type="datetime-local" name="custom_date"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-600 text-sm rounded-xl p-3 focus:ring-indigo-500 focus:border-indigo-500">
                        <p class="text-[10px] text-slate-400 mt-1">*Biarkan kosong untuk menggunakan waktu sekarang.</p>
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- TOMBOL SUBMIT --}}
                <button type="submit"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-500/30 transition transform active:scale-95 flex justify-center items-center gap-2 group">
                    <span>Tambah Saldo Sekarang</span>
                    <i class="fas fa-plus-circle group-hover:rotate-90 transition"></i>
                </button>
            </form>
        </div>

        <p class="text-center text-slate-400 text-xs mt-6">Saldo akan langsung ditambahkan ke portofolio Anda.</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
const walletSelect = document.getElementById('walletSelect');
const currencySymbol = document.getElementById('currencySymbol');
const idrPresets = document.getElementById('idrPresets');

walletSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const currency = selectedOption.getAttribute('data-currency');
    currencySymbol.innerText = (currency === 'USD') ? '$' : 'Rp';
    if (currency === 'IDR') {
        idrPresets.classList.remove('hidden');
    } else {
        idrPresets.classList.add('hidden');
    }
});
</script>
@endsection