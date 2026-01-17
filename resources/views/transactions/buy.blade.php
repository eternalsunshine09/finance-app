@extends('layouts.app')

@section('title', 'Beli Aset')
@section('header', '🛒 Beli Aset Investasi')

@section('content')

<div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-sm border border-gray-100">

    @if ($errors->any())
    <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-6 text-sm">
        ⚠️ {{ $errors->first() }}
    </div>
    @endif

    <form action="{{ route('buy.process') }}" method="POST">
        @csrf
        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Aset</label>
            <select name="asset_symbol" id="assetSelect"
                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500 bg-white" required>
                <option value="" data-price="0">-- Pilih Saham/Kripto --</option>
                @foreach($assets as $asset)
                <option value="{{ $asset->symbol }}"
                    {{ (isset($selectedAsset) && $selectedAsset == $asset->symbol) ? 'selected' : '' }}>
                    {{ $asset->symbol }} - {{ $asset->name }}
                </option>
                @endforeach
            </select>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Harga Beli Per Unit (IDR)</label>
            <div class="relative">
                <span class="absolute left-4 top-3 text-gray-500 font-bold">Rp</span>
                <input type="number" step="0.01" name="buy_price" id="buyPriceInput"
                    class="w-full pl-12 pr-4 py-3 border border-indigo-200 rounded-lg bg-indigo-50 font-bold text-indigo-900 focus:bg-white focus:ring-2 focus:ring-indigo-500 transition"
                    placeholder="0" required>
            </div>
            <p class="text-xs text-gray-500 mt-2">
                *Harga otomatis dari pasar. Bisa diedit manual jika beli di exchange lain.
            </p>
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 text-sm font-bold mb-2">Jumlah Unit (Lot/Koin)</label>
            <input type="number" step="0.0001" name="amount" id="amountInput"
                class="w-full px-4 py-3 border rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Contoh: 0.5"
                required>
        </div>

        <div class="mb-8 text-right bg-gray-50 p-4 rounded-lg">
            <span class="text-sm text-gray-500 block mb-1">Estimasi Total Bayar</span>
            <h3 id="totalDisplay" class="text-3xl font-black text-gray-800">Rp 0</h3>
        </div>

        <div class="mb-8 border-t pt-6">
            <div class="flex items-center gap-2 mb-2">
                <span class="text-lg">📅</span>
                <label class="text-gray-700 text-sm font-bold">Backdate (Opsional)</label>
            </div>
            <input type="datetime-local" name="custom_date"
                class="w-full px-4 py-2 border rounded-lg text-sm text-gray-600">
            <p class="text-xs text-gray-400 mt-1">
                *Isi jika mencatat transaksi masa lampau.
            </p>
        </div>

        <button type="submit"
            class="w-full bg-indigo-600 text-white font-bold py-4 rounded-xl hover:bg-indigo-700 transition shadow-lg shadow-indigo-200">
            🚀 Beli Aset Sekarang
        </button>
    </form>
</div>

@endsection

@section('scripts')
<script>
const assetSelect = document.getElementById('assetSelect');
const amountInput = document.getElementById('amountInput');
const buyPriceInput = document.getElementById('buyPriceInput');
const totalDisplay = document.getElementById('totalDisplay');

// 1. Ambil Harga API saat ganti aset
assetSelect.addEventListener('change', function() {
    const symbol = this.value;
    if (symbol) {
        fetch(`/api/price/${symbol}`)
            .then(response => response.json())
            .then(data => {
                buyPriceInput.value = parseFloat(data.price);
                calculateTotal();
            });
    } else {
        buyPriceInput.value = "";
        calculateTotal();
    }
});

// 2. Hitung real-time
buyPriceInput.addEventListener('input', calculateTotal);
amountInput.addEventListener('input', calculateTotal);

function calculateTotal() {
    const amount = parseFloat(amountInput.value) || 0;
    const currentPrice = parseFloat(buyPriceInput.value) || 0;
    const total = amount * currentPrice;

    totalDisplay.innerText = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    }).format(total);
}

// Auto trigger kalau ada aset terpilih dari URL
if (assetSelect.value) {
    assetSelect.dispatchEvent(new Event('change'));
}
</script>
@endsection