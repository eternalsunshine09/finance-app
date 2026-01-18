<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Finance App</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    {{-- Font Awesome untuk Ikon --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="bg-slate-900 text-slate-200 font-sans antialiased" x-data="{ sidebarOpen: false }">

    <div class="flex h-screen overflow-hidden">

        <aside
            class="absolute z-20 flex flex-col w-64 h-screen px-4 py-8 overflow-y-auto bg-slate-800 border-r border-slate-700 transition-transform duration-300 transform md:relative md:translate-x-0"
            :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">

            <a href="#" class="flex items-center gap-3 px-2 mb-10">
                <span class="text-3xl">👑</span>
                <span class="text-2xl font-black text-white">AdminPanel</span>
            </a>

            <div class="flex flex-col justify-between flex-1">
                <nav class="space-y-2">
                    {{-- Menu Dashboard --}}
                    <a href="{{ route('admin.dashboard') }}"
                        class="flex items-center gap-3 px-4 py-3 transition-colors rounded-xl hover:bg-indigo-600 hover:text-white {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-400' }}">
                        <i class="fas fa-home w-5"></i>
                        <span class="font-bold">Dashboard</span>
                    </a>

                    {{-- Menu Master Aset --}}
                    <a href="{{ route('admin.assets.index') }}"
                        class="flex items-center gap-3 px-4 py-3 transition-colors rounded-xl hover:bg-indigo-600 hover:text-white {{ request()->routeIs('admin.assets.*') ? 'bg-indigo-600 text-white shadow-lg' : 'text-slate-400' }}">
                        <i class="fas fa-coins w-5"></i>
                        <span class="font-bold">Master Aset</span>
                    </a>

                    {{-- 🔥 MENU BARU: KELOLA VALAS --}}
                    <a href="{{ route('admin.exchange-rates.index') }}"
                        class="flex items-center gap-3 px-4 py-3 transition-colors rounded-xl hover:bg-blue-600 hover:text-white {{ request()->routeIs('admin.exchange-rates.*') ? 'bg-blue-600 text-white shadow-lg' : 'text-slate-400' }}">
                        <i class="fas fa-globe-asia w-5"></i>
                        <span class="font-bold">Kelola Valas</span>
                    </a>

                    {{-- Menu Approval Top Up --}}
                    <a href="{{ route('admin.transactions.index') }}"
                        class="flex items-center gap-3 px-4 py-3 transition-colors rounded-xl hover:bg-emerald-600 hover:text-white {{ request()->routeIs('admin.transactions.index') ? 'bg-emerald-600 text-white shadow-lg' : 'text-slate-400' }}">
                        <i class="fas fa-check-circle w-5"></i>
                        <span class="font-medium">Approval Top Up</span>
                    </a>

                    {{-- Menu Approval Withdraw --}}
                    <a href="{{ route('admin.withdrawals.index') }}"
                        class="flex items-center gap-3 px-4 py-3 transition-colors rounded-xl hover:bg-rose-600 hover:text-white {{ request()->routeIs('admin.withdrawals.index') ? 'bg-rose-600 text-white shadow-lg' : 'text-slate-400' }}">
                        <i class="fas fa-money-bill-wave w-5"></i>
                        <span class="font-medium">Approval Withdraw</span>
                    </a>
                </nav>

                <div class="border-t border-slate-700 pt-4 space-y-2">
                    <a href="{{ route('wallet.index') }}"
                        class="flex items-center gap-3 px-4 py-3 text-emerald-400 transition-colors rounded-xl hover:bg-slate-700">
                        <i class="fas fa-wallet"></i>
                        <span class="font-bold">App User</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="flex w-full items-center gap-3 px-4 py-3 text-rose-400 transition-colors rounded-xl hover:bg-slate-700">
                            <i class="fas fa-sign-out-alt"></i>
                            <span class="font-bold">Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <div class="flex flex-col flex-1 w-0 overflow-hidden">
            <header
                class="flex items-center justify-between px-6 py-4 bg-slate-800 border-b border-slate-700 md:hidden">
                <button @click="sidebarOpen = true" class="text-slate-400 focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
                <span class="font-bold text-white">Admin Panel</span>
            </header>

            <main class="flex-1 overflow-y-auto bg-slate-900 p-6 md:p-10">
                @if(session('success'))
                <div class="mb-6 bg-emerald-500/10 border border-emerald-500/50 text-emerald-400 px-4 py-3 rounded-xl flex items-center gap-3 shadow-lg"
                    x-data="{show: true}" x-show="show">
                    <i class="fas fa-check-circle"></i>
                    <span class="flex-1">{{ session('success') }}</span>
                    <button @click="show = false" class="text-emerald-400 hover:text-white">✖</button>
                </div>
                @endif

                @yield('content')
            </main>
        </div>

        <div x-show="sidebarOpen" @click="sidebarOpen = false"
            class="fixed inset-0 z-10 bg-slate-900/80 backdrop-blur-sm md:hidden" style="display: none;"></div>
    </div>
</body>

</html>