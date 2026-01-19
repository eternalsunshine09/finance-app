@extends('layouts.app')

@section('title', 'Beli Aset')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 py-12 px-4">
    <div class="max-w-4xl w-full">

        <div class="text-center mb-10">
            <h1 class="text-4xl font-black text-slate-800 tracking-tighter mb-2">🛒 Investasi Baru</h1>
            <p class="text-slate-500 font-medium">Diversifikasi portofolio Anda dengan aset terbaik.</p>
        </div>

        @if ($errors->any())
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 mb-6 rounded-r shadow-sm">
            <p class="font-bold">Gagal:</p>
            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div
            class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden flex flex-col lg:flex-row">

            {{-- KOLOM KIRI (FORM) --}}
            <div class="lg:w-3/5 p-8 lg:p-12 relative">
                <form action="{{ route('buy.process') }}" method="POST" class="space-y-6 relative z-10">
                    @csrf

                    {{-- 1. SUMBER DANA --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 ml-1 tracking-wider">1.
                            Sumber Dana</label>
                        <div class="relative group">
                            <select name="wallet_id" id="walletSelect"
                                class="w-full pl-12 pr-10 py-3 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-700 appearance-none cursor-pointer focus:ring-4 focus:ring-indigo-100 focus:border-indigo-500 transition-all outline-none"
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
                            <div class="absolute left-4 top-3.5 text-indigo-400"><i class="fas fa-wallet text-lg"></i>
                            </div>
                            <div class="absolute right-4 top-4 text-slate-400 pointer-events-none"><i
                                    class="fas fa-chevron-down text-xs"></i></div>
                        </div>
                        <div class="mt-2 flex justify-end">
                            <span class="inline-flex items-center gap-2 text-indigo-600 text-xs font-bold">
                                <img id="currencyFlag" src="" class="w-4 h-3 rounded shadow-sm hidden object-cover">
                                Saldo: <span id="walletBalanceDisplay">Rp 0</span>
                            </span>
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    {{-- 2. DETAIL ASET --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 ml-1 tracking-wider">2.
                            Detail Aset</label>
                        <div class="space-y-4">
                            <div class="relative group">
                                <select name="asset_symbol" id="assetSelect"
                                    class="w-full pl-12 pr-10 py-3 bg-white border border-slate-200 rounded-2xl font-bold text-slate-700 appearance-none cursor-pointer focus:ring-4 focus:ring-emerald-100 focus:border-emerald-500 transition-all outline-none"
                                    required>
                                    <option value="" data-price="0">-- Pilih Produk --</option>
                                    @foreach($assets as $asset)
                                    <option value="{{ $asset->symbol }}" data-type="{{ $asset->type }}"
                                        data-price="{{ $asset->current_price }}">
                                        {{ $asset->symbol }} - {{ $asset->name }}
                                    </option>
                                    @endforeach
                                </select>
                                <div class="absolute left-4 top-3.5 text-emerald-500"><i
                                        class="fas fa-chart-pie text-lg"></i></div>
                                <div class="absolute right-4 top-4 text-slate-400 pointer-events-none"><i
                                        class="fas fa-chevron-down text-xs"></i></div>
                            </div>

                            <div class="relative">
                                <span id="currencyLabel"
                                    class="absolute left-4 top-3.5 text-slate-400 font-bold text-sm">Rp</span>
                                <input type="number" step="any" name="buy_price" id="buyPriceInput"
                                    class="w-full pl-12 pr-4 py-3 bg-slate-50 border-0 rounded-2xl font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-200 transition"
                                    placeholder="Harga Pasar" required>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- 3. KUANTITAS --}}
                        <div>
                            <label
                                class="block text-xs font-extrabold text-slate-400 uppercase mb-2 ml-1 tracking-wider">3.
                                Kuantitas</label>
                            <input type="number" step="0.00000001" name="amount" id="amountInput"
                                class="w-full px-4 py-3 bg-emerald-50/50 border-2 border-emerald-100 rounded-2xl font-bold text-emerald-700 focus:border-emerald-500 focus:ring-0 transition placeholder-emerald-200/50"
                                placeholder="0.00" required>
                        </div>

                        {{-- 4. FEE BROKER (INPUT MANUAL) --}}
                        <div>
                            <label
                                class="block text-xs font-extrabold text-slate-400 uppercase mb-2 ml-1 tracking-wider">4.
                                Fee Broker</label>
                            <div class="relative">
                                <span id="feeCurrencyLabel"
                                    class="absolute left-3 top-3.5 text-slate-400 font-bold text-xs">Rp</span>
                                <input type="number" step="any" name="fee" id="feeInput"
                                    class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-200 transition"
                                    placeholder="0" value="0">
                            </div>
                        </div>
                    </div>

                    <button type="submit" id="realSubmitBtn" class="hidden"></button>
                </form>
            </div>

            {{-- KOLOM KANAN (SUMMARY) --}}
            <div class="lg:w-2/5 bg-slate-900 p-8 lg:p-12 text-white relative flex flex-col justify-between">
                <div
                    class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/20 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none">
                </div>
                <div
                    class="absolute bottom-0 left-0 w-40 h-40 bg-emerald-500/20 rounded-full blur-3xl -ml-10 -mb-10 pointer-events-none">
                </div>

                <div class="relative z-10">
                    <h3 class="text-lg font-bold mb-6 flex items-center gap-2"><i
                            class="fas fa-receipt text-indigo-400"></i> Ringkasan Order</h3>
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between text-slate-400"><span>Harga Aset</span> <span id="summaryPrice"
                                class="font-mono text-white">0</span></div>
                        <div class="flex justify-between text-slate-400"><span>Jumlah</span> <span id="summaryAmount"
                                class="font-mono text-white">0</span></div>

                        {{-- FEE MANUAL --}}
                        <div class="flex justify-between text-slate-400">
                            <span>Fee Broker</span>
                            <span id="summaryFee" class="font-mono text-indigo-300">+ 0</span>
                        </div>

                        <div class="h-px bg-slate-700 my-4"></div>
                        <div class="flex justify-between items-end">
                            <span class="text-slate-300 font-bold">Total Tagihan</span>
                            <span id="totalDisplay" class="text-3xl font-black text-emerald-400">0</span>
                        </div>
                    </div>

                    <div id="insufficientBalanceMsg"
                        class="hidden mt-4 bg-rose-500/20 border border-rose-500/50 p-3 rounded-xl flex items-center gap-3">
                        <div
                            class="w-8 h-8 rounded-full bg-rose-500/20 flex items-center justify-center text-rose-400 shrink-0">
                            <i class="fas fa-ban"></i></div>
                        <div>
                            <p class="text-xs font-bold text-rose-300 uppercase">Saldo Tidak Cukup</p>
                            <p class="text-[10px] text-rose-200/70">Total tagihan melebihi saldo dompet.</p>
                        </div>
                    </div>
                </div>

                <div class="mt-8 relative z-10">
                    <button type="button" onclick="document.getElementById('realSubmitBtn').click()" id="submitBtn"
                        class="w-full group bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-500/30 transition-all transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-3">
                        <span>Bayar Sekarang</span>
                        <div
                            class="w-6 h-6 rounded-full bg-white/20 flex items-center justify-center group-hover:bg-white/30 transition">
                            <i class="fas fa-arrow-right text-xs"></i></div>
                    </button>
                </div>
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
const feeInput = document.getElementById('feeInput'); // Input Fee Baru

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

// 1. Logic Pilih Aset
assetSelect.addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    const type = option.getAttribute('data-type');
    const price = option.getAttribute('data-price');

    // Set Currency Label ($/Rp)
    const symbolCurrency = (type === 'Crypto') ? '$' : 'Rp';
    currencyLabel.innerText = symbolCurrency;
    feeCurrencyLabel.innerText = symbolCurrency; // Update label mata uang Fee juga

    // Auto isi harga
    if (price && price > 0) buyPriceInput.value = price;

    calculate();
});

// 2. Kalkulasi Total
function calculate() {
    const price = parseFloat(buyPriceInput.value) || 0;
    const amount = parseFloat(amountInput.value) || 0;
    const fee = parseFloat(feeInput.value) || 0; // Ambil nilai Fee Manual

    const subtotal = price * amount;
    const total = subtotal + fee; // Jumlahkan manual

    // Ambil Data Wallet
    const walletOption = walletSelect.options[walletSelect.selectedIndex];
    const balance = walletOption && !walletOption.disabled ? parseFloat(walletOption.getAttribute('data-balance')) : 0;
    const currency = walletOption ? walletOption.getAttribute('data-currency') : 'IDR';

    const formatter = new Intl.NumberFormat(currency === 'USD' ? 'en-US' : 'id-ID', {
        style: 'currency',
        currency: currency
    });

    // Render ke UI
    summaryPrice.innerText = formatter.format(price);
    summaryAmount.innerText = amount;
    summaryFee.innerText = "+ " + formatter.format(fee); // Tampilkan Fee
    totalDisplay.innerText = formatter.format(total);

    // Validasi Saldo
    if (total > balance) {
        insufficientBalanceMsg.classList.remove('hidden');
        submitBtn.disabled = true;
        submitBtn.classList.add('bg-slate-700', 'text-slate-500');
        submitBtn.classList.remove('bg-indigo-600');
        totalDisplay.classList.add('text-rose-500');
        totalDisplay.classList.remove('text-emerald-400');
    } else {
        insufficientBalanceMsg.classList.add('hidden');
        submitBtn.disabled = false;
        submitBtn.classList.remove('bg-slate-700', 'text-slate-500');
        submitBtn.classList.add('bg-indigo-600');
        totalDisplay.classList.remove('text-rose-500');
        totalDisplay.classList.add('text-emerald-400');
    }
}

// 3. Update Wallet Info
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
feeInput.addEventListener('input', calculate); // Listener untuk input fee
</script>
@endsection