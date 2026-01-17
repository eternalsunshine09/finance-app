<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beli Aset</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 font-sans">

    <nav class="bg-blue-800 p-4 shadow-lg mb-8">
        <div class="container mx-auto font-bold text-white">
            <a href="{{ route('dashboard') }}">⬅ Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full border-t-4 border-indigo-600">
            <h1 class="text-2xl font-bold text-gray-800 mb-6 text-center">🛒 Beli Aset Investasi</h1>

            @if ($errors->any())
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                {{ $errors->first() }}
            </div>
            @endif

            <form action="{{ route('buy.process') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Aset</label>
                    <select name="asset_symbol" id="assetSelect"
                        class="w-full px-3 py-2 border rounded shadow-sm bg-white" required>
                        <option value="" data-price="0">-- Pilih Saham/Kripto --</option>
                        @foreach($assets as $asset)
                        <option value="{{ $asset->symbol }}">
                            {{ $asset->symbol }} - {{ $asset->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Harga Beli Per Unit (IDR)</label>

                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">Rp</span>
                        <input type="number" step="0.01" name="buy_price" id="buyPriceInput"
                            class="w-full pl-10 pr-3 py-2 border border-indigo-300 rounded shadow-sm bg-indigo-50 font-bold text-indigo-800 focus:bg-white focus:ring-2 focus:ring-indigo-500"
                            placeholder="0" required>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        *Otomatis ambil harga pasar terkini. Silakan ubah jika harga di OKX berbeda atau transaksi
                        lampau.
                    </p>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Jumlah Unit (Lot/Koin)</label>
                    <input type="number" step="0.0001" name="amount" id="amountInput"
                        class="w-full px-3 py-2 border rounded shadow-sm" placeholder="0.0" required>
                </div>

                <div class="mb-6 text-right">
                    <span class="text-sm text-gray-500">Estimasi Total Bayar:</span>
                    <h3 id="totalDisplay" class="text-2xl font-bold text-gray-800">Rp 0</h3>
                </div>

                <div class="mb-6 bg-gray-50 p-3 rounded border border-gray-200">
                    <label class="block text-gray-700 text-sm font-bold mb-2">
                        📅 Waktu Pembelian (Opsional)
                    </label>
                    <input type="datetime-local" name="custom_date"
                        class="w-full px-3 py-2 border rounded shadow-sm text-sm">
                    <p class="text-xs text-gray-500 mt-1">
                        *Isi jika ini adalah transaksi lampau (misal dari OKX). Kosongkan untuk waktu sekarang.
                    </p>
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 transition">
                    Beli Sekarang
                </button>
            </form>
        </div>
    </div>

    <script>
    const assetSelect = document.getElementById('assetSelect');
    const amountInput = document.getElementById('amountInput');

    // 👇 Ganti variabel ini (tadinya priceDisplay)
    const buyPriceInput = document.getElementById('buyPriceInput');

    const totalDisplay = document.getElementById('totalDisplay');

    // Saat Ganti Aset
    assetSelect.addEventListener('change', function() {
        const symbol = this.value;

        if (symbol) {
            fetch(`/api/price/${symbol}`)
                .then(response => response.json())
                .then(data => {
                    // 👇 Masukkan harga API ke dalam Input Form
                    buyPriceInput.value = parseFloat(data.price);
                    calculateTotal();
                });
        } else {
            buyPriceInput.value = "";
            calculateTotal();
        }
    });

    // 👇 Saat User Edit Harga Manual
    buyPriceInput.addEventListener('input', function() {
        calculateTotal();
    });

    // Saat User Ketik Jumlah
    amountInput.addEventListener('input', function() {
        calculateTotal();
    });

    function calculateTotal() {
        const amount = parseFloat(amountInput.value) || 0;

        // 👇 Ambil harga dari INPUT MANUAL user, bukan dari variabel global
        const currentPrice = parseFloat(buyPriceInput.value) || 0;

        const total = amount * currentPrice;
        totalDisplay.innerText = new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR'
        }).format(total);
    }
    </script>

</body>

</html>