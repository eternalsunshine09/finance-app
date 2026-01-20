<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyInvest')</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- ApexCharts for Market Pages -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #fafafa;
        color: #1a1a1a;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    .chart-container {
        position: relative;
        height: 300px;
    }

    .apexcharts-tooltip {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }

    .apexcharts-gridline {
        stroke: #f3f4f6;
    }

    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    [x-cloak] {
        display: none !important;
    }
    </style>
</head>

<body class="min-h-screen">
    <!-- NAVBAR ELEGANT -->
    <!-- NAVBAR ELEGANT -->
    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 no-underline">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 flex items-center justify-center">
                            <span class="text-white font-bold text-lg">M</span>
                        </div>
                        <div>
                            <h1 class="text-lg font-semibold text-gray-900">MyInvest</h1>
                            <span class="text-xs text-gray-500 tracking-widest">FINANCE</span>
                        </div>
                    </a>
                </div>

                <!-- Navigation -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('wallet.index') }}"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('wallet.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Dompet
                    </a>
                    <a href="{{ route('portfolio.index') }}"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                          {{ request()->routeIs('portfolio.index') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Portfolio
                    </a>

                    <!-- Market Dropdown -->
                    <div class="relative" x-data="{ marketOpen: false }">
                        <button @click="marketOpen = !marketOpen" class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                                   text-gray-600 hover:text-gray-900 hover:bg-gray-50 flex items-center space-x-1">
                            <span>Market</span>
                            <svg class="w-4 h-4 transition-transform duration-200"
                                :class="marketOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="marketOpen" @click.away="marketOpen = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50 overflow-hidden">
                            <div class="py-2">
                                <a href="{{ route('market.index') }}"
                                    class="block px-4 py-3 text-sm hover:bg-gray-50 transition-colors duration-200
                                      {{ request()->routeIs('market.index') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 flex items-center justify-center">
                                            <span class="text-sm">🇮🇩</span>
                                        </div>
                                        <span>Indonesia</span>
                                    </div>
                                </a>
                                <a href="{{ route('market.us') }}"
                                    class="block px-4 py-3 text-sm hover:bg-gray-50 transition-colors duration-200
                                      {{ request()->routeIs('market.us') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 flex items-center justify-center">
                                            <span class="text-sm">🇺🇸</span>
                                        </div>
                                        <span>US Market</span>
                                    </div>
                                </a>
                                <a href="{{ route('market.crypto') }}"
                                    class="block px-4 py-3 text-sm hover:bg-gray-50 transition-colors duration-200
                                      {{ request()->routeIs('market.crypto') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 flex items-center justify-center">
                                            <span class="text-sm">₿</span>
                                        </div>
                                        <span>Crypto</span>
                                    </div>
                                </a>
                                <a href="{{ route('market.commodities') }}"
                                    class="block px-4 py-3 text-sm hover:bg-gray-50 transition-colors duration-200
                                      {{ request()->routeIs('market.commodities') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 flex items-center justify-center">
                                            <span class="text-sm">⛏️</span>
                                        </div>
                                        <span>Commodities</span>
                                    </div>
                                </a>
                                <a href="{{ route('market.reksadana') }}"
                                    class="block px-4 py-3 text-sm hover:bg-gray-50 transition-colors duration-200
                                      {{ request()->routeIs('market.reksadana') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                                    <div class="flex items-center space-x-2">
                                        <div class="w-6 h-6 flex items-center justify-center">
                                            <span class="text-sm">📊</span>
                                        </div>
                                        <span>Reksadana/ETF</span>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Transaksi Dropdown -->
                    <div class="relative" x-data="{ transaksiOpen: false }">
                        <button @click="transaksiOpen = !transaksiOpen" class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                                   text-gray-600 hover:text-gray-900 hover:bg-gray-50 flex items-center space-x-1">
                            <span>Transaksi</span>
                            <svg class="w-4 h-4 transition-transform duration-200"
                                :class="transaksiOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="transaksiOpen" @click.away="transaksiOpen = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-50 overflow-hidden">
                            <div class="py-2">
                                <a href="{{ route('topup') }}"
                                    class="block px-4 py-3 text-sm hover:bg-gray-50 transition-colors duration-200
                                      {{ request()->routeIs('topup') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                                    Deposit
                                </a>
                                <a href="{{ route('withdraw') }}"
                                    class="block px-4 py-3 text-sm hover:bg-gray-50 transition-colors duration-200
                                      {{ request()->routeIs('withdraw') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                                    Withdraw
                                </a>
                                <div class="border-t border-gray-200 my-2"></div>
                                <a href="{{ route('buy') }}"
                                    class="block px-4 py-3 text-sm hover:bg-gray-50 transition-colors duration-200
                                      {{ request()->routeIs('buy') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                                    Beli
                                </a>
                                <a href="{{ route('sell') }}"
                                    class="block px-4 py-3 text-sm hover:bg-gray-50 transition-colors duration-200
                                      {{ request()->routeIs('sell') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                                    Jual
                                </a>
                                <div class="border-t border-gray-200 my-2"></div>
                                <a href="{{ route('history') }}"
                                    class="block px-4 py-3 text-sm hover:bg-gray-50 transition-colors duration-200
                                      {{ request()->routeIs('history') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900' }}">
                                    Riwayat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Section -->
                <div class="flex items-center space-x-4">
                    <!-- Date -->
                    <div class="hidden md:block text-sm text-gray-500">
                        <span id="currentDate"></span>
                    </div>

                    <!-- User -->

                    <div class="relative" x-data="{ userOpen: false }">
                        <button @click="userOpen = !userOpen"
                            class="flex items-center space-x-2 text-sm text-gray-600 hover:text-gray-900 transition-colors duration-200">
                            <div
                                class="w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center">
                                <span class="font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        </button>

                        <div x-show="userOpen" @click.away="userOpen = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-50 overflow-hidden"
                            style="display: none;">
                            <div class="p-4 border-b border-gray-200">
                                <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 mt-1">{{ Auth::user()->email }}</p>
                            </div>

                            <div class="py-2">
                                <a href="{{ route('profile.edit') }}"
                                    class="block px-4 py-3 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200">
                                    Pengaturan Profil
                                </a>
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-3 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 transition-colors duration-200">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden">
                        <button @click="mobileOpen = !mobileOpen" x-data="{ mobileOpen: false }"
                            class="text-gray-600 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile menu -->
                <div class="md:hidden" x-data="{ mobileOpen: false }" x-show="mobileOpen"
                    @click.away="mobileOpen = false">
                    <div class="py-4 border-t border-gray-200 space-y-1">
                        <a href="{{ route('dashboard') }}"
                            class="block px-4 py-3 rounded-lg transition-colors duration-200
                          {{ request()->routeIs('dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('wallet.index') }}"
                            class="block px-4 py-3 rounded-lg transition-colors duration-200
                          {{ request()->routeIs('wallet.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            Dompet
                        </a>
                        <a href="{{ route('portfolio.index') }}"
                            class="block px-4 py-3 rounded-lg transition-colors duration-200
                          {{ request()->routeIs('portfolio.index') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                            Portfolio
                        </a>

                        <!-- Mobile Market Dropdown -->
                        <div x-data="{ mobileMarketOpen: false }" class="border-t border-gray-200 pt-2 mt-2">
                            <button @click="mobileMarketOpen = !mobileMarketOpen"
                                class="w-full flex items-center justify-between px-4 py-3 text-left text-gray-600 hover:text-gray-900">
                                <span class="font-medium">Market</span>
                                <svg class="w-4 h-4 transition-transform duration-200"
                                    :class="mobileMarketOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="mobileMarketOpen" class="pl-4 space-y-1 mt-1">
                                <a href="{{ route('market.index') }}"
                                    class="block px-4 py-3 rounded-lg transition-colors duration-200
                                  {{ request()->routeIs('market.index') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                    Indonesia (IDX)
                                </a>
                                <a href="{{ route('market.us') }}"
                                    class="block px-4 py-3 rounded-lg transition-colors duration-200
                                  {{ request()->routeIs('market.us') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                    US Market
                                </a>
                                <a href="{{ route('market.crypto') }}"
                                    class="block px-4 py-3 rounded-lg transition-colors duration-200
                                  {{ request()->routeIs('market.crypto') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                    Crypto
                                </a>
                                <a href="{{ route('market.commodities') }}"
                                    class="block px-4 py-3 rounded-lg transition-colors duration-200
                                  {{ request()->routeIs('market.commodities') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                    Commodities
                                </a>
                                <a href="{{ route('market.reksadana') }}"
                                    class="block px-4 py-3 rounded-lg transition-colors duration-200
                                  {{ request()->routeIs('market.reksadana') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                    Reksadana/ETF
                                </a>
                            </div>
                        </div>

                        <!-- Mobile Transaksi Dropdown -->
                        <div x-data="{ mobileTransaksiOpen: false }" class="border-t border-gray-200 pt-2 mt-2">
                            <button @click="mobileTransaksiOpen = !mobileTransaksiOpen"
                                class="w-full flex items-center justify-between px-4 py-3 text-left text-gray-600 hover:text-gray-900">
                                <span class="font-medium">Transaksi</span>
                                <svg class="w-4 h-4 transition-transform duration-200"
                                    :class="mobileTransaksiOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                            <div x-show="mobileTransaksiOpen" class="pl-4 space-y-1 mt-1">
                                <a href="{{ route('topup') }}"
                                    class="block px-4 py-3 rounded-lg transition-colors duration-200
                                  {{ request()->routeIs('topup') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                    Deposit
                                </a>
                                <a href="{{ route('withdraw') }}"
                                    class="block px-4 py-3 rounded-lg transition-colors duration-200
                                  {{ request()->routeIs('withdraw') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                    Withdraw
                                </a>
                                <div class="border-t border-gray-200 my-2"></div>
                                <a href="{{ route('buy') }}"
                                    class="block px-4 py-3 rounded-lg transition-colors duration-200
                                  {{ request()->routeIs('buy') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                    Beli
                                </a>
                                <a href="{{ route('sell') }}"
                                    class="block px-4 py-3 rounded-lg transition-colors duration-200
                                  {{ request()->routeIs('sell') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                    Jual
                                </a>
                                <div class="border-t border-gray-200 my-2"></div>
                                <a href="{{ route('history') }}"
                                    class="block px-4 py-3 rounded-lg transition-colors duration-200
                                  {{ request()->routeIs('history') ? 'bg-gray-100 text-gray-900' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                                    Riwayat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header -->
        @hasSection('header')
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900">@yield('header')</h1>
            @hasSection('header_description')
            <p class="text-gray-600 mt-2">@yield('header_description')</p>
            @endif
            <div class="border-b border-gray-200 mt-4"></div>
        </div>
        @endif

        <!-- Content Container -->
        @yield('content')

        <!-- Footer -->
        <div class="mt-12 pt-8 border-t border-gray-200">
            <div class="flex flex-col md:flex-row justify-between items-center text-sm text-gray-500">
                <span>MyInvest © {{ date('Y') }}</span>
                <span class="mt-2 md:mt-0">Dashboard Keuangan</span>
            </div>
        </div>
    </main>

    <script>
    // Date formatting
    document.addEventListener('DOMContentLoaded', function() {
        const dateElement = document.getElementById('currentDate');
        if (dateElement) {
            const now = new Date();
            const options = {
                weekday: 'short',
                day: 'numeric',
                month: 'short',
                year: 'numeric'
            };
            dateElement.textContent = now.toLocaleDateString('id-ID', options);
        }
    });
    </script>

    <!-- SweetAlert2 for notifications -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        background: '#ffffff',
        color: '#1a1a1a',
        border: '1px solid #e5e5e5',
        width: 'auto',
        padding: '0.75rem 1rem',
        customClass: {
            popup: 'shadow-lg rounded-lg'
        },
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    @if(session('success'))
    Toast.fire({
        icon: 'success',
        title: "{{ session('success') }}",
        iconColor: '#10b981',
    });
    @endif

    @if(session('error'))
    Toast.fire({
        icon: 'error',
        title: "{{ session('error') }}",
        iconColor: '#ef4444',
    });
    @endif
    </script>
    @yield('scripts')
</body>

</html>