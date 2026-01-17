<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyInvestment - Masa Depan Finansialmu</title>
    @vite('resources/css/app.css')

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;700;900&display=swap" rel="stylesheet">
    <style>
    body {
        font-family: 'Inter', sans-serif;
    }

    /* Animasi Text Berjalan (Ticker) */
    @keyframes ticker {
        0% {
            transform: translateX(100%);
        }

        100% {
            transform: translateX(-100%);
        }
    }

    .ticker-wrap {
        overflow: hidden;
        white-space: nowrap;
    }

    .ticker-move {
        display: inline-block;
        animation: ticker 20s linear infinite;
    }

    .glass {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    </style>
</head>

<body class="bg-gray-900 text-white selection:bg-yellow-500 selection:text-black">

    <div class="bg-black py-2 border-b border-gray-800 ticker-wrap">
        <div class="ticker-move text-sm font-mono">
            @foreach($assets as $asset)
            <span class="mx-6 text-gray-300">
                {{ $asset->symbol }}
                <span class="{{ $asset->type == 'crypto' ? 'text-green-400' : 'text-blue-400' }}">
                    Rp {{ number_format($asset->current_price, 0, ',', '.') }}
                </span>
                @if($asset->api_id) <span class="text-xs text-yellow-600">● LIVE</span> @endif
            </span>
            @endforeach
            <span class="mx-6 text-gray-300">IHSG <span class="text-red-500">▼ 6.800</span></span>
            <span class="mx-6 text-gray-300">USD/IDR <span class="text-green-500">▲ 15.500</span></span>
        </div>
    </div>

    <nav class="container mx-auto px-6 py-6 flex justify-between items-center relative z-20">
        <div class="text-2xl font-black tracking-tighter flex items-center gap-2">
            <span class="text-yellow-500 text-3xl">🚀</span> MyInvestment
        </div>
        <div class="hidden md:flex gap-8 text-sm font-medium text-gray-300">
            <a href="#" class="hover:text-white transition">Market</a>
            <a href="#" class="hover:text-white transition">Fitur</a>
            <a href="#" class="hover:text-white transition">Berita</a>
        </div>
        <div class="flex gap-4">
            <a href="{{ route('login') }}"
                class="px-6 py-2 rounded-full border border-gray-600 hover:border-white transition text-sm font-bold">
                Masuk
            </a>
            <a href="{{ route('register') }}"
                class="px-6 py-2 rounded-full bg-yellow-500 text-black hover:bg-yellow-400 transition text-sm font-bold shadow-lg shadow-yellow-500/20">
                Daftar
            </a>
        </div>
    </nav>

    <div class="relative overflow-hidden">
        <div
            class="absolute top-0 left-1/2 -translate-x-1/2 w-[800px] h-[500px] bg-blue-600 opacity-20 blur-[120px] rounded-full pointer-events-none">
        </div>

        <div class="container mx-auto px-6 pt-20 pb-32 text-center relative z-10">
            <span
                class="inline-block py-1 px-3 rounded-full bg-gray-800 border border-gray-700 text-xs font-bold text-blue-400 mb-6 tracking-wide">
                PLATFORM INVESTASI #1 DI INDONESIA 🇮🇩
            </span>

            <h1 class="text-5xl md:text-7xl font-black mb-6 leading-tight">
                Kelola Kekayaan.<br>
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-400 to-orange-500">
                    Tanpa Batas.
                </span>
            </h1>

            <p class="text-gray-400 text-lg md:text-xl max-w-2xl mx-auto mb-10 leading-relaxed">
                Akses pasar saham global dan kripto dalam satu aplikasi. Pantau portofolio real-time,
                aman, dan terpercaya. Mulai perjalanan finansialmu sekarang.
            </p>

            <div class="flex justify-center gap-4 flex-col md:flex-row">
                <a href="{{ route('register') }}"
                    class="px-8 py-4 bg-blue-600 hover:bg-blue-500 text-white rounded-lg font-bold text-lg transition shadow-xl shadow-blue-600/30">
                    Mulai Investasi Gratis ➡
                </a>
                <a href="#market"
                    class="px-8 py-4 glass text-white rounded-lg font-bold text-lg hover:bg-white/10 transition">
                    Lihat Pasar
                </a>
            </div>

            <div class="mt-20 grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <div class="glass p-6 rounded-2xl border-t border-gray-700">
                    <div class="text-3xl font-bold text-white mb-1">10rb+</div>
                    <div class="text-gray-400 text-sm">Investor Bergabung</div>
                </div>
                <div class="glass p-6 rounded-2xl border-t border-gray-700">
                    <div class="text-3xl font-bold text-yellow-400 mb-1">Rp 50M+</div>
                    <div class="text-gray-400 text-sm">Total Transaksi</div>
                </div>
                <div class="glass p-6 rounded-2xl border-t border-gray-700">
                    <div class="text-3xl font-bold text-green-400 mb-1">24/7</div>
                    <div class="text-gray-400 text-sm">Support Real-time</div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-gray-900 border-t border-gray-800 py-20" id="market">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold mb-10 text-center">Pasar Terhangat 🔥</h2>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div
                    class="bg-gray-800 p-6 rounded-xl hover:-translate-y-2 transition duration-300 cursor-pointer group">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-full bg-orange-500 flex items-center justify-center font-bold text-white">
                            ₿</div>
                        <span class="text-green-400 text-sm font-bold bg-green-900/30 px-2 py-1 rounded">+2.4%</span>
                    </div>
                    <h3 class="font-bold text-lg">Bitcoin</h3>
                    <p class="text-gray-400 text-sm">BTC/IDR</p>
                    <div class="mt-4 font-mono text-xl group-hover:text-yellow-400 transition">Rp 1.500.000.000</div>
                </div>

                <div
                    class="bg-gray-800 p-6 rounded-xl hover:-translate-y-2 transition duration-300 cursor-pointer group">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-full bg-gray-600 flex items-center justify-center font-bold text-white">
                            Ξ</div>
                        <span class="text-red-400 text-sm font-bold bg-red-900/30 px-2 py-1 rounded">-0.8%</span>
                    </div>
                    <h3 class="font-bold text-lg">Ethereum</h3>
                    <p class="text-gray-400 text-sm">ETH/IDR</p>
                    <div class="mt-4 font-mono text-xl group-hover:text-blue-400 transition">Rp 45.000.000</div>
                </div>

                <div
                    class="bg-gray-800 p-6 rounded-xl hover:-translate-y-2 transition duration-300 cursor-pointer group">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-full bg-green-600 flex items-center justify-center font-bold text-white">
                            G</div>
                        <span class="text-green-400 text-sm font-bold bg-green-900/30 px-2 py-1 rounded">+5.1%</span>
                    </div>
                    <h3 class="font-bold text-lg">GoTo</h3>
                    <p class="text-gray-400 text-sm">GOTO/IDR</p>
                    <div class="mt-4 font-mono text-xl group-hover:text-green-400 transition">Rp 68</div>
                </div>

                <div
                    class="bg-gray-800 p-6 rounded-xl hover:-translate-y-2 transition duration-300 cursor-pointer group">
                    <div class="flex justify-between items-start mb-4">
                        <div
                            class="w-10 h-10 rounded-full bg-blue-600 flex items-center justify-center font-bold text-white">
                            B</div>
                        <span class="text-green-400 text-sm font-bold bg-green-900/30 px-2 py-1 rounded">+0.5%</span>
                    </div>
                    <h3 class="font-bold text-lg">BBCA</h3>
                    <p class="text-gray-400 text-sm">Bank BCA</p>
                    <div class="mt-4 font-mono text-xl group-hover:text-blue-400 transition">Rp 10.200</div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-black text-gray-500 py-12 border-t border-gray-800 text-sm text-center">
        <p>&copy; 2026 MyInvestment App. All rights reserved.</p>
        <p class="mt-2">Created with 🔥 by Oja & Partner.</p>
    </footer>

</body>

</html>