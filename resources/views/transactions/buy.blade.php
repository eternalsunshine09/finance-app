@extends('layouts.app')

@section('title', 'Beli Aset')

@section('content')
<div class="min-h-screen bg-slate-50 py-12">
    <div class="max-w-3xl mx-auto px-4">

        {{-- Header --}}
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">🛒 Beli Aset Investasi</h1>
            <p class="text-slate-500 mt-2">Pilih aset potensial dan bayar menggunakan saldo dompetmu.</p>
        </div>

        @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r shadow-sm">
            <p class="font-bold">Gagal Memproses:</p>
            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('buy.process') }}" method="POST" class="space-y-6">
            @csrf

            {{-- SECTION 1: SUMBER DANA --}}
            <div class="bg-white p-6 rounded-3xl shadow-lg border border-slate-100 relative overflow-hidden">
                <div class="absolute top-0 right-0 w-24 h-24 bg-indigo-50 rounded-bl-full -mr-4 -mt-4 z-0"></div>

                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4 relative z-10">1. Sumber Dana
                </h3>

                <div class="relative z-10">
                    <label class="block text-slate-700 font-bold mb-2">Bayar Menggunakan:</label>
                    <div class="relative">
                        <select name="wallet_id" id="walletSelect"
                            class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-slate-700 appearance-none cursor-pointer"
                            required>
                            <option value="" disabled selected>-- Pilih Dompet --</option>
                            @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}" data-balance="{{ $wallet->balance }}"
                                data-currency="{{ $wallet->currency }}"
                                data-flag="{{ $wallet->currency == 'IDR' ? 'id' : 'us' }}">
                                {{ $wallet->bank_name }} - {{ $wallet->account_name }} ({{ $wallet->currency }})
                            </option>
                            @endforeach
                        </select>
                        <div class="absolute left-4 top-4 text-xl">💳</div>
                        <div class="absolute right-4 top-4 text-slate-400"><i class="fas fa-chevron-down"></i></div>
                    </div>

                    {{-- Info Saldo dengan Bendera --}}
                    <div
                        class="mt-3 flex justify-between items-center bg-indigo-50/50 p-3 rounded-xl border border-indigo-100">
                        <span class="text-xs text-indigo-600 font-bold">Saldo Tersedia:</span>
                        <div class="flex items-center gap-2">
                            {{-- Image Flag Placeholder --}}
                            <img id="currencyFlag" src="" class="w-6 h-4 rounded shadow-sm hidden object-cover">
                            <span id="walletBalanceDisplay" class="font-black text-indigo-700 text-lg">Rp 0</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SECTION 2: RINCIAN ASET --}}
            <div class="bg-white p-6 rounded-3xl shadow-lg border border-slate-100">
                <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">2. Rincian Pembelian</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Pilih Aset --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Aset Tujuan</label>
                        <div class="relative">
                            <select name="asset_symbol" id="assetSelect"
                                class="w-full pl-10 pr-4 py-3 border border-slate-200 rounded-xl focus:ring-indigo-500 font-bold text-slate-800"
                                required>
                                <option value="" data-price="0">-- Pilih Aset --</option>
                                @foreach($assets as $asset)
                                <option value="{{ $asset->symbol }}" data-type="{{ $asset->type }}">
                                    {{ $asset->symbol }} - {{ $asset->name }} ({{ $asset->type }})
                                </option>
                                @endforeach
                            </select>
                            <div class="absolute left-3.5 top-3.5 text-slate-400"><i class="fas fa-chart-pie"></i></div>
                        </div>
                    </div>

                    {{-- Harga Per Unit --}}
                    <div>
                        <label class="block text-xs font-bold text-slate-500 uppercase mb-2">Harga Pasar (Unit)</label>
                        <div class="relative">
                            <span id="currencyLabel" class="absolute left-4 top-3 text-slate-400 font-bold">Rp</span>
                            <input type="number" step="any" name="buy_price" id="buyPriceInput"
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-600 focus:bg-white transition"
                                placeholder="0" required>
                        </div>
                    </div>
                </div>

                {{-- Input Jumlah --}}
                <div class="mt-6">
                    <label class="block text-xs font-bold text-emerald-600 uppercase mb-2">Jumlah yang dibeli
                        (Lot/Coin)</label>
                    <div class="relative">
                        <input type="number" step="0.00000001" name="amount" id="amountInput"
                            class="w-full pl-4 pr-4 py-4 border-2 border-emerald-100 rounded-xl font-mono text-2xl font-bold text-slate-800 focus:border-emerald-500 focus:ring-0 transition placeholder-slate-300"
                            placeholder="0.00" required>
                        <span
                            class="absolute right-4 top-5 text-xs font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded">UNIT</span>
                    </div>
                </div>
            </div>

            {{-- SECTION 3: TOTAL & SUBMIT --}}
            <div class="bg-slate-800 p-6 rounded-3xl shadow-xl text-white relative overflow-hidden">
                <div class="flex justify-between items-end mb-6 relative z-10">
                    <div>
                        <p class="text-slate-400 text-sm font-medium">Estimasi Total Bayar</p>
                        <h2 id="totalDisplay" class="text-4xl font-black mt-1">0</h2>
                    </div>

                    {{-- Warning jika saldo kurang --}}
                    <div id="insufficientBalanceMsg"
                        class="hidden bg-rose-500/20 border border-rose-500/50 text-rose-300 px-3 py-1 rounded-lg text-xs font-bold">
                        <i class="fas fa-exclamation-triangle"></i> Saldo Kurang
                    </div>
                </div>

                {{-- Tombol Submit --}}
                <button type="submit" id="submitBtn"
                    class="w-full bg-emerald-500 hover:bg-emerald-400 text-white font-bold py-4 rounded-xl shadow-lg shadow-emerald-500/20 transition transform active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed flex justify-center items-center gap-2 relative z-10">
                    <span>Konfirmasi Pembelian</span>
                    <i class="fas fa-arrow-right"></i>
                </button>

                {{-- Opsi Backdate --}}
                <div class="mt-6 pt-4 border-t border-slate-700 relative z-10">
                    <label
                        class="flex items-center gap-2 text-slate-400 text-xs cursor-pointer hover:text-white transition w-fit"
                        onclick="document.getElementById('dateInputContainer').classList.toggle('hidden')">
                        <i class="fas fa-calendar-alt"></i> Set Tanggal Transaksi (Backdate)
                    </label>
                    <div id="dateInputContainer" class="hidden mt-2">
                        <input type="datetime-local" name="custom_date"
                            class="bg-slate-900 border border-slate-600 text-slate-300 text-sm rounded-lg p-2 w-full">
                    </div>
                </div>

                {{-- Hiasan --}}
                <div class="absolute -right-6 -bottom-10 w-40 h-40 bg-emerald-500/20 rounded-full blur-3xl z-0"></div>
            </div>

        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
const walletSelect = document.getElementById('walletSelect');
const walletBalanceDisplay = document.getElementById('walletBalanceDisplay');
const currencyFlag = document.getElementById('currencyFlag');

const assetSelect = document.getElementById('assetSelect');
const amountInput = document.getElementById('amountInput');
const buyPriceInput = document.getElementById('buyPriceInput');
const totalDisplay = document.getElementById('totalDisplay');
const insufficientBalanceMsg = document.getElementById('insufficientBalanceMsg');
const submitBtn = document.getElementById('submitBtn');
const currencyLabel = document.getElementById('currencyLabel');

// 1. UPDATE SALDO SAAT GANTI DOMPET
function updateWalletInfo() {
    const selectedOption = walletSelect.options[walletSelect.selectedIndex];

    if (selectedOption && !selectedOption.disabled) {
        const balance = parseFloat(selectedOption.getAttribute('data-balance')) || 0;
        const currency = selectedOption.getAttribute('data-currency') || 'IDR';
        const flagCode = selectedOption.getAttribute('data-flag') || 'id';

        // Format Uang
        const formatter = new Intl.NumberFormat(currency === 'USD' ? 'en-US' : 'id-ID', {
            style: 'currency',
            currency: currency
        });

        walletBalanceDisplay.innerText = formatter.format(balance);

        // Update Bendera (Pakai API flagcdn)
        currencyFlag.src = `https://flagcdn.com/w40/${flagCode}.png`;
        currencyFlag.classList.remove('hidden');
    } else {
        walletBalanceDisplay.innerText = "Rp 0";
        currencyFlag.classList.add('hidden');
    }

    calculateTotal(); // Cek ulang kecukupan saldo
}

walletSelect.addEventListener('change', updateWalletInfo);

// 2. LOGIC AMBIL HARGA & DETEKSI TIPE ASET
assetSelect.addEventListener('change', function() {
    const symbol = this.value;
    const selectedOption = this.options[this.selectedIndex];
    const assetType = selectedOption.getAttribute('data-type'); // 'Crypto' atau 'Stock'

    // Ubah Simbol Mata Uang Input
    let currencySymbol = 'Rp';
    if (assetType === 'Crypto') currencySymbol = '$';
    if (currencyLabel) currencyLabel.innerText = currencySymbol;

    // Ambil Harga API
    if (symbol) {
        fetch(`/api/price/${symbol}`)
            .then(res => res.json())
            .then(data => {
                buyPriceInput.value = parseFloat(data.price);
                calculateTotal();
            });
    }
});

// 3. KALKULASI TOTAL & VALIDASI SALDO
function calculateTotal() {
    const amount = parseFloat(amountInput.value) || 0;
    const price = parseFloat(buyPriceInput.value) || 0;
    const total = amount * price;

    // Ambil data dompet yang dipilih
    const selectedWalletOption = walletSelect.options[walletSelect.selectedIndex];
    const balance = selectedWalletOption && !selectedWalletOption.disabled ?
        parseFloat(selectedWalletOption.getAttribute('data-balance')) :
        0;

    // Cek Mata Uang untuk Format
    const selectedAssetOption = assetSelect.options[assetSelect.selectedIndex];
    const assetType = selectedAssetOption ? selectedAssetOption.getAttribute('data-type') : 'Stock';
    const currencyCode = (assetType === 'Crypto') ? 'USD' : 'IDR';
    const locale = (assetType === 'Crypto') ? 'en-US' : 'id-ID';

    // Tampilkan Total
    totalDisplay.innerText = new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currencyCode
    }).format(total);

    // Validasi Saldo
    if (total > balance) {
        // Saldo Kurang
        totalDisplay.classList.add('text-rose-400');
        insufficientBalanceMsg.classList.remove('hidden');
        submitBtn.disabled = true;
        submitBtn.classList.add('bg-slate-600', 'text-slate-400');
        submitBtn.classList.remove('bg-emerald-500', 'text-white');
        submitBtn.innerHTML = "<i class='fas fa-ban'></i> Saldo Tidak Cukup";
    } else {
        // Aman
        totalDisplay.classList.remove('text-rose-400');
        insufficientBalanceMsg.classList.add('hidden');
        submitBtn.disabled = false;
        submitBtn.classList.remove('bg-slate-600', 'text-slate-400');
        submitBtn.classList.add('bg-emerald-500', 'text-white');
        submitBtn.innerHTML = `<span>Konfirmasi Pembelian</span> <i class="fas fa-arrow-right"></i>`;
    }
}

// Event Listeners
buyPriceInput.addEventListener('input', calculateTotal);
amountInput.addEventListener('input', calculateTotal);

// Init (Jalankan saat load biar saldo awal muncul)
updateWalletInfo();
</script>
@endsection