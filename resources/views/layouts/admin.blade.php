<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    @vite('resources/css/app.css')

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f9fafb;
        color: #111827;
    }

    /* Custom Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: transparent;
    }

    ::-webkit-scrollbar-thumb {
        background: #d1d5db;
        border-radius: 10px;
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

    <nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                {{-- Logo Area --}}
                <div class="flex items-center space-x-3">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center space-x-3 no-underline group">
                        <div
                            class="w-10 h-10 rounded-lg bg-black flex items-center justify-center group-hover:bg-gray-800 transition-colors shadow-sm">
                            <span class="text-white font-bold text-lg">A</span>
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-lg font-bold text-gray-900 leading-tight">AdminPanel</h1>
                            <span
                                class="text-[10px] font-semibold text-gray-500 tracking-widest uppercase">Management</span>
                        </div>
                    </a>
                </div>

                {{-- Desktop Navigation --}}
                <div class="hidden md:flex items-center space-x-1">

                    {{-- Dashboard Link --}}
                    <a href="{{ route('admin.dashboard') }}"
                        class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200
                        {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50' }}">
                        Dashboard
                    </a>

                    {{-- Master Data Dropdown (UPDATED STRUKTUR) --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 flex items-center space-x-1
                            {{ request()->routeIs('admin.assets.*') ? 'bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50' }}">
                            <span>Master Data</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute left-0 mt-2 w-56 bg-white border border-gray-200 rounded-lg shadow-xl z-50 py-2">

                            {{-- Menu Saham --}}
                            <a href="{{ route('admin.assets.index', ['type' => 'Stock']) }}"
                                class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-black group">
                                <span class="w-6 text-center mr-2">🏢</span>
                                Saham Indonesia
                            </a>

                            <a href="{{ route('admin.assets.index', ['type' => 'US Stock']) }}"
                                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                🗽 Saham Amerika
                            </a>

                            {{-- Menu Reksadana --}}
                            <a href="{{ route('admin.assets.index', ['type' => 'Mutual Fund']) }}"
                                class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-black group">
                                <span class="w-6 text-center mr-2">📈</span>
                                Reksadana
                            </a>

                            {{-- Menu Crypto --}}
                            <a href="{{ route('admin.assets.index', ['type' => 'Crypto']) }}"
                                class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-black group">
                                <span class="w-6 text-center mr-2">₿</span>
                                Crypto Assets
                            </a>

                            {{-- Menu Emas --}}
                            <a href="{{ route('admin.assets.index', ['type' => 'Gold']) }}"
                                class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-black group">
                                <span class="w-6 text-center mr-2">🥇</span>
                                Emas & Komoditas
                            </a>

                            <div class="border-t border-gray-100 my-1"></div>

                            {{-- Menu Kurs Valas --}}
                            <a href="{{ route('admin.exchange-rates.index') }}"
                                class="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 hover:text-black group">
                                <span class="w-6 text-center mr-2">💱</span>
                                Kurs Valas
                            </a>
                        </div>
                    </div>

                    {{-- Approval Dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 flex items-center space-x-1
                            {{ request()->routeIs('admin.transactions.*') || request()->routeIs('admin.withdrawals.*') ? 'bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50' }}">
                            <span>Approval</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false" x-cloak
                            x-transition:enter="transition ease-out duration-100"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            class="absolute left-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-xl z-50 py-1">

                            <a href="{{ route('admin.transactions.index') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-black">
                                Data Top Up
                            </a>
                            <a href="{{ route('admin.withdrawals.index') }}"
                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-black">
                                Data Withdraw
                            </a>
                        </div>
                    </div>

                    <a href="{{ route('admin.users.index') }}"
                        class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200
                        {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 text-black' : 'text-gray-600 hover:text-black hover:bg-gray-50' }}">
                        Pengguna
                    </a>

                </div>

                {{-- User Profile Area --}}
                <div class="flex items-center space-x-4">
                    <div class="hidden md:block text-xs font-medium text-gray-400 font-mono" id="currentDate"></div>

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
                            class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-md shadow-xl z-50">
                            <div class="p-3 border-b border-gray-100">
                                <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 truncate">Administrator</p>
                            </div>
                            <div class="py-1">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 font-medium flex items-center">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    {{-- Mobile Menu Button --}}
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

        {{-- Mobile Menu --}}
        <div class="md:hidden border-t border-gray-200 bg-white" x-data="{ mobileOpen: false }" x-show="mobileOpen"
            @click.away="mobileOpen = false" style="display: none;">
            <div class="px-2 pt-2 pb-3 space-y-1">
                <a href="{{ route('admin.dashboard') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50">Dashboard</a>

                {{-- Mobile Assets Menu --}}
                <div class="block px-3 py-2 text-base font-medium text-gray-900 border-b border-gray-100 pb-2 mb-2">
                    Master Data</div>
                <a href="{{ route('admin.assets.index', ['type' => 'Stock']) }}"
                    class="block px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-50 pl-6">Saham
                    Indonesia</a>
                <a href="{{ route('admin.assets.index', ['type' => 'Mutual Fund']) }}"
                    class="block px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-50 pl-6">Reksadana</a>
                <a href="{{ route('admin.assets.index', ['type' => 'Crypto']) }}"
                    class="block px-3 py-2 rounded-md text-sm font-medium text-gray-600 hover:bg-gray-50 pl-6">Crypto</a>

                <a href="{{ route('admin.users.index') }}"
                    class="block px-3 py-2 rounded-md text-base font-medium text-gray-700 hover:bg-gray-50 mt-2">Pengguna</a>
            </div>
        </div>
    </nav>

    <main class="flex-grow max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @hasSection('header')
        <div class="mb-8">
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">@yield('header')</h1>
            <div class="w-12 h-1 bg-black mt-4 rounded-full"></div>
        </div>
        @endif

        {{-- Notifications --}}
        @if(session('success'))
        <div
            class="bg-white border-l-4 border-black text-gray-700 px-4 py-3 shadow-sm mb-6 flex items-center gap-3 text-sm rounded-r-md">
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div
            class="bg-white border-l-4 border-red-500 text-gray-700 px-4 py-3 shadow-sm mb-6 flex items-center gap-3 text-sm rounded-r-md">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-t border-gray-200 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center text-xs text-gray-500">
                <p>&copy; {{ date('Y') }} MyInvest Admin.</p>
                <p class="mt-2 md:mt-0 font-medium text-gray-400">Secure Admin Panel</p>
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
    @yield('scripts')
</body>

</html>