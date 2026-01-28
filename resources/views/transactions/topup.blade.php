@extends('layouts.app')

@section('title', 'Isi Saldo')
@section('header', 'Deposit Dana')

@section('content')
<div class="min-h-screen bg-white py-12">
    <div class="max-w-xl mx-auto px-4">

        {{-- Header Icon --}}
        <div class="mb-10 text-center">
            <div
                class="w-16 h-16 bg-black text-white rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg shadow-gray-200 transform rotate-3">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-black text-gray-900 tracking-tight">Isi Saldo</h1>
            <p class="text-gray-500 mt-2 font-medium text-sm">Catat penambahan dana tunai ke dompet Anda.</p>
        </div>

        {{-- CARD FORM --}}
        <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-xl relative overflow-hidden">

            {{-- Hiasan Background Abstrak --}}
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-gray-50 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none">
            </div>

            <form action="{{ route('topup.process') }}" method="POST" class="space-y-8 relative z-10"
                x-data="{ amount: '' }">
                @csrf

                {{-- 1. PILIH DOMPET --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1 tracking-wider">Ke Akun
                        Mana?</label>
                    <div class="relative group">
                        <select name="wallet_id" id="walletSelect"
                            class="w-full pl-12 pr-10 py-4 bg-gray-50 border border-gray-200 rounded-2xl focus:bg-white focus:ring-1 focus:ring-black focus:border-black font-bold text-gray-900 appearance-none cursor-pointer transition outline-none"
                            required>
                            <option value="" disabled selected>-- Pilih Dompet --</option>
                            @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}" data-currency="{{ $wallet->currency }}">
                                {{ $wallet->bank_name }} - {{ $wallet->account_name }}
                            </option>
                            @endforeach
                        </select>
                        <div
                            class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-hover:text-black transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                </path>
                            </svg>
                        </div>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- 2. NOMINAL --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1 tracking-wider">Jumlah
                        Penambahan</label>

                    <div class="relative mb-4">
                        <span id="currencySymbol"
                            class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-lg">Rp</span>
                        <input type="number" step="any" name="amount" x-model="amount" id="amountInput"
                            class="w-full pl-14 pr-4 py-4 text-3xl font-black text-gray-900 bg-white border border-gray-200 rounded-2xl focus:ring-1 focus:ring-black focus:border-black transition placeholder-gray-200 outline-none"
                            placeholder="0" required>
                    </div>

                    {{-- Preset Tombol (IDR Only) --}}
                    <div id="idrPresets" class="grid grid-cols-3 gap-3 hidden">
                        <button type="button" @click="amount = 100000"
                            class="py-3 rounded-xl bg-white border border-gray-200 text-gray-600 font-bold text-xs hover:bg-black hover:text-white hover:border-black transition shadow-sm">
                            +100rb
                        </button>
                        <button type="button" @click="amount = 500000"
                            class="py-3 rounded-xl bg-white border border-gray-200 text-gray-600 font-bold text-xs hover:bg-black hover:text-white hover:border-black transition shadow-sm">
                            +500rb
                        </button>
                        <button type="button" @click="amount = 1000000"
                            class="py-3 rounded-xl bg-white border border-gray-200 text-gray-600 font-bold text-xs hover:bg-black hover:text-white hover:border-black transition shadow-sm">
                            +1 Juta
                        </button>
                    </div>
                </div>

                {{-- 3. TANGGAL TRANSAKSI (Expandable) --}}
                <div class="pt-2 border-t border-gray-100" x-data="{ showDate: false }">
                    <label
                        class="flex items-center gap-2 text-gray-400 text-xs font-bold cursor-pointer w-fit hover:text-black transition select-none mt-4"
                        @click="showDate = !showDate">
                        <div class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        Atur Tanggal Transaksi (Backdate)
                    </label>
                    <div x-show="showDate" class="mt-3" x-transition>
                        <input type="datetime-local" name="created_at"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-3 focus:ring-black focus:border-black font-medium outline-none transition">
                        <p class="text-[10px] text-gray-400 mt-1.5 ml-1 font-medium">*Biarkan kosong untuk menggunakan
                            waktu sekarang.</p>
                    </div>
                </div>

                {{-- TOMBOL SUBMIT --}}
                <button type="submit"
                    class="w-full bg-black hover:bg-gray-800 text-white font-bold py-4 rounded-2xl shadow-lg transition transform active:scale-[0.98] flex justify-center items-center gap-2 group mt-2">
                    <span>Konfirmasi Top Up</span>
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>

            </form>
        </div>

        <p class="text-center text-gray-400 text-xs mt-8">Saldo akan langsung ditambahkan ke portofolio Anda.</p>
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
        // Panggil sekali saat load
        updateCurrency();
    }
});
</script>
@endsection