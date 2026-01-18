<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    <style>
    body {
        font-family: 'Inter', sans-serif;
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
    </style>
</head>

<body class="bg-slate-900 text-slate-100 antialiased" x-data="{ sidebarOpen: true }">

    <div class="flex h-screen overflow-hidden">

        <aside
            class="bg-slate-950 text-white flex flex-col fixed h-full transition-all duration-300 ease-in-out z-50 border-r border-slate-800 shadow-2xl"
            :class="sidebarOpen ? 'w-64' : 'w-20'">
            <div class="h-16 flex items-center justify-between px-4 border-b border-slate-800 shrink-0">
                <div class="flex items-center justify-center w-full transition-opacity duration-300"
                    x-show="sidebarOpen" x-transition:enter="delay-100">
                    <h1 class="text-lg font-black tracking-wider text-indigo-500 whitespace-nowrap">👑 AdminPanel</h1>
                </div>

                <div class="flex items-center justify-center w-full" x-show="!sidebarOpen">
                    <span class="text-2xl">👑</span>
                </div>

                <button @click="sidebarOpen = !sidebarOpen"
                    class="absolute right-[-12px] top-5 bg-indigo-600 hover:bg-indigo-500 text-white border border-slate-900 shadow-md z-50 rounded-full w-6 h-6 flex items-center justify-center transform transition-transform duration-300"
                    :class="sidebarOpen ? '' : 'rotate-180'">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                </button>
            </div>

            <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4 hover:overflow-y-auto no-scrollbar space-y-6">

                <div>
                    <div class="px-6 py-2 text-[10px] font-bold text-slate-500 uppercase transition-all duration-300 whitespace-nowrap"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">
                        Master Data
                    </div>

                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                                class="relative flex items-center py-3 hover:bg-slate-800 transition-colors duration-200
                               {{ request()->routeIs('admin.dashboard') ? 'bg-slate-800 text-indigo-400' : 'text-slate-400' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Dashboard">
                                @if(request()->routeIs('admin.dashboard'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 rounded-r"></div>
                                @endif
                                <span class="text-lg w-6 text-center transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''"><i class="fas fa-home"></i></span>
                                <span class="whitespace-nowrap transition-opacity duration-300 font-medium"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">Dashboard</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.assets.index') }}"
                                class="relative flex items-center py-3 hover:bg-slate-800 transition-colors duration-200
                               {{ request()->routeIs('admin.assets.index') ? 'bg-slate-800 text-indigo-400' : 'text-slate-400' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Master Aset">
                                @if(request()->routeIs('admin.assets.index'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 rounded-r"></div>
                                @endif
                                <span class="text-lg w-6 text-center transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''"><i class="fas fa-coins"></i></span>
                                <span class="whitespace-nowrap transition-opacity duration-300 font-medium"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">Master
                                    Aset</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.exchange-rates.index') }}"
                                class="relative flex items-center py-3 hover:bg-slate-800 transition-colors duration-200
                               {{ request()->routeIs('admin.exchange-rates.index') ? 'bg-slate-800 text-indigo-400' : 'text-slate-400' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Kelola Valas">
                                @if(request()->routeIs('admin.exchange-rates.index'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500 rounded-r"></div>
                                @endif
                                <span class="text-lg w-6 text-center transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''"><i class="fas fa-globe"></i></span>
                                <span class="whitespace-nowrap transition-opacity duration-300 font-medium"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">Kelola
                                    Valas</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="mx-4 border-t border-slate-800" x-show="!sidebarOpen"></div>

                <div>
                    <div class="px-6 py-2 text-[10px] font-bold text-slate-500 uppercase transition-all duration-300 whitespace-nowrap"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">
                        Monitoring & User
                    </div>

                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.transactions.index') }}"
                                class="relative flex items-center py-3 hover:bg-slate-800 transition-colors duration-200
                               {{ request()->routeIs('admin.transactions.index') ? 'bg-slate-800 text-emerald-400' : 'text-slate-400' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Data Top Up">
                                @if(request()->routeIs('admin.transactions.index'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-emerald-500 rounded-r"></div>
                                @endif
                                <span class="text-lg w-6 text-center transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''"><i class="fas fa-arrow-down"></i></span>
                                <span class="whitespace-nowrap transition-opacity duration-300 font-medium"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">Data Top
                                    Up</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.withdrawals.index') }}"
                                class="relative flex items-center py-3 hover:bg-slate-800 transition-colors duration-200
                               {{ request()->routeIs('admin.withdrawals.index') ? 'bg-slate-800 text-rose-400' : 'text-slate-400' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Data Withdraw">
                                @if(request()->routeIs('admin.withdrawals.index'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-rose-500 rounded-r"></div>
                                @endif
                                <span class="text-lg w-6 text-center transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''"><i class="fas fa-arrow-up"></i></span>
                                <span class="whitespace-nowrap transition-opacity duration-300 font-medium"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">Data
                                    Withdraw</span>
                            </a>
                        </li>

                        <li>
                            <a href="#"
                                class="relative flex items-center py-3 hover:bg-slate-800 transition-colors duration-200 text-slate-400"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="App User">
                                <span class="text-lg w-6 text-center transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''"><i class="fas fa-users"></i></span>
                                <span class="whitespace-nowrap transition-opacity duration-300 font-medium"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">App User</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="border-t border-slate-800 bg-slate-950 transition-all duration-300"
                :class="sidebarOpen ? 'p-4' : 'p-2'">

                <div class="flex items-center gap-3 p-2" :class="sidebarOpen ? '' : 'justify-center'">
                    <div
                        class="w-8 h-8 rounded-full bg-indigo-500 text-white flex items-center justify-center font-bold text-xs shadow-lg shadow-indigo-500/50">
                        A
                    </div>
                    <div class="overflow-hidden transition-all duration-300"
                        :class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 hidden w-0'">
                        <p class="text-sm font-bold text-white truncate">Administrator</p>
                        <p class="text-[10px] text-slate-500 truncate">Super Admin</p>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button
                        class="w-full text-left text-xs text-rose-400 hover:text-rose-300 hover:bg-rose-500/10 px-3 py-2 rounded-lg flex items-center gap-2 transition"
                        :class="sidebarOpen ? '' : 'justify-center'" title="Keluar">
                        <i class="fas fa-sign-out-alt"></i>
                        <span x-show="sidebarOpen">Keluar Aplikasi</span>
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 h-full overflow-y-auto bg-slate-900 transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'ml-64' : 'ml-20'">

            <header
                class="bg-slate-900/80 backdrop-blur-md border-b border-slate-800 p-6 sticky top-0 z-40 flex items-center justify-between">
                <h2 class="text-xl font-bold text-white">Admin Dashboard</h2>
                <div class="text-xs text-slate-500 font-mono"
                    x-text="new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' })">
                </div>
            </header>

            <div class="p-8 pb-20">
                {{-- Notifikasi --}}
                @if(session('success'))
                <div
                    class="bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 text-sm">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div
                    class="bg-rose-500/10 border border-rose-500/20 text-rose-400 px-4 py-3 rounded-xl mb-6 flex items-center gap-2 text-sm">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
                @endif

                @yield('content')
            </div>
        </main>

    </div>

</body>

</html>