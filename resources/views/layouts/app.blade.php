<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyInvest')</title>
    @vite('resources/css/app.css')

    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Fonts: Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
    body {
        font-family: 'Inter', sans-serif;
        background-color: #fafafa;
        color: #1a1a1a;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
    }

    /* Smooth transitions */
    * {
        transition: background-color 0.2s ease, border-color 0.2s ease;
    }

    /* Elegant focus rings */
    *:focus {
        outline: 2px solid rgba(0, 0, 0, 0.1);
        outline-offset: 2px;
    }

    /* Custom subtle shadows */
    .shadow-subtle {
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
    }

    .shadow-elevated {
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }

    /* Clean scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: #a1a1a1;
    }

    [x-cloak] {
        display: none !important;
    }

    /* Typography scale */
    .text-display {
        font-size: 2.5rem;
        font-weight: 700;
        letter-spacing: -0.02em;
    }

    .text-title {
        font-size: 1.875rem;
        font-weight: 600;
        letter-spacing: -0.01em;
    }

    .text-heading {
        font-size: 1.5rem;
        font-weight: 600;
        letter-spacing: -0.01em;
    }

    .text-subheading {
        font-size: 1.125rem;
        font-weight: 500;
        letter-spacing: -0.005em;
    }

    /* Border radius scale */
    .rounded-sm {
        border-radius: 4px;
    }

    .rounded-md {
        border-radius: 8px;
    }

    .rounded-lg {
        border-radius: 12px;
    }

    .rounded-xl {
        border-radius: 16px;
    }

    /* Color palette */
    .bg-surface {
        background-color: #ffffff;
    }

    .bg-muted {
        background-color: #f5f5f5;
    }

    .border-subtle {
        border-color: #e5e5e5;
    }

    .border-medium {
        border-color: #d4d4d4;
    }

    .text-primary {
        color: #1a1a1a;
    }

    .text-secondary {
        color: #404040;
    }

    .text-tertiary {
        color: #6b6b6b;
    }

    /* Hover states */
    .hover-lift {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .hover-lift:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    /* Gradient for active state */
    .active-gradient {
        background: linear-gradient(135deg, #f5f5f5 0%, #eaeaea 100%);
        border-color: #d4d4d4;
    }

    /* Elegant divider */
    .divider {
        height: 1px;
        background: linear-gradient(to right, transparent, #e5e5e5, transparent);
    }
    </style>
</head>

<body class="min-h-screen">
    <!-- NAVBAR ELEGANT -->
    <nav class="bg-surface border-b border-subtle sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <div class="flex items-center space-x-3">
                    <div
                        class="w-10 h-10 rounded-xl bg-gradient-to-br from-gray-900 to-gray-800 flex items-center justify-center">
                        <span class="text-white font-bold text-lg">M</span>
                    </div>
                    <div>
                        <h1 class="text-lg font-semibold text-primary">MyInvest</h1>
                        <span class="text-xs text-tertiary tracking-widest">FINANCE</span>
                    </div>
                </div>

                <!-- Navigation -->
                <div class="hidden md:flex items-center space-x-1">
                    <a href="{{ route('dashboard') }}"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                              {{ request()->routeIs('dashboard') ? 'text-primary bg-muted' : 'text-secondary hover:text-primary hover:bg-muted' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('wallet.index') }}"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                              {{ request()->routeIs('wallet.*') ? 'text-primary bg-muted' : 'text-secondary hover:text-primary hover:bg-muted' }}">
                        Dompet
                    </a>
                    <a href="{{ route('portfolio.index') }}"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                              {{ request()->routeIs('portfolio.index') ? 'text-primary bg-muted' : 'text-secondary hover:text-primary hover:bg-muted' }}">
                        Portfolio
                    </a>
                    @if(Route::has('market.index'))
                    <a href="{{ route('market.index') }}"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                              {{ request()->routeIs('market.index') ? 'text-primary bg-muted' : 'text-secondary hover:text-primary hover:bg-muted' }}">
                        Pasar
                    </a>
                    @endif

                    <!-- Transaksi Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
                                       text-secondary hover:text-primary hover:bg-muted flex items-center space-x-1">
                            <span>Transaksi</span>
                            <svg class="w-4 h-4 transition-transform duration-200" :class="open ? 'rotate-180' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>

                        <div x-show="open" @click.away="open = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute left-0 mt-2 w-48 bg-surface border border-subtle rounded-lg shadow-elevated z-50 overflow-hidden">
                            <div class="py-2">
                                <a href="{{ route('topup') }}"
                                    class="block px-4 py-3 text-sm hover:bg-muted transition-colors duration-200
                                          {{ request()->routeIs('topup') ? 'active-gradient' : 'text-secondary hover:text-primary' }}">
                                    Deposit
                                </a>
                                <a href="{{ route('withdraw') }}"
                                    class="block px-4 py-3 text-sm hover:bg-muted transition-colors duration-200
                                          {{ request()->routeIs('withdraw') ? 'active-gradient' : 'text-secondary hover:text-primary' }}">
                                    Withdraw
                                </a>
                                <a href="{{ route('buy') }}"
                                    class="block px-4 py-3 text-sm hover:bg-muted transition-colors duration-200
                                          {{ request()->routeIs('buy') ? 'active-gradient' : 'text-secondary hover:text-primary' }}">
                                    Beli
                                </a>
                                <a href="{{ route('sell') }}"
                                    class="block px-4 py-3 text-sm hover:bg-muted transition-colors duration-200
                                          {{ request()->routeIs('sell') ? 'active-gradient' : 'text-secondary hover:text-primary' }}">
                                    Jual
                                </a>
                                <div class="divider my-2"></div>
                                <a href="{{ route('history') }}"
                                    class="block px-4 py-3 text-sm hover:bg-muted transition-colors duration-200
                                          {{ request()->routeIs('history') ? 'active-gradient' : 'text-secondary hover:text-primary' }}">
                                    Riwayat
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Section -->
                <div class="flex items-center space-x-4">
                    <!-- Date -->
                    <div class="hidden md:block text-sm text-tertiary">
                        <span id="currentDate"></span>
                    </div>

                    <!-- User -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open"
                            class="flex items-center space-x-2 text-sm text-secondary hover:text-primary transition-colors duration-200">
                            <div
                                class="w-8 h-8 rounded-full bg-muted border border-subtle flex items-center justify-center">
                                <span class="font-medium">{{ substr(Auth::user()->name, 0, 1) }}</span>
                            </div>
                        </button>

                        <div x-show="open" @click.away="open = false"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                            class="absolute right-0 mt-2 w-56 bg-surface border border-subtle rounded-lg shadow-elevated z-50 overflow-hidden">
                            <div class="p-4 border-b border-subtle">
                                <p class="text-sm font-medium text-primary">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-tertiary mt-1">{{ Auth::user()->email }}</p>
                            </div>
                            <div class="py-2">
                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-3 text-sm text-secondary hover:text-primary hover:bg-muted transition-colors duration-200">
                                        Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button @click="open = !open" x-data="{ open: false }" class="text-secondary hover:text-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile menu -->
            <div class="md:hidden" x-data="{ open: false }" x-show="open" @click.away="open = false">
                <div class="py-4 border-t border-subtle space-y-1">
                    <a href="{{ route('dashboard') }}"
                        class="block px-4 py-3 rounded-lg transition-colors duration-200
                              {{ request()->routeIs('dashboard') ? 'active-gradient text-primary' : 'text-secondary hover:text-primary hover:bg-muted' }}">
                        Dashboard
                    </a>
                    <a href="{{ route('wallet.index') }}"
                        class="block px-4 py-3 rounded-lg transition-colors duration-200
                              {{ request()->routeIs('wallet.*') ? 'active-gradient text-primary' : 'text-secondary hover:text-primary hover:bg-muted' }}">
                        Dompet
                    </a>
                    <a href="{{ route('portfolio.index') }}"
                        class="block px-4 py-3 rounded-lg transition-colors duration-200
                              {{ request()->routeIs('portfolio.index') ? 'active-gradient text-primary' : 'text-secondary hover:text-primary hover:bg-muted' }}">
                        Portfolio
                    </a>
                    @if(Route::has('market.index'))
                    <a href="{{ route('market.index') }}"
                        class="block px-4 py-3 rounded-lg transition-colors duration-200
                              {{ request()->routeIs('market.index') ? 'active-gradient text-primary' : 'text-secondary hover:text-primary hover:bg-muted' }}">
                        Pasar
                    </a>
                    @endif
                    <div class="divider my-2"></div>
                    <p class="px-4 py-2 text-xs font-medium text-tertiary uppercase tracking-wider">Transaksi</p>
                    <a href="{{ route('topup') }}"
                        class="block px-4 py-3 rounded-lg transition-colors duration-200
                              {{ request()->routeIs('topup') ? 'active-gradient text-primary' : 'text-secondary hover:text-primary hover:bg-muted' }}">
                        Deposit
                    </a>
                    <a href="{{ route('withdraw') }}"
                        class="block px-4 py-3 rounded-lg transition-colors duration-200
                              {{ request()->routeIs('withdraw') ? 'active-gradient text-primary' : 'text-secondary hover:text-primary hover:bg-muted' }}">
                        Withdraw
                    </a>
                    <a href="{{ route('buy') }}"
                        class="block px-4 py-3 rounded-lg transition-colors duration-200
                              {{ request()->routeIs('buy') ? 'active-gradient text-primary' : 'text-secondary hover:text-primary hover:bg-muted' }}">
                        Beli
                    </a>
                    <a href="{{ route('sell') }}"
                        class="block px-4 py-3 rounded-lg transition-colors duration-200
                              {{ request()->routeIs('sell') ? 'active-gradient text-primary' : 'text-secondary hover:text-primary hover:bg-muted' }}">
                        Jual
                    </a>
                    <a href="{{ route('history') }}"
                        class="block px-4 py-3 rounded-lg transition-colors duration-200
                              {{ request()->routeIs('history') ? 'active-gradient text-primary' : 'text-secondary hover:text-primary hover:bg-muted' }}">
                        Riwayat
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- MAIN CONTENT -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Page Header - Minimal -->
        <div class="mb-8">
            <h1 class="text-title text-primary">@yield('header', 'Dashboard')</h1>
            <div class="divider mt-4"></div>
        </div>

        <!-- Content Container -->
        <div class="bg-surface rounded-xl border border-subtle shadow-subtle overflow-hidden">
            @yield('content')
        </div>

        <!-- Minimal Footer -->
        <div class="mt-12 pt-8 border-t border-subtle">
            <div class="flex flex-col md:flex-row justify-between items-center text-sm text-tertiary">
                <span>MyInvest © 2026</span>
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

    <!-- Elegant Toast Notifications -->
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
            popup: 'shadow-elevated rounded-lg'
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
        iconColor: '#22c55e',
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