<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarik Dana - Investment Manager</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <nav class="bg-blue-800 p-4 shadow-lg">
        <div class="container mx-auto text-white font-bold">
            <a href="{{ route('dashboard') }}">⬅ Batal & Kembali</a>
        </div>
    </nav>

    <div class="flex items-center justify-center h-screen">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full border-t-4 border-orange-500">
            <h1 class="text-2xl font-bold text-gray-800 mb-2 text-center">💸 Tarik Dana (Withdraw)</h1>
            <p class="text-center text-gray-500 text-sm mb-6">Uang akan ditransfer ke rekening terdaftar.</p>

            @if ($errors->any())
            <div class="bg-red-100 text-red-700 px-4 py-3 rounded mb-4 text-sm">
                {{ $errors->first() }}
            </div>
            @endif

            <form action="{{ route('withdraw.process') }}" method="POST">
                @csrf
                <input type="hidden" name="user_id" value="{{ Auth::id() }}">
                <input type="hidden" name="currency" value="IDR">

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Nominal Penarikan (Rp)</label>
                    <input type="number" name="amount" min="10000"
                        class="w-full px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-orange-500"
                        placeholder="Minimal 10.000" required>
                    <p class="text-xs text-gray-400 mt-1">Saldo akan langsung dipotong saat request.</p>
                </div>

                <button type="submit"
                    class="w-full bg-orange-600 text-white font-bold py-2 px-4 rounded hover:bg-orange-700 transition duration-200">
                    Ajukan Penarikan
                </button>
            </form>
        </div>
    </div>

</body>

</html>