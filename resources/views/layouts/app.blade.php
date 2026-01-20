<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyInvestment Dashboard')</title>
    @vite('resources/css/app.css')

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Fonts: Plus Jakarta Sans --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    {{-- Alpine.js untuk dropdown --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    ::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    [x-cloak] {
        display: none !important;
    }

    /* Smooth dropdown animation */
    .dropdown-enter {
        opacity: 0;
        transform: translateY(-10px);
    }

    .dropdown-enter-active {
        opacity: 1;
        transform: translateY(0);
        transition: opacity 200ms, transform 200ms;
    }

    .dropdown-leave {
        opacity: 1;
        transform: translateY(0);
    }

    .dropdown-leave-active {
        opacity: 0;
        transform: translateY(-10px);
        transition: opacity 200ms, transform 200ms;
    }
    </style>
</head>

<body class="bg-slate-50 text-slate-600 antialiased">
    <!-- HORIZONTAL NAVBAR DENGAN DROPDOWN -->
    <nav class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <!-- Logo Section -->
                <div class="flex items-center">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-500/20">
                            M
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-xl font-bold text-slate-800 tracking-tight">My<span
                                    class="text-indigo-600">Invest</span>.</h1>
                            <span class="text-[10px] font-bold text-slate-400 tracking-widest uppercase">Finance
                                App</span>
                        </div>
                    </div>
                </div>

                <!-- Navigation Links - Center -->
                <div class="hidden md:flex items-center space-x-8">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}"
                        class="{{ request()->routeIs('dashboard') ? 'text-indigo-600 border-b-2 border-indigo-600' : 'text-slate-700 hover:text-indigo-600' }} px-3 py-2 text-sm font-medium transition-colors duration-200">
                        Dashboard
                    </a>

                    <!-- Dompet Saya -->
                    <a href="{{ route('wallet.index') }}"
                        class="{{ request()->routeIs('wallet.*') ? 'text-purple-600 border-b-2 border-purple-600' : 'text-slate-700 hover:text-purple-600' }} px-3 py-2 text-sm font-medium transition-colors duration-200">
                        Dompet Saya
                    </a>

                    <!-- Portfolio -->
                    <a href="{{ route('portfolio.index') }}"
                        class="{{ request()->routeIs('portfolio.index') ? 'text-emerald-600 border-b-2 border-emerald-600' : 'text-slate-700 hover:text-emerald-600' }} px-3 py-2 text-sm font-medium transition-colors duration-200">
                        Portfolio
                    </a>

                    <!-- Pasar Saham -->
                    @if(Route::has('market.index'))
                    <a href="{{ route('market.index') }}"
                        class="{{ request()->routeIs('market.index') ? 'text-orange-600 border-b-2 border-orange-600' : 'text-slate-700 hover:text-orange-600' }} px-3 py-2 text-sm font-medium transition-colors duration-200">
                        Pasar Saham
                    </a>
                    @endif

                    <!-- Transaksi Dropdown -->
                    <div class="relative" x-data="{ open: false }" @mouseenter="open = true" @mouseleave="open = false">
                        <button
                            class="{{ request()->routeIs(['topup', 'withdraw', 'buy', 'sell', 'history']) ? 'text-indigo-600' : 'text-slate-700' }} hover:text-indigo-600 px-3 py-2 text-sm font-medium flex items-center gap-1 transition-colors duration-200">
                            Transaksi
                            <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 mt-2 w-56 origin-top-left bg-white rounded-lg shadow-lg border border-slate-200 z-50">
                            <div class="py-2">
                                <!-- Deposit -->
                                <a href="{{ route('topup') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-sm {{ request()->routeIs('topup') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <svg class="w-5 h-5 {{ request()->routeIs('topup') ? 'text-emerald-600' : 'text-slate-400' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    Deposit
                                </a>

                                <!-- Withdraw -->
                                <a href="{{ route('withdraw') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-sm {{ request()->routeIs('withdraw') ? 'bg-rose-50 text-rose-600' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <svg class="w-5 h-5 {{ request()->routeIs('withdraw') ? 'text-rose-600' : 'text-slate-400' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    Withdraw
                                </a>

                                <!-- Beli Aset -->
                                <a href="{{ route('buy') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-sm {{ request()->routeIs('buy') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <svg class="w-5 h-5 {{ request()->routeIs('buy') ? 'text-blue-600' : 'text-slate-400' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Beli Aset
                                </a>

                                <!-- Jual Aset -->
                                <a href="{{ route('sell') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-sm {{ request()->routeIs('sell') ? 'bg-rose-50 text-rose-600' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <svg class="w-5 h-5 {{ request()->routeIs('sell') ? 'text-rose-600' : 'text-slate-400' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Jual Aset
                                </a>

                                <div class="border-t border-slate-100 my-1"></div>

                                <!-- Riwayat -->
                                <a href="{{ route('history') }}"
                                    class="flex items-center gap-3 px-4 py-3 text-sm {{ request()->routeIs('history') ? 'bg-amber-50 text-amber-600' : 'text-slate-700 hover:bg-slate-50' }}">
                                    <svg class="w-5 h-5 {{ request()->routeIs('history') ? 'text-amber-600' : 'text-slate-400' }}"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Riwayat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Section: Date & User -->
                <div class="flex items-center space-x-6">
                    <!-- Date -->
                    <div
                        class="hidden md:flex items-center gap-2 px-4 py-2 rounded-full bg-slate-100 border border-slate-200 text-xs text-slate-500 font-medium font-mono">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span id="currentDate"></span>
                    </div>

                    <!-- User Dropdown -->
                    <div class="relative" x-data="{ open: false }" @click.away="open = false">
                        <button @click="open = !open"
                            class="flex items-center gap-3 text-sm font-medium text-slate-700 hover:text-slate-900 focus:outline-none transition-colors duration-200">
                            <div class="relative">
                                <div
                                    class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border border-indigo-200 text-sm">
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                </div>
                                <div
                                    class="absolute bottom-0 right-0 w-2 h-2 bg-emerald-500 border border-white rounded-full">
                                </div>
                            </div>
                            <span class="hidden md:inline">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-48 origin-top-right bg-white rounded-lg shadow-lg border border-slate-200 z-50">
                            <div class="py-2">
                                <div class="px-4 py-3 border-b border-slate-100">
                                    <p class="text-sm font-semibold text-slate-800">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-slate-500 mt-1">{{ Auth::user()->email }}</p>
                                </div>

                                <form action="{{ route('logout') }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit"
                                        class="flex items-center gap-3 w-full px-4 py-3 text-sm text-slate-700 hover:bg-slate-50 hover:text-rose-600 transition-colors duration-200">
                                        <svg class="w-4 h-4 text-slate-400 group-hover:text-rose-500" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                            </path>
                                        </svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden flex items-center">
                    <button @click="open = !open" class="text-slate-700 hover:text-slate-900 focus:outline-none">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile menu (responsive) -->
        <div class="md:hidden" x-data="{ open: false }" x-show="open" @click.away="open = false">
            <div class="px-2 pt-2 pb-3 space-y-1 bg-white border-t border-slate-200">
                <!-- Mobile Menu Items -->
                <a href="{{ route('dashboard') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-700 hover:bg-slate-50' }}">
                    Dashboard
                </a>
                <a href="{{ route('wallet.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('wallet.*') ? 'bg-purple-50 text-purple-600' : 'text-slate-700 hover:bg-slate-50' }}">
                    Dompet Saya
                </a>
                <a href="{{ route('portfolio.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('portfolio.index') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-700 hover:bg-slate-50' }}">
                    Portfolio
                </a>
                <div class="border-t border-slate-100 my-2"></div>
                <p class="px-3 py-2 text-xs font-bold text-slate-400 uppercase">Transaksi</p>
                <a href="{{ route('topup') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('topup') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-700 hover:bg-slate-50' }}">
                    Deposit
                </a>
                <a href="{{ route('withdraw') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('withdraw') ? 'bg-rose-50 text-rose-600' : 'text-slate-700 hover:bg-slate-50' }}">
                    Withdraw
                </a>
                <a href="{{ route('buy') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('buy') ? 'bg-blue-50 text-blue-600' : 'text-slate-700 hover:bg-slate-50' }}">
                    Beli Aset
                </a>
                <a href="{{ route('sell') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('sell') ? 'bg-rose-50 text-rose-600' : 'text-slate-700 hover:bg-slate-50' }}">
                    Jual Aset
                </a>
                <a href="{{ route('history') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium {{ request()->routeIs('history') ? 'bg-amber-50 text-amber-600' : 'text-slate-700 hover:bg-slate-50' }}">
                    Riwayat
                </a>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT AREA -->
    <main class="h-full overflow-y-auto bg-slate-50">
        <div class="p-4 md:p-6 lg:p-8">
            <!-- Page Header -->
            <div class="mb-6 md:mb-8">
                <h2 class="text-2xl md:text-3xl font-bold text-slate-800 tracking-tight">@yield('header', 'Dashboard')
                </h2>
                <p class="text-slate-500 text-sm md:text-base mt-1 md:mt-2">Selamat datang kembali, <span
                        class="font-semibold text-indigo-600">{{ Auth::user()->name }}</span>!</p>
            </div>

            <!-- Content -->
            @yield('content')
        </div>
    </main>

    <script>
    // Script untuk menampilkan tanggal
    document.addEventListener('DOMContentLoaded', function() {
        const dateElement = document.getElementById('currentDate');
        if (dateElement) {
            const options = {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            dateElement.textContent = new Date().toLocaleDateString('id-ID', options);
        }
    });
    </script>

    <!-- SweetAlert dan script lainnya -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Sukses',
        text: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 2000,
        confirmButtonColor: '#4f46e5',
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: "{{ session('error') }}",
        confirmButtonColor: '#e11d48',
    });
    @endif
    </script>
    @yield('scripts')
</body>

</html>