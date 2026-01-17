<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Investment Manager</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 flex items-center justify-center h-screen">

    <div class="bg-white p-8 rounded-lg shadow-lg max-w-sm w-full">
        <div class="text-center mb-6">
            <h1 class="text-2xl font-bold text-blue-800">🚀 Masuk Aplikasi</h1>
            <p class="text-gray-500 text-sm">Silakan login untuk kelola asetmu</p>
        </div>

        @error('email')
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">
            <span class="block sm:inline">{{ $message }}</span>
        </div>
        @enderror

        <form action="{{ route('login') }}" method="POST">
            @csrf <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email"
                    class="w-full px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="contoh: budi@finance.com" required>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password"
                    class="w-full px-3 py-2 border rounded shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white font-bold py-2 px-4 rounded hover:bg-blue-700 transition duration-200">
                Masuk Sekarang
            </button>
        </form>

        <p class="text-center text-gray-400 text-xs mt-4">
            Belum punya akun? Hubungi Admin.
        </p>
    </div>

</body>

</html>