@extends('layouts.app')

@section('title', 'Jual Aset')
@section('header', 'Likuidasi Aset')
@section('header_description', 'Cairkan investasi Saham, Crypto, atau Valas Anda menjadi saldo tunai.')

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
            <form action="{{ route('sell.process') }}" method="POST" class="space-y-8" id="sellForm">
                @csrf

                {{-- 1. PILIH ASET --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">1. Pilih Aset
                        yang Dimiliki</label>
                    <div class="relative group">
                        <select name="asset_symbol" id="assetSelect"
                            class="w-full pl-12 pr-10 py-3.5 bg-white border border-gray-200 rounded-xl font-bold text-gray-800 appearance-none cursor-pointer focus:ring-1 focus:ring-rose-500 focus:border-rose-500 transition-all outline-none"
                            required>
                            <option value="" data-price="0" data-quantity="0">-- Pilih Aset --</option>
                            @foreach($myPortfolio as $item)
                            <option value="{{ $item->asset_symbol }}" data-type="{{ $item->asset->type ?? 'Stock' }}"
                                data-price="{{ $item->asset->current_price ?? 0 }}"
                                data-quantity="{{ $item->quantity }}">
                                {{ $item->asset_symbol }} - {{ $item->asset->name ?? '' }} (Tersedia:
                                {{ number_format($item->quantity, 4) }})
                            </option>
                            @endforeach
                        </select>
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-rose-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>

                    {{-- Info Saldo Klik Cepat --}}
                    <div id="quickActionContainer" class="hidden mt-2 flex justify-end">
                        <button type="button" id="sellAllBtn"
                            class="text-xs font-bold text-rose-600 hover:text-rose-800 underline decoration-rose-300 decoration-2 underline-offset-2 transition">
                            Jual Semua (<span id="maxQtyDisplay">0</span>)
                        </button>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Harga Jual --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">2. Harga
                            Jual</label>
                        <div class="relative">
                            <span id="currencyLabel"
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">Rp</span>
                            <input type="number" step="any" name="sell_price" id="sellPriceInput"
                                class="w-full pl-12 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl font-bold text-gray-800 focus:bg-white focus:ring-1 focus:ring-rose-500 focus:border-rose-500 transition placeholder-gray-400"
                                placeholder="0" required>
                        </div>
                    </div>

                    {{-- Jumlah Jual --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">
                            3. <span id="unitLabel">Unit</span> Dijual
                        </label>
                        <input type="number" step="0.00000001" name="amount" id="amountInput"
                            class="w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl font-bold text-gray-800 focus:bg-white focus:border-rose-500 focus:ring-1 focus:ring-rose-500 transition placeholder-gray-400"
                            placeholder="0.00" required>
                    </div>
                </div>

                {{-- Fee & Wallet --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Fee --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">4. Fee
                            Broker</label>
                        <div class="relative">
                            <span id="feeCurrencyLabel"
                                class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-xs">Rp</span>
                            <input type="number" step="any" name="fee" id="feeInput"
                                class="w-full pl-10 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl font-bold text-gray-600 focus:bg-white focus:ring-1 focus:ring-rose-500 focus:border-rose-500 transition placeholder-gray-400"
                                placeholder="0" value="0">
                        </div>
                    </div>

                    {{-- Tujuan Dana --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">5.
                            Simpan Hasil Ke</label>
                        <div class="relative">
                            <select name="wallet_id" id="walletSelect"
                                class="w-full pl-10 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl font-bold text-gray-800 appearance-none cursor-pointer focus:ring-1 focus:ring-rose-500 focus:border-rose-500 transition-all outline-none"
                                required>
                                @foreach($wallets as $wallet)
                                <option value="{{ $wallet->id }}">
                                    {{ $wallet->bank_name }} ({{ $wallet->currency }})
                                </option>
                                @endforeach
                            </select>
                            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                    </path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tanggal (Backdate) --}}
                <div class="pt-2" x-data="{ showDate: false }">
                    <label
                        class="flex items-center gap-2 text-gray-400 text-xs font-bold cursor-pointer w-fit hover:text-rose-600 transition select-none"
                        @click="showDate = !showDate">
                        <div class="w-5 h-5 rounded-full bg-rose-50 flex items-center justify-center">
                            <svg class="w-3 h-3 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        Atur Tanggal Penjualan (Backdate)
                    </label>
                    <div x-show="showDate" class="mt-3" x-transition>
                        <input type="datetime-local" name="created_at"
                            class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl p-3 focus:ring-rose-500 focus:border-rose-500 font-medium outline-none">
                        <p class="text-[10px] text-gray-400 mt-1.5 ml-1 font-medium">*Biarkan kosong untuk menggunakan
                            waktu sekarang.</p>
                    </div>
                </div>

                <button type="submit" id="realSubmitBtn" class="hidden"></button>
            </form>
        </div>

        {{-- KOLOM KANAN (SUMMARY - GELAP) --}}
        <div
            class="lg:w-2/5 bg-slate-900 p-8 lg:p-10 text-white flex flex-col justify-between relative overflow-hidden">

            {{-- Dekorasi Abstrak --}}
            <div
                class="absolute bottom-0 left-0 w-64 h-64 bg-rose-900 rounded-full blur-3xl -ml-16 -mb-16 opacity-30 pointer-events-none">
            </div>

            <div class="relative z-10">
                <h3 class="text-lg font-bold mb-8 flex items-center gap-2 text-white">
                    <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                        </path>
                    </svg>
                    Estimasi Terima
                </h3>

                <div class="space-y-5 text-sm">
                    <div class="flex justify-between text-gray-400">
                        <span>Total Penjualan</span>
                        <span id="summaryGross" class="font-mono text-white font-medium">0</span>
                    </div>
                    <div class="flex justify-between text-gray-400">
                        <span>Fee Broker</span>
                        <span id="summaryFee" class="font-mono text-rose-400 font-medium">- 0</span>
                    </div>

                    <div class="h-px bg-gray-700 my-6"></div>

                    <div class="flex justify-between items-end">
                        <span class="text-gray-300 font-bold uppercase text-xs tracking-wider">Bersih Diterima</span>
                        <span id="totalDisplay" class="text-3xl font-bold text-emerald-400 tracking-tight">0</span>
                    </div>
                </div>

                {{-- Alert Saldo Kurang (Logic JS) --}}
                <div id="insufficientMsg"
                    class="hidden mt-6 bg-red-900/30 border border-red-500/30 p-4 rounded-xl flex items-start gap-3">
                    <svg class="w-5 h-5 text-red-400 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                        </path>
                    </svg>
                    <div>
                        <p class="text-xs font-bold text-red-400 uppercase tracking-wide">Melebihi Saldo Aset</p>
                        <p class="text-[11px] text-red-200 mt-1">Anda tidak memiliki unit aset sebanyak ini.</p>
                    </div>
                </div>
            </div>

            {{-- Tombol Jual --}}
            <div class="mt-8 relative z-10">
                <button type="button" onclick="document.getElementById('realSubmitBtn').click()" id="submitBtn"
                    class="w-full group bg-rose-600 hover:bg-rose-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-rose-900/20 transition-all transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-3">
                    <span>Konfirmasi Penjualan</span>
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M14 5l7 7m0 0l-7 7m7-7H3">
                        </path>
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
const assetSelect = document.getElementById('assetSelect');
const sellPriceInput = document.getElementById('sellPriceInput');
const amountInput = document.getElementById('amountInput');
const feeInput = document.getElementById('feeInput');

// UI Elements
const summaryGross = document.getElementById('summaryGross');
const summaryFee = document.getElementById('summaryFee');
const totalDisplay = document.getElementById('totalDisplay');
const insufficientMsg = document.getElementById('insufficientMsg');
const submitBtn = document.getElementById('submitBtn');

const currencyLabel = document.getElementById('currencyLabel');
const feeCurrencyLabel = document.getElementById('feeCurrencyLabel');
const unitLabel = document.getElementById('unitLabel');
const quickActionContainer = document.getElementById('quickActionContainer');
const sellAllBtn = document.getElementById('sellAllBtn');
const maxQtyDisplay = document.getElementById('maxQtyDisplay');

let maxQuantity = 0;

// ---------------------------------------------------------
// 1. AUTO-SELECT ASSET DARI URL PARAMETER (Sama seperti Buy)
// ---------------------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    // Terima parameter 'asset' (dari Valas) atau 'symbol' (dari Portfolio)
    const symbolParam = urlParams.get('asset') || urlParams.get('symbol');

    if (symbolParam) {
        for (let i = 0; i < assetSelect.options.length; i++) {
            if (assetSelect.options[i].value === symbolParam) {
                assetSelect.selectedIndex = i;
                assetSelect.dispatchEvent(new Event('change'));
                break;
            }
        }
    }
});

// 2. Logic Saat Aset Dipilih
assetSelect.addEventListener('change', function() {
    const option = this.options[this.selectedIndex];

    // Ambil data dari option
    const price = parseFloat(option.getAttribute('data-price')) || 0;
    const type = option.getAttribute('data-type');
    maxQuantity = parseFloat(option.getAttribute('data-quantity')) || 0;

    // Tampilkan tombol "Jual Semua" dan Saldo
    if (this.value) {
        quickActionContainer.classList.remove('hidden');
        maxQtyDisplay.innerText = maxQuantity;
    } else {
        quickActionContainer.classList.add('hidden');
    }

    // Ubah Label (Unit vs Nominal) untuk Valas
    if (type === 'Currency') {
        unitLabel.innerText = "Nominal"; // Valas dijual nominalnya
        currencyLabel.innerText = "Rp"; // Harga jual dalam rupiah
    } else if (type === 'US Stock' || type === 'Crypto') {
        unitLabel.innerText = "Unit";
        currencyLabel.innerText = (type === 'US Stock') ? '$' : 'Rp'; // Sesuaikan mata uang dasar
    } else {
        unitLabel.innerText = "Lot"; // Saham Indo
        currencyLabel.innerText = "Rp";
    }

    // Auto Fill Harga
    if (price > 0) {
        sellPriceInput.value = price;
    }

    calculate();
});

// 3. Tombol Shortcut "Jual Semua"
sellAllBtn.addEventListener('click', function() {
    amountInput.value = maxQuantity;
    calculate();
});

// 4. Kalkulasi Total & Validasi
function calculate() {
    const price = parseFloat(sellPriceInput.value) || 0;
    const amount = parseFloat(amountInput.value) || 0;
    const fee = parseFloat(feeInput.value) || 0;

    const gross = price * amount;
    const net = gross - fee;

    // Format Rupiah
    const formatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0,
        maximumFractionDigits: 2
    });

    summaryGross.innerText = formatter.format(gross);
    summaryFee.innerText = "- " + formatter.format(fee);
    totalDisplay.innerText = formatter.format(net);

    // VALIDASI: Cek apakah jual melebihi kepemilikan
    if (amount > maxQuantity) {
        insufficientMsg.classList.remove('hidden');
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
    } else {
        insufficientMsg.classList.add('hidden');
        // Enable tombol jika amount valid (> 0)
        if (amount > 0 && price > 0) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
    }
}

// Listeners
sellPriceInput.addEventListener('input', calculate);
amountInput.addEventListener('input', calculate);
feeInput.addEventListener('input', calculate);
</script>
@endsection