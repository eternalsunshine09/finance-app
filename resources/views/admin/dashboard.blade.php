@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header', 'Dashboard Overview')

@section('content')
<div class="space-y-8">
    {{-- BAGIAN 1: HEADER GREETING --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="p-3 rounded-lg bg-black text-white">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z">
                    </path>
                </svg>
            </div>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Selamat Datang, Administrator</h1>
                <p class="text-sm text-gray-500 mt-1">Ringkasan statistik sistem dan aktivitas terbaru.</p>
            </div>
        </div>
    </div>

    {{-- BAGIAN 2: STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

        {{-- Kartu 1: Pending Top Up --}}
        <div
            class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 group">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Top Up Pending</span>
                    {{-- Menampilkan data dari controller --}}
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $pendingTopUp ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">Permintaan perlu diproses</p>
                </div>
                <div
                    class="p-2 rounded-md bg-gray-50 text-gray-400 group-hover:bg-black group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.transactions.index') }}"
                class="inline-flex items-center gap-1 text-xs font-semibold text-black hover:underline mt-2">
                Proses Sekarang &rarr;
            </a>
        </div>

        {{-- Kartu 2: Total User --}}
        <div
            class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 group">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Investor</span>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalUser ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">User terdaftar aktif</p>
                </div>
                <div
                    class="p-2 rounded-md bg-gray-50 text-gray-400 group-hover:bg-black group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.users.index') }}"
                class="inline-flex items-center gap-1 text-xs font-semibold text-black hover:underline mt-2">
                Kelola User &rarr;
            </a>
        </div>

        {{-- Kartu 3: Total Assets --}}
        <div
            class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300 group">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Aset Terdaftar</span>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $totalAset ?? 0 }}</p>
                    <p class="text-xs text-gray-400 mt-1">Jenis instrumen investasi</p>
                </div>
                <div
                    class="p-2 rounded-md bg-gray-50 text-gray-400 group-hover:bg-black group-hover:text-white transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('admin.assets.index') }}"
                class="inline-flex items-center gap-1 text-xs font-semibold text-black hover:underline mt-2">
                Lihat Daftar Aset &rarr;
            </a>
        </div>
    </div>

    {{-- BAGIAN 3: LIST TRANSAKSI TERAKHIR & SYSTEM INFO --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- List Top Up --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-lg font-bold text-gray-900">Aktivitas Top Up</h3>
                <a href="{{ route('admin.transactions.index') }}"
                    class="text-xs font-bold text-black border-b border-black pb-0.5 hover:opacity-70 transition">Lihat
                    Semua</a>
            </div>

            <div class="space-y-3">
                @forelse($recentTopUps as $topup)
                <div
                    class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-100 hover:border-gray-300 transition-colors">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-md bg-white border border-gray-200 flex items-center justify-center text-sm font-bold text-gray-700">
                            {{ substr($topup->user->name ?? 'U', 0, 1) }}
                        </div>
                        <div>
                            <p class="font-semibold text-sm text-gray-900">{{ $topup->user->name ?? 'User Hapus' }}</p>
                            <p class="text-xs text-gray-500">{{ $topup->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span
                            class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase 
                            {{ $topup->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                              ($topup->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                            {{ $topup->status }}
                        </span>
                        <p class="font-mono text-sm font-bold text-gray-900 mt-1">Rp
                            {{ number_format($topup->amount_cash ?? 0, 0, ',', '.') }}</p>
                    </div>
                </div>
                @empty
                <p class="text-center text-gray-400 text-sm py-4">Belum ada transaksi.</p>
                @endforelse
            </div>
        </div>

        {{-- System Status --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm h-fit">
            <h3 class="text-lg font-bold text-gray-900 mb-6">Status Sistem</h3>
            <div class="space-y-4">
                <div class="flex justify-between p-3 border-b border-gray-100"><span class="text-sm text-gray-500">Versi
                        App</span><span class="text-sm font-bold text-gray-900">v2.1.0</span></div>
                <div class="flex justify-between p-3 border-b border-gray-100"><span class="text-sm text-gray-500">Total
                        Transaksi DB</span><span class="text-sm font-bold text-gray-900">{{ $totalTransactions }}
                        Row</span></div>
                <div class="flex justify-between p-3 border-b border-gray-100"><span class="text-sm text-gray-500">Waktu
                        Server</span><span class="text-sm font-mono text-gray-900">{{ now()->format('H:i:s') }}</span>
                </div>
                <div class="flex justify-between p-3"><span class="text-sm text-gray-500">Database</span><span
                        class="text-sm font-bold text-green-600 flex items-center gap-1"><span
                            class="w-2 h-2 rounded-full bg-green-500"></span> Connected</span></div>
            </div>
        </div>
    </div>
</div>
@endsection