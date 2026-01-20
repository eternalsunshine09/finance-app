<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel')</title>
    @vite('resources/css/app.css')
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

    /* Custom Scrollbar */
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
    </style>
</head>

<body class="bg-gray-50 text-gray-900 antialiased" x-data="{ sidebarOpen: true }">

    <div class="flex h-screen overflow-hidden">

        {{-- Sidebar --}}
        <aside
            class="bg-white text-gray-800 flex flex-col fixed h-full transition-all duration-300 ease-in-out z-50 border-r border-gray-200 shadow-lg"
            :class="sidebarOpen ? 'w-64' : 'w-20'">

            {{-- Sidebar Header --}}
            <div class="h-16 flex items-center justify-between px-4 border-b border-gray-200 shrink-0">
                <div class="flex items-center justify-center w-full transition-opacity duration-300"
                    x-show="sidebarOpen" x-transition:enter="delay-100">
                    <h1 class="text-lg font-black tracking-wider text-gray-900 whitespace-nowrap">
                        <span class="text-gray-600">👑</span> AdminPanel
                    </h1>
                </div>

                <div class="flex items-center justify-center w-full" x-show="!sidebarOpen">
                    <span class="text-2xl text-gray-600">👑</span>
                </div>

                {{-- Toggle Button --}}
                <button @click="sidebarOpen = !sidebarOpen"
                    class="absolute right-[-12px] top-5 bg-gray-800 hover:bg-gray-900 text-white border border-gray-300 shadow-md z-50 rounded-full w-6 h-6 flex items-center justify-center transform transition-transform duration-300"
                    :class="sidebarOpen ? '' : 'rotate-180'">
                    <i class="fas fa-chevron-left text-[10px]"></i>
                </button>
            </div>

            {{-- Sidebar Navigation --}}
            <nav class="flex-1 overflow-y-auto overflow-x-hidden py-4 hover:overflow-y-auto no-scrollbar space-y-6">
                {{-- Master Data Section --}}
                <div>
                    <div class="px-6 py-2 text-[10px] font-bold text-gray-500 uppercase transition-all duration-300 whitespace-nowrap"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">
                        Master Data
                    </div>

                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.dashboard') }}"
                                class="relative flex items-center py-3 hover:bg-gray-100 transition-colors duration-200
                               {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 text-gray-900' : 'text-gray-600' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Dashboard">
                                @if(request()->routeIs('admin.dashboard'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-gray-800 rounded-r"></div>
                                @endif
                                <span class="text-lg w-6 text-center transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''"><i class="fas fa-home"></i></span>
                                <span class="whitespace-nowrap transition-opacity duration-300 font-medium"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">Dashboard</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.assets.index') }}"
                                class="relative flex items-center py-3 hover:bg-gray-100 transition-colors duration-200
                               {{ request()->routeIs('admin.assets.index') ? 'bg-gray-100 text-gray-900' : 'text-gray-600' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Master Aset">
                                @if(request()->routeIs('admin.assets.index'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-gray-800 rounded-r"></div>
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
                                class="relative flex items-center py-3 hover:bg-gray-100 transition-colors duration-200
                               {{ request()->routeIs('admin.exchange-rates.index') ? 'bg-gray-100 text-gray-900' : 'text-gray-600' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Kelola Valas">
                                @if(request()->routeIs('admin.exchange-rates.index'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-gray-800 rounded-r"></div>
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

                <div class="mx-4 border-t border-gray-200" x-show="!sidebarOpen"></div>

                {{-- Monitoring & User Section --}}
                <div>
                    <div class="px-6 py-2 text-[10px] font-bold text-gray-500 uppercase transition-all duration-300 whitespace-nowrap"
                        :class="sidebarOpen ? 'opacity-100' : 'opacity-0 hidden'">
                        Monitoring & User
                    </div>

                    <ul class="space-y-1">
                        <li>
                            <a href="{{ route('admin.transactions.index') }}"
                                class="relative flex items-center py-3 hover:bg-gray-100 transition-colors duration-200
                               {{ request()->routeIs('admin.transactions.index') ? 'bg-gray-100 text-gray-900' : 'text-gray-600' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Data Top Up">
                                @if(request()->routeIs('admin.transactions.index'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-gray-800 rounded-r"></div>
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
                                class="relative flex items-center py-3 hover:bg-gray-100 transition-colors duration-200
                               {{ request()->routeIs('admin.withdrawals.index') ? 'bg-gray-100 text-gray-900' : 'text-gray-600' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="Data Withdraw">
                                @if(request()->routeIs('admin.withdrawals.index'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-gray-800 rounded-r"></div>
                                @endif
                                <span class="text-lg w-6 text-center transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''"><i class="fas fa-arrow-up"></i></span>
                                <span class="whitespace-nowrap transition-opacity duration-300 font-medium"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">Data
                                    Withdraw</span>
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('admin.users.index') }}"
                                class="relative flex items-center py-3 hover:bg-gray-100 transition-colors duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-gray-100 text-gray-900' : 'text-gray-600' }}"
                                :class="sidebarOpen ? 'px-6' : 'px-0 justify-center'" title="App User">
                                @if(request()->routeIs('admin.users.*'))
                                <div class="absolute left-0 top-0 bottom-0 w-1 bg-gray-800 rounded-r"></div>
                                @endif
                                <span class="text-lg w-6 text-center transition-all duration-300"
                                    :class="sidebarOpen ? 'mr-3' : ''"><i class="fas fa-users"></i></span>
                                <span class="whitespace-nowrap transition-opacity duration-300 font-medium"
                                    :class="sidebarOpen ? 'opacity-100 block' : 'opacity-0 hidden w-0'">App User</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            {{-- Sidebar Footer --}}
            <div class="border-t border-gray-200 bg-white transition-all duration-300"
                :class="sidebarOpen ? 'p-4' : 'p-2'">

                <div class="flex items-center gap-3 p-2" :class="sidebarOpen ? '' : 'justify-center'">
                    <div
                        class="w-8 h-8 rounded-full bg-gray-800 text-white flex items-center justify-center font-bold text-xs shadow-sm">
                        A
                    </div>
                    <div class="overflow-hidden transition-all duration-300"
                        :class="sidebarOpen ? 'opacity-100 w-auto' : 'opacity-0 hidden w-0'">
                        <p class="text-sm font-bold text-gray-900 truncate">Administrator</p>
                        <p class="text-[10px] text-gray-500 truncate">Super Admin</p>
                    </div>
                </div>

                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button
                        class="w-full text-left text-xs text-gray-600 hover:text-gray-900 hover:bg-gray-100 px-3 py-2 rounded-lg flex items-center gap-2 transition"
                        :class="sidebarOpen ? '' : 'justify-center'" title="Keluar">
                        <i class="fas fa-sign-out-alt"></i>
                        <span x-show="sidebarOpen">Keluar Aplikasi</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="flex-1 h-full overflow-y-auto bg-gray-50 transition-all duration-300 ease-in-out"
            :class="sidebarOpen ? 'ml-64' : 'ml-20'">

            <header
                class="bg-white border-b border-gray-200 p-6 sticky top-0 z-40 flex items-center justify-between shadow-sm">
                <h2 class="text-xl font-bold text-gray-900">@yield('header', 'Admin Dashboard')</h2>
                <div class="text-xs text-gray-500 font-mono"
                    x-text="new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'short', day: 'numeric' })">
                </div>
            </header>

            <div class="p-8 pb-20">
                {{-- Notifikasi --}}
                @if(session('success'))
                <div
                    class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2 text-sm">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
                @endif

                @if(session('error'))
                <div
                    class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2 text-sm">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
                @endif

                @if(session('warning'))
                <div
                    class="bg-yellow-50 border border-yellow-200 text-yellow-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2 text-sm">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                </div>
                @endif

                @if(session('info'))
                <div
                    class="bg-blue-50 border border-blue-200 text-blue-700 px-4 py-3 rounded-lg mb-6 flex items-center gap-2 text-sm">
                    <i class="fas fa-info-circle"></i> {{ session('info') }}
                </div>
                @endif

                @yield('content')
            </div>
        </main>

    </div>

</body>

</html>