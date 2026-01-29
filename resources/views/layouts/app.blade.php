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
        color: #111827;
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

    [x-cloak] {
        display: none !important;
    }
    </style>
</head>

<body class="min-h-screen flex flex-col">

    {{-- NAVIGATION BAR - x-data ditaruh di sini agar membungkus semua --}}
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50" x-data="{ mobileOpen: false }">
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
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50' }}">Dashboard</a>
                    <a href="{{ route('wallet.index') }}"
                        class="px-4 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('wallet.*') ? 'bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50' }}">Dompet</a>
                    <a href="{{ route('portfolio.index') }}"
                        class="px-4 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('portfolio.index') ? 'bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50' }}">Portfolio</a>

                    {{-- Dropdown Market --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="px-4 py-2 text-sm font-medium rounded-md transition-all text-gray-600 hover:text-black hover:bg-gray-50 flex items-center space-x-1">
                            <span>Market</span>
                            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak x-transition
                            class="absolute left-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <div class="py-1">
                                <a href="{{ route('market.index') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Indonesia (IDX)</a>
                                <a href="{{ route('market.us') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">US Market</a>
                                <a href="{{ route('market.crypto') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Crypto</a>
                                <a href="{{ route('market.commodities') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Commodities</a>
                                <a href="{{ route('market.reksadana') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Reksadana/ETF</a>
                                <a href="{{ route('market.valas') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Kurs Valas</a>
                            </div>
                        </div>
                    </div>

                    {{-- Dropdown Transaksi Horizontal --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="px-4 py-2 text-sm font-medium rounded-md transition-all text-gray-600 hover:text-black hover:bg-gray-50 flex items-center space-x-1">
                            <span>Transaksi</span>
                            <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak x-transition
                            class="absolute right-0 mt-2 w-[550px] bg-white border border-gray-200 rounded-xl shadow-2xl z-50 overflow-hidden hidden md:block">
                            <div class="grid grid-cols-2">
                                <div class="p-5 bg-white space-y-6">
                                    <div>
                                        <div
                                            class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">
                                            Kas & Saldo</div>
                                        <a href="{{ route('topup') }}"
                                            class="flex items-center p-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 group">
                                            <div
                                                class="w-8 h-8 rounded-full bg-green-50 text-green-600 flex items-center justify-center mr-3 group-hover:bg-green-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path d="M12 4v16m8-8H4" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </div>
                                            <div><span class="font-bold block">Deposit</span><span
                                                    class="text-xs text-gray-400">Isi saldo</span></div>
                                        </a>
                                        <a href="{{ route('withdraw') }}"
                                            class="flex items-center p-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 group">
                                            <div
                                                class="w-8 h-8 rounded-full bg-red-50 text-red-600 flex items-center justify-center mr-3 group-hover:bg-red-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path d="M20 12H4" stroke-width="2" stroke-linecap="round" />
                                                </svg>
                                            </div>
                                            <div><span class="font-bold block">Withdraw</span><span
                                                    class="text-xs text-gray-400">Tarik dana</span></div>
                                        </a>
                                    </div>
                                    <div>
                                        <div
                                            class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">
                                            Trading</div>
                                        <a href="{{ route('buy') }}"
                                            class="flex items-center p-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 group">
                                            <div
                                                class="w-8 h-8 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center mr-3 group-hover:bg-blue-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"
                                                        stroke-width="2" />
                                                </svg>
                                            </div>
                                            <div><span class="font-bold block">Beli Aset</span><span
                                                    class="text-xs text-gray-400">Order baru</span></div>
                                        </a>
                                        <a href="{{ route('sell') }}"
                                            class="flex items-center p-2 rounded-lg text-sm text-gray-700 hover:bg-gray-50 group">
                                            <div
                                                class="w-8 h-8 rounded-full bg-rose-50 text-rose-600 flex items-center justify-center mr-3 group-hover:bg-rose-100">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path
                                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                        stroke-width="2" />
                                                </svg>
                                            </div>
                                            <div><span class="font-bold block">Jual Aset</span><span
                                                    class="text-xs text-gray-400">Likuidasi</span></div>
                                        </a>
                                    </div>
                                </div>
                                <div class="p-5 bg-gray-50 border-l border-gray-100 space-y-6">
                                    <div>
                                        <div
                                            class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3">
                                            Aksi Korporasi</div>
                                        <a href="{{ route('transactions.dividend.cash') }}"
                                            class="block p-2 text-sm text-gray-600 hover:bg-white hover:text-black transition font-bold">Dividen
                                            Tunai</a>
                                        <a href="{{ route('transactions.dividend.unit') }}"
                                            class="block p-2 text-sm text-gray-600 hover:bg-white hover:text-black transition font-bold">Dividen
                                            Saham</a>
                                        <a href="{{ route('transactions.stocksplit') }}"
                                            class="block p-2 text-sm text-gray-600 hover:bg-white hover:text-black transition font-bold">Stock
                                            Split</a>
                                        <a href="{{ route('transactions.rightissue') }}"
                                            class="block p-2 text-sm text-gray-600 hover:bg-white hover:text-black transition font-bold">Right
                                            Issue</a>
                                    </div>
                                    <a href="{{ route('history') }}"
                                        class="flex items-center p-2 rounded-lg text-sm text-gray-600 hover:bg-white group transition">
                                        <div
                                            class="w-6 h-6 rounded bg-white border border-gray-200 flex items-center justify-center mr-3">
                                            <svg class="w-3 h-3 text-gray-500" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                                                    stroke-width="2" />
                                            </svg></div>
                                        Riwayat Transaksi
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('report.index') }}"
                        class="px-4 py-2 text-sm font-medium rounded-md transition-all {{ request()->routeIs('report.index') ? 'bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50' }}">Laporan</a>
                </div>

                {{-- USER PROFILE & HAMBURGER --}}
                <div class="flex items-center space-x-4">
                    <div class="hidden md:block text-xs font-medium text-gray-400" id="currentDate"></div>

                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                            <div
                                class="w-8 h-8 rounded-md bg-gray-200 border border-gray-300 flex items-center justify-center text-gray-700 font-bold hover:bg-gray-300">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                        </button>
                        <div x-show="open" @click.away="open = false" x-cloak x-transition
                            class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-lg z-50">
                            <div class="p-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="py-1">
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Profil</a>
                                <form action="{{ route('logout') }}" method="POST">@csrf<button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-50">Keluar</button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Hamburger Menu Button --}}
                    <div class="md:hidden">
                        <button @click="mobileOpen = !mobileOpen"
                            class="text-gray-600 hover:text-black p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-inset focus:ring-black">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                :class="{'hidden': mobileOpen, 'block': !mobileOpen }">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                :class="{'block': mobileOpen, 'hidden': !mobileOpen }" x-cloak>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- MOBILE MENU (RESPONSIVE) --}}
        <div class="md:hidden border-t border-gray-200 bg-white" x-show="mobileOpen" x-cloak x-transition>
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('dashboard') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Dashboard</a>
                <a href="{{ route('wallet.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Dompet</a>
                <a href="{{ route('portfolio.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Portfolio</a>

                <div x-data="{ marketOpen: false }">
                    <button @click="marketOpen = !marketOpen"
                        class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 flex justify-between">
                        <span>Market</span>
                        <svg class="w-4 h-4 mt-1 transition-transform" :class="marketOpen ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" />
                        </svg>
                    </button>
                    <div x-show="marketOpen" class="pl-6 space-y-1">
                        <a href="{{ route('market.index') }}" class="block px-3 py-2 text-sm text-gray-600">Indonesia
                            (IDX)</a>
                        <a href="{{ route('market.us') }}" class="block px-3 py-2 text-sm text-gray-600">US Market</a>
                        <a href="{{ route('market.crypto') }}" class="block px-3 py-2 text-sm text-gray-600">Crypto</a>
                        <a href="{{ route('market.commodities') }}"
                            class="block px-3 py-2 text-sm text-gray-600">Commodities</a>
                    </div>
                </div>

                <div x-data="{ transOpen: false }">
                    <button @click="transOpen = !transOpen"
                        class="w-full text-left px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 flex justify-between">
                        <span>Transaksi</span>
                        <svg class="w-4 h-4 mt-1 transition-transform" :class="transOpen ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M19 9l-7 7-7-7" stroke-width="2" />
                        </svg>
                    </button>
                    <div x-show="transOpen" class="pl-6 space-y-1">
                        <a href="{{ route('topup') }}" class="block px-3 py-2 text-sm text-gray-600">Deposit</a>
                        <a href="{{ route('withdraw') }}" class="block px-3 py-2 text-sm text-gray-600">Withdraw</a>
                        <a href="{{ route('buy') }}" class="block px-3 py-2 text-sm text-gray-600">Beli Aset</a>
                        <a href="{{ route('sell') }}" class="block px-3 py-2 text-sm text-gray-600">Jual Aset</a>
                        <a href="{{ route('history') }}"
                            class="block px-3 py-2 text-sm text-gray-600 font-bold">Riwayat</a>
                    </div>
                </div>

                <a href="{{ route('report.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Laporan</a>
            </div>
        </div>
    </nav>

    {{-- MAIN CONTENT --}}
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @hasSection('header')
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">@yield('header')</h1>
            @hasSection('header_description')
            <p class="text-sm text-gray-500 mt-1">@yield('header_description')</p>
            @endif
            <div class="w-12 h-1 bg-black mt-4 rounded-full"></div>
        </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center text-xs text-gray-500">
                <p>&copy; {{ date('Y') }} MyInvest. All rights reserved.</p>
                <p class="mt-2 md:mt-0 font-medium text-gray-400">Personal Finance Dashboard</p>
            </div>
        </div>
    </footer>

    <script>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: '#1f2937',
        color: '#fff',
        customClass: {
            popup: 'rounded-md shadow-xl border border-gray-700'
        }
    });
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

    @yield('scripts')
</body>

</html>