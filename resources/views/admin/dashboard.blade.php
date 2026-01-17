<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Investment Manager</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-800 text-white font-sans">

    <nav class="bg-gray-900 p-4 shadow-lg border-b border-gray-700">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="font-bold text-xl text-yellow-400">👑 Admin Control Center</h1>
            <div class="flex items-center gap-4">
                <span class="text-gray-300">Halo, Admin</span>
                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm bg-red-600 px-3 py-1 rounded hover:bg-red-700 transition">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-8 px-4">
        <h2 class="text-2xl font-bold mb-6">Ringkasan Sistem</h2>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <div class="bg-gray-700 p-6 rounded-lg shadow-lg border-l-4 border-yellow-500">
                <h3 class="text-gray-400 text-sm font-bold uppercase">Menunggu Approval</h3>
                <p class="text-3xl font-bold text-white mt-2">{{ $pendingTopUp }} <span
                        class="text-sm font-normal text-gray-400">Request</span></p>
                <a href="{{ route('admin.transactions.index') }}"
                    class="text-yellow-400 text-sm mt-4 inline-block hover:underline">
                    Lihat Request &rarr;
                </a>
            </div>

            <div class="bg-gray-700 p-6 rounded-lg shadow-lg border-l-4 border-blue-500">
                <h3 class="text-gray-400 text-sm font-bold uppercase">Total Investor</h3>
                <p class="text-3xl font-bold text-white mt-2">{{ $totalUser }} <span
                        class="text-sm font-normal text-gray-400">Orang</span></p>
            </div>

            <div class="bg-gray-700 p-6 rounded-lg shadow-lg border-l-4 border-green-500">
                <h3 class="text-gray-400 text-sm font-bold uppercase">Aset Terdaftar</h3>
                <p class="text-3xl font-bold text-white mt-2">{{ $totalAset }} <span
                        class="text-sm font-normal text-gray-400">Jenis</span></p>
                <a href="{{ route('admin.assets.index') }}"
                    class="text-green-400 text-sm mt-4 inline-block hover:underline">
                    Kelola Aset &rarr;
                </a>
            </div>
        </div>

        <div class="bg-gray-700 p-6 rounded-lg shadow-lg">
            <h3 class="text-xl font-bold mb-4">Aksi Cepat</h3>
            <div class="flex gap-4">
                <a href="{{ route('admin.assets.index') }}"
                    class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-6 rounded transition">
                    🏢 Manajemen Aset
                </a>
                <a href="{{ route('admin.transactions.index') }}"
                    class="bg-green-600 hover:bg-green-500 text-white font-bold py-2 px-6 rounded transition">
                    ✅ Approval Top Up
                </a>
            </div>
        </div>

    </div>
</body>

</html>