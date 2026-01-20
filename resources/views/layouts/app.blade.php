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

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
    body {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    /* Custom Scrollbar untuk Tema Terang */
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

    .no-scrollbar::-webkit-scrollbar {
        display: none;
    }

    .no-scrollbar {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    [x-cloak] {
        display: none !important;
    }

    .sidebar-transition {
        transition: width 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    </style>
</head>

<body class="bg-slate-50 text-slate-600 antialiased overflow-hidden" x-data="{ 
          sidebarOpen: localStorage.getItem('sidebarOpen') === 'true' || localStorage.getItem('sidebarOpen') === null,
          toggleSidebar() {
              this.sidebarOpen = !this.sidebarOpen;
              localStorage.setItem('sidebarOpen', this.sidebarOpen);
          }
      }">

    <div class="flex h-screen w-full">

        {{-- SIDEBAR --}}
        <aside
            class="fixed inset-y-0 left-0 z-50 flex flex-col h-full bg-white border-r border-slate-200 sidebar-transition shadow-xl shadow-slate-200/50"
            :class="sidebarOpen ? 'w-72' : 'w-24'">

            {{-- 1. HEADER / LOGO --}}
            <div class="h-24 flex items-center justify-between px-6 shrink-0 relative">

                {{-- Logo Full (Saat Buka) --}}
                <div class="flex items-center gap-3 overflow-hidden transition-all duration-300" x-show="sidebarOpen"
                    x-transition.opacity>
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-500/20">
                        M</div>
                    <div class="flex flex-col">
                        <h1 class="text-xl font-bold text-slate-800 tracking-tight">My<span
                                class="text-indigo-600">Invest</span>.</h1>
                        <span class="text-[10px] font-bold text-slate-400 tracking-widest uppercase">Finance App</span>
                    </div>
                </div>

                {{-- Logo Kecil (Saat Tutup) --}}
                <div class="w-full flex justify-center absolute left-0" x-show="!sidebarOpen" x-transition.opacity>
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-500/20">
                        M</div>
                </div>

                {{-- Tombol Toggle (Gunakan fungsi toggleSidebar) --}}
                <button @click="toggleSidebar()"
                    class="absolute -right-3 top-9 bg-white border border-slate-200 text-slate-400 rounded-full w-7 h-7 flex items-center justify-center hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition-all duration-200 shadow-sm z-50">
                    <svg class="w-3 h-3 transition-transform duration-300" :class="!sidebarOpen ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7">
                        </path>
                    </svg>
                </button>
            </div>


            {{-- 2. NAVIGASI --}}
            <nav class="flex-1 overflow-y-auto overflow-x-hidden py-6 px-4 space-y-8 no-scrollbar">

                {{-- Group: Main Menu --}}
                <div>
                    <div class="px-2 mb-3 text-xs font-bold text-slate-400 uppercase tracking-wider transition-opacity duration-300"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">
                        Menu Utama
                    </div>
                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('dashboard') }}"
                                class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 font-medium {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-600' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}"
                                :class="!sidebarOpen ? 'justify-center px-2' : ''">

                                <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'group-hover:text-indigo-500 transition-colors' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                    </path>
                                </svg>

                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden w-0'">Dashboard</span>

                                @if(request()->routeIs('dashboard'))
                                <div class="absolute right-3 w-1.5 h-1.5 rounded-full bg-indigo-600"
                                    x-show="sidebarOpen"></div>
                                @endif
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('wallet.index') }}"
                                class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 font-medium {{ request()->routeIs('wallet.*') ? 'bg-purple-50 text-purple-600' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}"
                                :class="!sidebarOpen ? 'justify-center px-2' : ''">

                                <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('wallet.*') ? 'text-purple-600' : 'group-hover:text-purple-500 transition-colors' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                    </path>
                                </svg>

                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden w-0'">Dompet Saya</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('portfolio.index') }}"
                                class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 font-medium {{ request()->routeIs('portfolio.index') ? 'bg-emerald-50 text-emerald-600' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}"
                                :class="!sidebarOpen ? 'justify-center px-2' : ''">

                                <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('portfolio.index') ? 'text-emerald-600' : 'group-hover:text-emerald-500 transition-colors' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                                </svg>

                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden w-0'">Portfolio</span>
                            </a>
                        </li>

                        @if(Route::has('market.index'))
                        <li>
                            <a href="{{ route('market.index') }}"
                                class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 font-medium {{ request()->routeIs('market.index') ? 'bg-orange-50 text-orange-600' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}"
                                :class="!sidebarOpen ? 'justify-center px-2' : ''">

                                <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('market.index') ? 'text-orange-600' : 'group-hover:text-orange-500 transition-colors' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                                </svg>

                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden w-0'">Pasar Saham</span>
                            </a>
                        </li>
                        @endif
                    </ul>
                </div>

                <div class="h-px bg-slate-100 my-2 mx-4" x-show="sidebarOpen"></div>

                {{-- Group: Transaksi --}}
                <div>
                    <div class="px-2 mb-3 text-xs font-bold text-slate-400 uppercase tracking-wider transition-opacity duration-300"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">
                        Transaksi
                    </div>
                    <ul class="space-y-1">
                        {{-- Menu Deposit --}}
                        <li>
                            <a href="{{ route('topup') }}"
                                class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('topup') ? 'text-emerald-600 bg-emerald-50' : '' }}"
                                :class="!sidebarOpen ? 'justify-center px-2' : ''">
                                <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('topup') ? 'text-emerald-600' : 'group-hover:text-emerald-500' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden w-0'">Deposit</span>
                            </a>
                        </li>

                        {{-- Menu Withdraw --}}
                        <li>
                            <a href="{{ route('withdraw') }}"
                                class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('withdraw') ? 'text-rose-600 bg-rose-50' : '' }}"
                                :class="!sidebarOpen ? 'justify-center px-2' : ''">
                                <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('withdraw') ? 'text-rose-600' : 'group-hover:text-rose-500' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden w-0'">Withdraw</span>
                            </a>
                        </li>

                        {{-- Menu Beli Aset --}}
                        <li>
                            <a href="{{ route('buy') }}"
                                class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('buy') ? 'text-blue-600 bg-blue-50' : '' }}"
                                :class="!sidebarOpen ? 'justify-center px-2' : ''">
                                <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('buy') ? 'text-blue-600' : 'group-hover:text-blue-500' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden w-0'">Beli Aset</span>
                            </a>
                        </li>

                        {{-- 🔥 MENU BARU: JUAL ASET --}}
                        <li>
                            <a href="{{ route('sell') }}"
                                class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('sell') ? 'text-rose-600 bg-rose-50' : '' }}"
                                :class="!sidebarOpen ? 'justify-center px-2' : ''">
                                <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('sell') ? 'text-rose-600' : 'group-hover:text-rose-500' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden w-0'">Jual Aset</span>
                            </a>
                        </li>

                        {{-- Menu Riwayat --}}
                        <li>
                            <a href="{{ route('history') }}"
                                class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-200 font-medium text-slate-500 hover:bg-slate-50 hover:text-slate-900 {{ request()->routeIs('history') ? 'text-amber-600 bg-amber-50' : '' }}"
                                :class="!sidebarOpen ? 'justify-center px-2' : ''">
                                <svg class="w-6 h-6 shrink-0 {{ request()->routeIs('history') ? 'text-amber-600' : 'group-hover:text-amber-500' }}"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="whitespace-nowrap transition-opacity duration-300"
                                    :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden w-0'">Riwayat</span>
                            </a>
                        </li>
                    </ul>
                </div>

            </nav>

            {{-- 3. PROFILE SECTION: Light Mode --}}
            <div class="border-t border-slate-200 bg-slate-50/50 p-4">
                <div class="flex items-center gap-3" :class="!sidebarOpen ? 'justify-center' : ''">
                    <div class="relative group cursor-pointer">
                        <div
                            class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold border border-indigo-200">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                        <div
                            class="absolute bottom-0 right-0 w-3 h-3 bg-emerald-500 border-2 border-white rounded-full">
                        </div>
                    </div>

                    <div class="overflow-hidden flex flex-col" x-show="sidebarOpen" x-transition.opacity>
                        <p class="text-sm font-bold text-slate-700 truncate w-32">{{ Auth::user()->name }}</p>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button
                                class="text-xs text-slate-500 hover:text-rose-500 flex items-center gap-1 transition-colors mt-0.5">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

        </aside>

        {{-- MAIN CONTENT AREA: Light Mode --}}
        <main class="flex-1 h-full overflow-y-auto bg-slate-50 transition-all duration-300 ease-in-out relative z-0"
            :class="sidebarOpen ? 'ml-72' : 'ml-24'">

            <header
                class="bg-white/80 backdrop-blur-md border-b border-slate-200 p-6 sticky top-0 z-40 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 tracking-tight">@yield('header', 'Dashboard')</h2>
                    <p class="text-slate-500 text-sm mt-1">Selamat datang kembali, <span
                            class="font-semibold text-indigo-600">{{ Auth::user()->name }}</span>!</p>
                </div>

                <div class="flex items-center gap-4">
                    <div
                        class="hidden md:flex items-center gap-2 px-4 py-2 rounded-full bg-slate-100 border border-slate-200 text-xs text-slate-500 font-medium font-mono">
                        <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                            </path>
                        </svg>
                        <span
                            x-text="new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })"></span>
                    </div>
                </div>
            </header>

            <div class="p-8 pb-32">
                @yield('content')
            </div>
        </main>
    </div>

    {{-- SweetAlert Global Script (Disesuaikan warnanya agar tidak terlalu dark) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Sukses',
        text: "{{ session('success') }}",
        showConfirmButton: false,
        timer: 2000,
        confirmButtonColor: '#4f46e5', // Indigo
    });
    @endif

    @if(session('error'))
    Swal.fire({
        icon: 'error',
        title: 'Gagal',
        text: "{{ session('error') }}",
        confirmButtonColor: '#e11d48', // Rose
    });
    @endif
    </script>
    @yield('scripts')
</body>

</html>