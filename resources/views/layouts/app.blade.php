<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyInvestment')</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">

    <style>
    body {
        font-family: 'Inter', sans-serif;
    }

    /* 1. Efek Hover Halus pada Kartu */
    .hover-scale {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }

    .hover-scale:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
    }

    /* 2. Scrollbar Cantik */
    ::-webkit-scrollbar {
        width: 8px;
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
    </style>
</head>

<body class="bg-gray-100 text-gray-800">

    <div class="flex h-screen overflow-hidden">

        <aside class="w-64 bg-gray-900 text-white flex flex-col fixed h-full transition-all duration-300 z-50">
            <div class="h-16 flex items-center justify-center border-b border-gray-800">
                <h1 class="text-2xl font-bold tracking-wider text-yellow-500">🚀 MyInvest</h1>
            </div>

            <nav class="flex-1 overflow-y-auto py-4">
                <ul class="space-y-1">
                    <li>
                        <a href="{{ route('dashboard') }}"
                            class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('dashboard') ? 'bg-gray-800 border-l-4 border-yellow-500' : '' }}">
                            <span class="text-xl mr-3">🏠</span> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('wallet.index') }}"
                            class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('wallet.index') ? 'bg-gray-800 border-l-4 border-yellow-500' : '' }}">
                            <span class="text-xl mr-3">💰</span> Wallet (Dompet)
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('portfolio.index') }}"
                            class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('portfolio.index') ? 'bg-gray-800 border-l-4 border-yellow-500' : '' }}">
                            <span class="text-xl mr-3">📊</span> Portofolio Detail
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('market.index') }}"
                            class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('market.index') ? 'bg-gray-800 border-l-4 border-yellow-500' : '' }}">
                            <span class="text-xl mr-3">💱</span> Exchange Rate
                        </a>
                    </li>

                    <li class="px-6 pt-4 pb-2 text-xs font-bold text-gray-500 uppercase">Transaksi</li>
                    <li>
                        <a href="{{ route('topup') }}"
                            class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('topup') ? 'bg-gray-800 border-l-4 border-green-500' : '' }}">
                            <span class="text-xl mr-3">📥</span> Top Up
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('withdraw') }}"
                            class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('withdraw') ? 'bg-gray-800 border-l-4 border-red-500' : '' }}">
                            <span class="text-xl mr-3">💸</span> Withdraw
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('buy') }}"
                            class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('buy') ? 'bg-gray-800 border-l-4 border-indigo-500' : '' }}">
                            <span class="text-xl mr-3">🛒</span> Beli Aset
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('history') }}"
                            class="flex items-center px-6 py-3 hover:bg-gray-800 {{ request()->routeIs('history') ? 'bg-gray-800 border-l-4 border-blue-500' : '' }}">
                            <span class="text-xl mr-3">📜</span> Riwayat
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="p-4 border-t border-gray-800">
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-3 hover:bg-gray-800 p-2 rounded transition">
                    @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" class="w-8 h-8 rounded-full object-cover">
                    @else
                    <div
                        class="w-8 h-8 rounded-full bg-yellow-500 text-black flex items-center justify-center font-bold text-xs">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    @endif
                    <div>
                        <p class="text-sm font-bold text-white">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-gray-500">Edit Profil</p>
                    </div>
                </a>
                <form action="{{ route('logout') }}" method="POST" class="mt-2">
                    @csrf
                    <button class="w-full text-left text-xs text-red-400 hover:text-red-300 px-2 py-1">
                        🚪 Keluar Aplikasi
                    </button>
                </form>
            </div>
        </aside>

        <main class="flex-1 ml-64 h-full overflow-y-auto bg-gray-100">
            <header class="bg-white shadow-sm p-6 sticky top-0 z-40">
                <h2 class="text-2xl font-bold text-gray-800">@yield('header', 'Dashboard')</h2>
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
    // Menggunakan count() agar formatter tidak memisahkan tanda panah
    @if(count($errors) > 0)
    Swal.fire({
        icon: 'warning',
        title: 'Periksa Inputan',
        // Hati-hati di baris bawah ini, pastikan tanda panah pada $errors->all() tetap nyambung
        html: '<ul class="text-left text-sm">@foreach($errors->all() as $error)<li>• {{ $error }}</li>@endforeach</ul>',
    });
    @endif

    // 4. Loading State (Agar tombol loading saat diklik)
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function() {
                const btn = this.querySelector('button[type="submit"]');
                if (btn) {
                    btn.disabled = true;
                    const originalText = btn.innerText;
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