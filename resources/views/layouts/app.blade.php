<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyInvestment')</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
    body {
        font-family: 'Inter', sans-serif;
    }

    /* Utility untuk menyembunyikan scrollbar tapi tetap bisa scroll */
    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    /* Custom Scrollbar untuk bagian lain */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
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
    </style>
</head>

<body class="bg-gray-100 text-gray-800 antialiased" x-data="{ sidebarOpen: true }">

    <div class="flex h-screen overflow-hidden">

        <aside
            class="bg-gray-900 text-white flex flex-col fixed h-full transition-all duration-300 ease-in-out z-50 border-r border-gray-800 shadow-xl"
            :class="sidebarOpen ? 'w-64' : 'w-20'">
            <div class="h-16 flex items-center justify-between px-4 border-b border-gray-800 shrink-0">
                <div class="flex items-center justify-center w-full transition-opacity duration-300"
                    x-show="sidebarOpen" x-transition:enter="delay-100">
                    <h1 class="text-xl font-bold tracking-wider text-yellow-500 whitespace-nowrap">🚀 MyInvest</h1>
                </div>

                <div class="flex items-center justify-center w-full" x-show="!sidebarOpen">
                    <span class="text-2xl">🚀</span>
                </div>

                <button @click="sidebarOpen = !sidebarOpen"
                    class="p-1 rounded-md hover:bg-gray-700 text-gray-400 hover:text-white focus:outline-none absolute right-[-12px] top-5 bg-gray-800 border border-gray-700 shadow-sm z-50 rounded-full w-6 h-6 flex items-center justify-center transform transition-transform duration-300"
                    :class="sidebarOpen ? '' : 'rotate-180'">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4 hover:overflow-y-auto no-scrollbar">

                <div class="mb-2">
                    <div class="px-6 py-2 text-xs font-bold text-gray-500 uppercase transition-all duration-300 whitespace-nowrap"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">
                        Menu Utama
                    </div>

                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('dashboard') }}" class="relative flex items-center py-3 hover:bg-gray-800 transition-colors duration-200
                               {{ request()->routeIs('dashboard') ? 'bg-gray-800 text-yellow-500' : 'text-gray-300' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Dashboard">
                                @if(request()->routeIs('dashboard'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-yellow-500 rounded-r"></div>
                                @endif

                                <span class="text-xl transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''">🏠</span>
                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">Dashboard</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('wallet.index') }}"
                                class="relative flex items-center py-3 hover:bg-gray-800 transition-colors duration-200
                               {{ request()->routeIs('wallet.index') ? 'bg-gray-800 text-yellow-500' : 'text-gray-300' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Wallet">
                                @if(request()->routeIs('wallet.index'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-yellow-500 rounded-r"></div>
                                @endif
                                <span class="text-xl transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''">💰</span>
                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">Wallet
                                    (Dompet)</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('portfolio.index') }}"
                                class="relative flex items-center py-3 hover:bg-gray-800 transition-colors duration-200
                               {{ request()->routeIs('portfolio.index') ? 'bg-gray-800 text-yellow-500' : 'text-gray-300' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Portofolio">
                                @if(request()->routeIs('portfolio.index'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-yellow-500 rounded-r"></div>
                                @endif
                                <span class="text-xl transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''">📊</span>
                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">Portofolio</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('market.index') }}"
                                class="relative flex items-center py-3 hover:bg-gray-800 transition-colors duration-200
                               {{ request()->routeIs('market.index') ? 'bg-gray-800 text-yellow-500' : 'text-gray-300' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Exchange Rate">
                                @if(request()->routeIs('market.index'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-yellow-500 rounded-r"></div>
                                @endif
                                <span class="text-xl transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''">💱</span>
                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">Exchange
                                    Rate</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="mx-4 border-t border-gray-700 my-2" x-show="!sidebarOpen"></div>

                <div class="mb-2">
                    <div class="px-6 py-2 text-xs font-bold text-gray-500 uppercase transition-all duration-300 whitespace-nowrap mt-4"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">
                        Transaksi
                    </div>

                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('topup') }}" class="relative flex items-center py-3 hover:bg-gray-800 transition-colors duration-200
                               {{ request()->routeIs('topup') ? 'bg-gray-800 text-green-400' : 'text-gray-300' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Top Up">
                                @if(request()->routeIs('topup'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-green-500 rounded-r"></div>
                                @endif
                                <span class="text-xl transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''">📥</span>
                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">Top Up</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('withdraw') }}" class="relative flex items-center py-3 hover:bg-gray-800 transition-colors duration-200
                               {{ request()->routeIs('withdraw') ? 'bg-gray-800 text-red-400' : 'text-gray-300' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Withdraw">
                                @if(request()->routeIs('withdraw'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-500 rounded-r"></div>
                                @endif
                                <span class="text-xl transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''">💸</span>
                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">Withdraw</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('buy') }}" class="relative flex items-center py-3 hover:bg-gray-800 transition-colors duration-200
                               {{ request()->routeIs('buy') ? 'bg-gray-800 text-indigo-400' : 'text-gray-300' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Beli Aset">
                                @if(request()->routeIs('buy'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 rounded-r"></div>
                                @endif
                                <span class="text-xl transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''">🛒</span>
                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">Beli Aset</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('history') }}" class="relative flex items-center py-3 hover:bg-gray-800 transition-colors duration-200
                               {{ request()->routeIs('history') ? 'bg-gray-800 text-blue-400' : 'text-gray-300' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Riwayat">
                                @if(request()->routeIs('history'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500 rounded-r"></div>
                                @endif
                                <span class="text-xl transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''">📜</span>
                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">Riwayat</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="border-t border-gray-800 bg-gray-900 transition-all duration-300"
                :class="sidebarOpen ? 'p-4' : 'p-2'">

                <a href="{{ route('profile.edit') }}"
                    class="flex items-center hover:bg-gray-800 rounded-lg transition-colors group"
                    :class="sidebarOpen ? 'gap-3 p-2' : 'justify-center p-2'">

                    @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                        class="rounded-full object-cover border-2 border-transparent group-hover:border-yellow-500 transition-all"
                        :class="sidebarOpen ? 'w-10 h-10' : 'w-8 h-8'">
                    @else
                    <div class="rounded-full bg-yellow-500 text-black flex items-center justify-center font-bold shadow-md"
                        :class="sidebarOpen ? 'w-10 h-10 text-base' : 'w-8 h-8 text-xs'">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    @endif

                    <div class="overflow-hidden transition-all duration-300"
                        :class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 hidden w-0'">
                        <p class="text-sm font-bold text-white truncate w-32">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500 truncate">Edit Profil</p>
                    </div>
                </a>

                <form action="{{ route('logout') }}" method="POST" class="mt-2" x-show="sidebarOpen" x-transition>
                    @csrf
                    <button
                        class="w-full text-left text-xs text-red-400 hover:text-red-300 px-2 py-1 flex items-center gap-2 hover:bg-gray-800/50 rounded">
                        <span>🚪</span> Keluar Aplikasi
                    </button>
                </form>

                <form action="{{ route('logout') }}" method="POST" class="mt-2 text-center" x-show="!sidebarOpen">
                    @csrf
                    <button class="text-red-400 hover:text-red-300 p-2 hover:bg-gray-800 rounded-full" title="Keluar">
                        🚪
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 h-full overflow-y-auto bg-gray-100 transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'ml-64' : 'ml-20'">

            <header class="bg-white shadow-sm p-6 sticky top-0 z-40 flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h2 class="text-2xl font-bold text-gray-800">@yield('header', 'Dashboard')</h2>
                </div>

                <div class="text-sm text-gray-500"
                    x-text="new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })">
                </div>
            </header>

            <div class="p-6 pb-20">
                @yield('content')
            </div>
        </main>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    // 1. Notifikasi Sukses
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Berhasil!',
        text: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 2000,
        background: '#f0fdf4',
        iconColor: '#16a34a'
    });
    @endif

    // 2. Notifikasi Error
    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Oops...',
        text: "{{ session('error') }}",
        background: '#fef2f2',
        iconColor: '#dc2626'
    });
    @endif

    // 3. Notifikasi Validasi Form
    @if(count($errors) > 0)
    Swal.fire({
        icon: 'warning',
        title: 'Periksa Inputan',
        html: '<ul class="text-left text-sm">@foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach</ul>',
    });
    @endif

    // 4. Loading State
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = `
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Memproses...
                        `;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                }
            });
        });
    });
    </script>

    @yield('scripts')
</body>

</html>