<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beli Aset - Investment Manager</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <nav class="bg-blue-800 p-4 shadow-lg">
        <div class="container mx-auto text-white font-bold">
            <a href="{{ route('dashboard') }}">⬅ Kembali ke Dashboard</a>
        </div>
    </nav>

    <div class="flex items-center justify-center h-screen">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
            <h1 class="text-2xl font-bold text-indigo-600 mb-6 text-center">🛒 Beli Aset Investasi</h1>

            @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach ($errors->all() as $error)
                    <li class="text-xs list-disc ml-4">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <form action="{{ route('buy.process') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Pilih Aset</label>
                    <select name="asset_symbol"
                        class="w-full px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 bg-white">
                        @foreach($assets as $asset)
                        <option value="{{ $asset->symbol }}">
                            {{ $asset->symbol }} - {{ $asset->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Harga Per Unit (Rp)</label>
                    <input type="number" name="price_per_unit"
                        class="w-full px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Contoh: 2500" required>
                    <p class="text-xs text-gray-500 mt-1">*Masukkan harga pasar saat ini</p>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Jumlah Unit (Lembar/Koin)</label>
                    <input type="number" name="quantity" step="0.01"
                        class="w-full px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        placeholder="Contoh: 100" required>
                </div>

                <button type="submit"
                    class="w-full bg-indigo-600 text-white font-bold py-2 px-4 rounded hover:bg-indigo-700 transition duration-200">
                    Konfirmasi Pembelian
                </button>
            </form>
        </div>
    </div>

</body>

</html>