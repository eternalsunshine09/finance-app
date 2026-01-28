<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- JUDUL HALAMAN DINAMIS --}}
    <title>@yield('title', 'MyInvest')</title>

    {{-- ASSETS: CSS & JS (Vite) --}}
    @vite('resources/css/app.css')

    {{-- LIBRARY EKSTERNAL --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    {{-- CUSTOM STYLES --}}
    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f9fafb;
        /* Abu sangat muda */
        color: #111827;
        /* Hitam pekat */
    }

    /* Scrollbar Minimalis */
    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    ::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }

    /* Utility AlpineJS */
    [x-cloak] {
        display: none !important;
    }
    </style>
</head>

<body class="min-h-screen flex flex-col">

    {{-- NAVIGATION BAR --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- LOGO & BRAND --}}
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 no-underline group">
                        <div
                            class="w-10 h-10 rounded-lg bg-black flex items-center justify-center group-hover:bg-gray-800 transition-colors">
                            <span class="text-white font-bold text-lg">M</span>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-lg font-bold text-gray-900 leading-tight">MyInvest</h1>
                            <span class="text-[10px] font-semibold text-gray-500 tracking-widest">PERSONAL
                                FINANCE</span>
                        </div>
                    </a>
                </div>

                {{-- DESKTOP MENU --}}
                <div class="hidden md:flex items-center space-x-1">

                    {{-- Menu Dashboard --}}
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200
                        {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50' }}">
                        Dashboard
                    </a>

                    {{-- Menu Dompet --}}
                    <a href="{{ route('wallet.index') }}"
                        class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200
                        {{ request()->routeIs('wallet.*') ? 'bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50' }}">
                        Dompet
                    </a>

                    {{-- Menu Portfolio --}}
                    <a href="{{ route('portfolio.index') }}"
                        class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200
                        {{ request()->routeIs('portfolio.index') ? 'bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50' }}">
                        Portfolio
                    </a>

                    {{-- Dropdown Market --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-600 hover:text-black hover:bg-gray-50 flex items-center space-x-1">
                            <span>Market</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute left-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <div class="py-1">
                                <a href="{{ route('market.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-black">Indonesia
                                    (IDX)</a>
                                <a href="{{ route('market.us') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-black">US
                                    Market</a>
                                <a href="{{ route('market.crypto') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-black">Crypto</a>
                                <a href="{{ route('market.commodities') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-black">Commodities</a>
                                <a href="{{ route('market.reksadana') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-black">Reksadana/ETF</a>
                                {{-- 🔥 TAMBAHKAN DISINI 🔥 --}}
                                <a href="{{ route('market.valas') }}"
                                    class="inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium transition duration-150 ease-in-out
                                     {{ request()->routeIs('market.valas') ? 'border-black text-gray-900' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                                    💱 Kurs Valas
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Dropdown Transaksi --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 text-gray-600 hover:text-black hover:bg-gray-50 flex items-center space-x-1">
                            <span>Transaksi</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute right-0 md:left-0 mt-2 w-64 bg-white border border-gray-200 rounded-md shadow-xl z-50">
                            <div class="py-2">
                                {{-- Kategori Kas --}}
                                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Kas
                                </div>
                                <a href="{{ route('topup') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-black group">
                                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-black" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                    Deposit / Top Up
                                </a>
                                <a href="{{ route('withdraw') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-black group">
                                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-black" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 12H4"></path>
                                    </svg>
                                    Withdraw
                                </a>

                                <div class="border-t border-gray-100 my-1"></div>

                                {{-- Kategori Perdagangan --}}
                                <div class="px-4 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                    Perdagangan</div>
                                <a href="{{ route('buy') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-black group">
                                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-black" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                        </path>
                                    </svg>
                                    Beli Aset
                                </a>
                                <a href="{{ route('sell') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-black group">
                                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-black" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                        </path>
                                    </svg>
                                    Jual Aset
                                </a>

                                <div class="border-t border-gray-100 my-1"></div>

                                {{-- Kategori Riwayat --}}
                                <a href="{{ route('history') }}"
                                    class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-black group">
                                    <svg class="w-4 h-4 mr-3 text-gray-400 group-hover:text-black" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Riwayat Transaksi
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- MENU LAPORAN (BARU DITAMBAHKAN DISINI) --}}
                    <a href="{{ route('report.index') }}"
                        class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200
                        {{ request()->routeIs('report.index') ? 'bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50' }}">
                        Laporan
                    </a>

                </div>

                {{-- USER PROFILE AREA --}}
                <div class="flex items-center space-x-4">
                    {{-- Tanggal Hari Ini --}}
                    <div class="hidden md:block text-xs font-medium text-gray-400" id="currentDate"></div>

                    {{-- Dropdown Profil User --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                            <div
                                class="w-8 h-8 rounded-md bg-gray-200 border border-gray-300 flex items-center justify-center text-gray-700 font-bold hover:bg-gray-300 transition-colors">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <div class="p-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-black">Pengaturan
                                    Profil</a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-black">Keluar</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Hamburger Menu (Mobile) --}}
                    <div class="md:hidden">
                        <button @click="mobileOpen = !mobileOpen" x-data="{ mobileOpen: false }"
                            class="text-gray-600 hover:text-black">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MOBILE MENU (RESPONSIVE) --}}
        <div class="md:hidden border-t border-gray-200 bg-white" x-data="{ mobileOpen: false }" x-show="mobileOpen"
            @click.away="mobileOpen = false" style="display: none;">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('dashboard') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Dashboard</a>
                <a href="{{ route('wallet.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Dompet</a>
                <a href="{{ route('portfolio.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Portfolio</a>
                <a href="{{ route('market.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Market</a>
                <a href="{{ route('history') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Transaksi</a>
                {{-- Menu Laporan Mobile --}}
                <a href="{{ route('report.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Laporan</a>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT WRAPPER --}}
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">

        {{-- HEADER SECTION (Optional) --}}
        @hasSection('header')
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">@yield('header')</h1>
            @hasSection('header_description')
            <p class="text-sm text-gray-500 mt-1">@yield('header_description')</p>
            @endif
            <div class="w-12 h-1 bg-black mt-4 rounded-full"></div>
        </div>
        @endif

        {{-- CONTENT INJECTION --}}
        @yield('content')
    </main>

    {{-- FOOTER --}}
    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center text-xs text-gray-500">
                <p>&copy; {{ date('Y') }} MyInvest. All rights reserved.</p>
                <p class="mt-2 md:mt-0 font-medium text-gray-400">Personal Finance Dashboard</p>
            </div>
        </div>
    </footer>

    {{-- SCRIPTS UTAMA --}}
    <script>
    // Menampilkan Tanggal Hari Ini
    document.addEventListener('DOMContentLoaded', function() {
        const dateElement = document.getElementById('currentDate');
        if (dateElement) {
            const now = new Date();
            dateElement.textContent = now.toLocaleDateString('id-ID', {
                weekday: 'long',
                day: 'numeric',
                month: 'long',
                year: 'numeric'
            });
        }
    });
    </script>

    {{-- NOTIFIKASI SWEETALERT (TOAST) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: '#1f2937', // Dark Toast
        color: '#fff',
        customClass: {
            popup: 'rounded-md shadow-xl border border-gray-700'
        }
    });

    // Menampilkan Flash Message dari Session Laravel
    @if(session('success')) Toast.fire({
        icon: 'success',
        title: "{{ session('success') }}"
    });
    @endif
    @if(session('error')) Toast.fire({
        icon: 'error',
        title: "{{ session('error') }}"
    });
    @endif
    </script>

    {{-- Script Tambahan per Halaman --}}
    @yield('scripts')
</body>

</html>