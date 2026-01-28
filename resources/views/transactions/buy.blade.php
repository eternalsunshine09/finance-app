@extends('layouts.app')

@section('title', 'Beli Aset')
@section('header', 'Investasi Baru')
@section('header_description', 'Diversifikasi portofolio Anda dengan membeli aset baru.')

@section('content')
<div class="max-w-5xl mx-auto py-8">

    {{-- Error Message --}}
    @if ($errors->any())
    <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r shadow-sm text-sm">
        <p class="font-bold mb-1">Gagal Memproses:</p>
        <ul class="list-disc ml-5 space-y-1">
            @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col lg:flex-row">

        {{-- KOLOM KIRI (FORM INPUT) --}}
        <div class="lg:w-3/5 p-8 lg:p-10 bg-white">
            <form action="{{ route('buy.process') }}" method="POST" class="space-y-8" id="buyForm">
                @csrf

                {{-- 1. SUMBER DANA --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">1. Sumber
                        Dana</label>
                    <div class="relative group">
                        <select name="wallet_id" id="walletSelect"
                            class="w-full pl-12 pr-10 py-3.5 bg-gray-50 border border-gray-200 rounded-xl font-bold text-gray-800 appearance-none cursor-pointer focus:ring-1 focus:ring-black focus:border-black transition-all outline-none"
                            required>
                            <option value="" disabled selected>-- Pilih Dompet --</option>
                            @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}" data-balance="{{ $wallet->balance }}"
                                data-currency="{{ $wallet->currency }}"
                                data-flag="{{ $wallet->currency == 'IDR' ? 'id' : 'us' }}">
                                {{ $wallet->bank_name }} - {{ $wallet->currency }}
                            </option>
                            @endforeach
                        </select>
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                    <div class="mt-2 flex justify-end">
                        <span class="inline-flex items-center gap-2 text-gray-500 text-xs font-medium">
                            <img id="currencyFlag" src=""
                                class="w-4 h-3 rounded shadow-sm hidden object-cover grayscale opacity-80">
                            Saldo Tersedia: <span id="walletBalanceDisplay" class="font-bold text-gray-900">Rp 0</span>
                        </span>
                    </div>
                </div>

                <div class="border-t border-gray-100"></div>

                {{-- 2. DETAIL ASET --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">2. Detail
                        Aset</label>
                    <div class="space-y-5">

                        {{-- Pilih Produk --}}
                        <div class="relative group">
                            <select name="asset_symbol" id="assetSelect"
                                class="w-full pl-12 pr-10 py-3.5 bg-white border border-gray-200 rounded-xl font-bold text-gray-800 appearance-none cursor-pointer focus:ring-1 focus:ring-black focus:border-black transition-all outline-none"
                                required>
                                <option value="" data-price="0">-- Pilih Aset Investasi --</option>
                                @foreach($assets as $asset)
                                <option value="{{ $asset->symbol }}" data-type="{{ $asset->type }}"
                                    data-price="{{ $asset->current_price }}">
                                    {{ $asset->symbol }} - {{ $asset->name }}
                                </option>
                                @endforeach
                            </select>
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>
                            </div>
                            <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>

                        {{-- Harga Beli --}}
                        <div class="relative">
                            <span id="currencyLabel"
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">Rp</span>
                            <input type="number" step="any" name="buy_price" id="buyPriceInput"
                                class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl font-bold text-gray-800 focus:bg-white focus:ring-1 focus:ring-black focus:border-black transition placeholder-gray-400"
                                placeholder="Harga per Unit" required>
                        </div>
                    </div>
                </div>

                {{-- 3. KUANTITAS & FEE --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">3.
                            Kuantitas</label>
                        <input type="number" step="0.00000001" name="amount" id="amountInput"
                            class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl font-bold text-gray-800 focus:bg-white focus:border-black focus:ring-1 focus:ring-black transition placeholder-gray-400"
                            placeholder="0.00" required>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">4. Biaya
                            (Fee)</label>
                        <div class="relative">
                            <span id="feeCurrencyLabel"
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xs">Rp</span>
                            <input type="number" step="any" name="fee" id="feeInput"
                                class="w-full pl-10 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl font-bold text-gray-600 focus:bg-white focus:ring-1 focus:ring-black focus:border-black transition placeholder-gray-400"
                                placeholder="0" value="0">
                        </div>
                    </div>
                </div>

                {{-- 5. TANGGAL TRANSAKSI (FITUR BARU) --}}
                <div class="pt-2" x-data="{ showDate: false }">
                    <label
                        class="flex items-center gap-2 text-gray-400 text-xs font-bold cursor-pointer w-fit hover:text-black transition select-none"
                        @click="showDate = !showDate">
                        <div class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        Atur Tanggal Pembelian (Backdate)
                    </label>
                    <div x-show="showDate" class="mt-3" x-transition>
                        <input type="datetime-local" name="created_at"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-3 focus:ring-black focus:border-black font-medium outline-none">
                        <p class="text-[10px] text-gray-400 mt-1.5 ml-1 font-medium">*Biarkan kosong untuk menggunakan
                            waktu sekarang.</p>
                    </div>
                </div>

                <button type="submit" id="realSubmitBtn" class="hidden"></button>
            </form>
        </div>

        {{-- KOLOM KANAN (SUMMARY - HITAM) --}}
        <div class="lg:w-2/5 bg-black p-8 lg:p-10 text-white flex flex-col justify-between relative overflow-hidden">

            {{-- Dekorasi Abstrak --}}
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-gray-800 rounded-full blur-3xl -mr-16 -mt-16 opacity-50 pointer-events-none">
            </div>

            <div class="relative z-10">
                <h3 class="text-lg font-bold mb-8 flex items-center gap-2 text-white">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    Ringkasan Order
                </h3>

                <div class="space-y-5 text-sm">
                    <div class="flex justify-between text-gray-400">
                        <span>Harga Aset</span>
                        <span id="summaryPrice" class="font-mono text-white font-medium">0</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>Jumlah Unit</span>
                        <span id="summaryAmount" class="font-mono text-white font-medium">0</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>Biaya Broker</span>
                        <span id="summaryFee" class="font-mono text-gray-300 font-medium">+ 0</span>
                    </div>

                    <div class="h-px bg-gray-800 my-6"></div>

                    <div class="flex justify-between items-end">
                        <span class="text-gray-300 font-bold uppercase text-xs tracking-wider">Total Bayar</span>
                        <span id="totalDisplay" class="text-3xl font-bold text-white tracking-tight">0</span>
                    </div>
                </div>

                {{-- Alert Saldo Kurang --}}
                <div id="insufficientBalanceMsg"
                    class="hidden mt-6 bg-red-900/30 border border-red-500/30 p-4 rounded-xl flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <div>
                        <p class="text-xs font-bold text-red-400 uppercase tracking-wide">Saldo Tidak Cukup</p>
                        <p class="text-[11px] text-red-200 mt-1">Silakan lakukan deposit atau kurangi jumlah pembelian.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Tombol Bayar --}}
            <div class="mt-8 relative z-10">
                <button type="button" onclick="document.getElementById('realSubmitBtn').click()" id="submitBtn"
                    class="w-full group bg-white hover:bg-gray-100 text-black font-bold py-4 rounded-xl shadow-lg transition-all transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-3">
                    <span>Konfirmasi Pembelian</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Elements
const walletSelect = document.getElementById('walletSelect');
const assetSelect = document.getElementById('assetSelect');

// Inputs
const buyPriceInput = document.getElementById('buyPriceInput');
const amountInput = document.getElementById('amountInput');
const feeInput = document.getElementById('feeInput');

// UI Elements
const summaryPrice = document.getElementById('summaryPrice');
const summaryAmount = document.getElementById('summaryAmount');
const summaryFee = document.getElementById('summaryFee');
const totalDisplay = document.getElementById('totalDisplay');

const insufficientBalanceMsg = document.getElementById('insufficientBalanceMsg');
const submitBtn = document.getElementById('submitBtn');
const walletBalanceDisplay = document.getElementById('walletBalanceDisplay');
const currencyFlag = document.getElementById('currencyFlag');
const currencyLabel = document.getElementById('currencyLabel');
const feeCurrencyLabel = document.getElementById('feeCurrencyLabel');

// ---------------------------------------------------------
// 1. AUTO-SELECT ASSET DARI URL PARAMETER (FIX UTAMA)
// ---------------------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
    // Ambil parameter 'symbol' dari URL (misal: ?symbol=BBCA)
    const urlParams = new URLSearchParams(window.location.search);
    const symbolParam = urlParams.get('symbol');

    if (symbolParam) {
        // Cari option yang value-nya sama dengan symbol
        // Kita loop karena value option mungkin formatnya beda, tapi di sini value="{{ $asset->symbol }}"
        for (let i = 0; i < assetSelect.options.length; i++) {
            if (assetSelect.options[i].value === symbolParam) {
                assetSelect.selectedIndex = i;
                // Trigger event change manual agar harga terisi otomatis
                assetSelect.dispatchEvent(new Event('change'));
                break;
            }
        }
    }
});

// 2. Logic Pilih Aset
assetSelect.addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const type = option.getAttribute('data-type');
    const price = option.getAttribute('data-price');

    // Set Currency Label ($/Rp)
    const symbolCurrency = (type === 'Crypto' || type === 'US Stock') ? '$' : 'Rp';
    currencyLabel.innerText = symbolCurrency;
    feeCurrencyLabel.innerText = symbolCurrency;

    // Auto isi harga jika ada
    if (price && price > 0) {
        buyPriceInput.value = price;
    }

    calculate();
});

// 3. Kalkulasi Total
function calculate() {
    const price = parseFloat(buyPriceInput.value) || 0;
    const amount = parseFloat(amountInput.value) || 0;
    const fee = parseFloat(feeInput.value) || 0;

    const subtotal = price * amount;
    const total = subtotal + fee;

    // Ambil Data Wallet
    const walletOption = walletSelect.options[walletSelect.selectedIndex];

    // Default jika wallet belum dipilih
    let balance = 0;
    let currency = 'IDR';

    if (walletOption && !walletOption.disabled) {
        balance = parseFloat(walletOption.getAttribute('data-balance'));
        currency = walletOption.getAttribute('data-currency');
    }

    const formatter = new Intl.NumberFormat(currency === 'USD' ? 'en-US' : 'id-ID', {
        style: 'currency',
        currency: currency,
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });

    // Render ke UI
    summaryPrice.innerText = formatter.format(price);
    summaryAmount.innerText = amount;
    summaryFee.innerText = "+ " + formatter.format(fee);
    totalDisplay.innerText = formatter.format(total);

    // Validasi Saldo (Hanya jika wallet sudah dipilih)
    if (walletOption && !walletOption.disabled) {
        if (total > balance) {
            insufficientBalanceMsg.classList.remove('hidden');
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
            totalDisplay.classList.add('text-red-400');
            totalDisplay.classList.remove('text-white');
        } else {
            insufficientBalanceMsg.classList.add('hidden');
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            totalDisplay.classList.remove('text-red-400');
            totalDisplay.classList.add('text-white');
        }
    }
}

// 4. Update Wallet Info
walletSelect.addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (option && !option.disabled) {
        const balance = parseFloat(option.getAttribute('data-balance'));
        const currency = option.getAttribute('data-currency');
        const flag = option.getAttribute('data-flag');

        const formatter = new Intl.NumberFormat(currency === 'USD' ? 'en-US' : 'id-ID', {
            style: 'currency',
            currency: currency
        });
        walletBalanceDisplay.innerText = formatter.format(balance);
        currencyFlag.src = `https://flagcdn.com/w40/${flag}.png`;
        currencyFlag.classList.remove('hidden');
    }
    calculate();
});

// Listeners
buyPriceInput.addEventListener('input', calculate);
amountInput.addEventListener('input', calculate);
feeInput.addEventListener('input', calculate);
</script>
@endsection