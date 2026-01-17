<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyInvestment')</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
    }
    </style>
</head>

<body class="bg-gray-100 text-gray-800">

    <div class="flex h-screen overflow-hidden">

        <aside class="w-64 bg-gray-900 text-white flex flex-col fixed h-full transition-all duration-300 z-50">
            <div class="h-16 flex items-center justify-center border-b border-gray-800">
                <h1 class="text-2xl font-bold tracking-wider text-yellow-500">🚀 MyInvest</h1>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('dashboard') ? 'bg-gray-800 border-l-4 border-yellow-500' : '' }}">
                            <span class="text-xl mr-3">🏠</span> Dashboard
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('wallet.index') }}"
                            class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('wallet.index') ? 'bg-gray-800 border-l-4 border-yellow-500' : '' }}">
                            <span class="text-xl mr-3">💰</span> Wallet (Dompet)
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center px-6 py-3 hover:bg-gray-800 text-gray-400 cursor-not-allowed"
                            title="Segera Hadir">
                            <span class="text-xl mr-3">📊</span> Portofolio Detail
                        </a>
                    </li>
                    <li>
                        <a href="#"
                            class="flex items-center px-6 py-3 hover:bg-gray-800 text-gray-400 cursor-not-allowed"
                            title="Segera Hadir">
                            <span class="text-xl mr-3">💱</span> Exchange Rate
                        </a>
                    </li>

                    <li class="px-6 pt-4 pb-2 text-xs font-bold text-gray-500 uppercase">Transaksi</li>
                    <li>
                        <a href="{{ route('topup') }}"
                            class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('topup') ? 'bg-gray-800 border-l-4 border-green-500' : '' }}">
                            <span class="text-xl mr-3">📥</span> Top Up
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('withdraw') }}"
                            class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('withdraw') ? 'bg-gray-800 border-l-4 border-red-500' : '' }}">
                            <span class="text-xl mr-3">💸</span> Withdraw
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('buy') }}"
                            class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('buy') ? 'bg-gray-800 border-l-4 border-indigo-500' : '' }}">
                            <span class="text-xl mr-3">🛒</span> Beli Aset
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('history') }}"
                            class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('history') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                            <span class="text-xl mr-3">📜</span> Riwayat
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="p-4 border-t border-gray-800">
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 hover:bg-gray-800 p-2 rounded transition">
                    @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-8 h-8 rounded-full object-cover">
                    @else
                    <div
                        class="w-8 h-8 rounded-full bg-yellow-500 text-black flex items-center justify-center font-bold text-xs">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    @endif
                    <div>
                        <p class="text-sm font-bold text-white">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">Edit Profil</p>
                    </div>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button class="w-full text-left text-xs text-red-400 hover:text-red-300 px-2 py-1">
                        🚪 Keluar Aplikasi
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 ml-64 h-full overflow-y-auto bg-gray-100">
            <header class="bg-white shadow-sm p-6 sticky top-0 z-40">
                <h2 class="text-2xl font-bold text-gray-800">@yield('header', 'Dashboard')</h2>
            </header>

            <div class="p-6 pb-20">
                @if(session('success'))
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
                    <p class="font-bold">Sukses!</p>
                    <p>{{ session('success') }}</p>
                </div>
                @endif

                @yield('content')
            </div>
        </main>

    </div>

    @yield('scripts')
</body>

</html>