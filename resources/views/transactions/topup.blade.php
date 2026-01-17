<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Top Up Saldo - Investment Manager</title>
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
            <h1 class="text-2xl font-bold text-green-600 mb-6 text-center">💸 Isi Saldo (Top Up)</h1>

            <form action="{{ route('topup.process') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                <input type="hidden" name="currency" value="IDR">

                <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nominal Top Up (Rp)</label>
                    <input type="number" name="amount" min="10000"
                        class="w-full px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-green-500"
                        placeholder="Minimal 10.000" required>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Bukti Transfer (Struk/SS)</label>
                    <input type="file" name="payment_proof"
                        class="w-full px-3 py-2 border rounded shadow-sm bg-gray-50 text-sm" accept="image/*" required>
                    <p class="text-xs text-gray-500 mt-1">Format: JPG, PNG. Maks 2MB.</p>
                </div>

                <button type="submit"
                    class="w-full bg-green-600 text-white font-bold py-2 px-4 rounded hover:bg-green-700 transition duration-200">
                    Kirim Bukti & Top Up
                </button>
            </form>
        </div>
    </div>

</body>

</html>