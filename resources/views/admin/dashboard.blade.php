@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header', 'Dashboard Admin')

@section('content')
<div class="space-y-8">
    {{-- SECTION 1: HEADER GREETING --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="p-3 rounded-lg bg-gray-100">
                <span class="text-2xl text-gray-700">👑</span>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Selamat Datang, Administrator</h1>
                <p class="text-gray-600 mt-1">Ringkasan statistik sistem dan aktivitas terbaru</p>
            </div>
        </div>
    </div>

    {{-- SECTION 2: STATS CARDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Pending Approvals --}}
        <div
            class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <div class="p-2 rounded-lg bg-yellow-50">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-500 uppercase">Menunggu Approval</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $pendingTopUp }}</p>
                    <p class="text-sm text-gray-500 mt-1">Request</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>
            </div>
            <a href="{{ route('admin.transactions.index') }}"
                class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                Lihat Request
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        {{-- Total Users --}}
        <div
            class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <div class="p-2 rounded-lg bg-blue-50">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-500 uppercase">Total Investor</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalUser }}</p>
                    <p class="text-sm text-gray-500 mt-1">Orang</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Active</span>
            </div>
            <a href="{{ route('admin.users.index') ?? '#' }}"
                class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                Kelola User
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>

        {{-- Total Assets --}}
        <div
            class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-300">
            <div class="flex items-start justify-between mb-4">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <div class="p-2 rounded-lg bg-green-50">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                                </path>
                            </svg>
                        </div>
                        <span class="text-xs font-medium text-gray-500 uppercase">Aset Terdaftar</span>
                    </div>
                    <p class="text-3xl font-bold text-gray-900">{{ $totalAset }}</p>
                    <p class="text-sm text-gray-500 mt-1">Jenis</p>
                </div>
                <span class="px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">Listed</span>
            </div>
            <a href="{{ route('admin.assets.index') }}"
                class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-gray-700 hover:text-gray-900 transition-colors">
                Kelola Aset
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"></path>
                </svg>
            </a>
        </div>
    </div>

    {{-- SECTION 3: QUICK ACTIONS --}}
    <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900">Aksi Cepat</h3>
            <span class="text-sm text-gray-500">Akses cepat ke fitur utama</span>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('admin.assets.index') }}"
                class="group p-5 border border-gray-200 rounded-xl hover:border-gray-300 hover:bg-gray-50 transition-all duration-200">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-lg bg-gray-100 text-gray-700 group-hover:bg-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                            </path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 group-hover:text-gray-700 transition-colors">Manajemen Aset
                        </h4>
                        <p class="text-sm text-gray-500 mt-1">Tambah/Edit/Hapus aset investasi</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <a href="{{ route('admin.transactions.index') }}"
                class="group p-5 border border-gray-200 rounded-xl hover:border-gray-300 hover:bg-gray-50 transition-all duration-200">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-lg bg-gray-100 text-gray-700 group-hover:bg-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 group-hover:text-gray-700 transition-colors">Approval Top
                            Up</h4>
                        <p class="text-sm text-gray-500 mt-1">Verifikasi deposit investor</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>

            <a href="{{ route('admin.users.index') ?? '#' }}"
                class="group p-5 border border-gray-200 rounded-xl hover:border-gray-300 hover:bg-gray-50 transition-all duration-200">
                <div class="flex items-center gap-4">
                    <div class="p-3 rounded-lg bg-gray-100 text-gray-700 group-hover:bg-gray-200 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5 0a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-medium text-gray-900 group-hover:text-gray-700 transition-colors">Kelola User
                        </h4>
                        <p class="text-sm text-gray-500 mt-1">Lihat & kelola data investor</p>
                    </div>
                    <svg class="w-5 h-5 text-gray-400 group-hover:text-gray-600 transition-colors" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5l7 7-7 7"></path>
                    </svg>
                </div>
            </a>
        </div>
    </div>

    {{-- SECTION 4: RECENT ACTIVITY --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Recent Top Up Requests --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Top Up Terbaru</h3>
                    <p class="text-sm text-gray-500 mt-1">Request deposit terbaru menunggu approval</p>
                </div>
                <a href="{{ route('admin.transactions.index') }}"
                    class="text-sm font-medium text-gray-700 hover:text-gray-900">
                    Lihat semua
                </a>
            </div>

            <div class="space-y-4">
                @if(isset($recentTopUps) && count($recentTopUps) > 0)
                @foreach($recentTopUps->take(5) as $topup)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                            <span class="text-sm font-medium text-gray-700">
                                {{ substr($topup->user->name ?? 'U', 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $topup->user->name ?? 'User' }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $topup->created_at->format('d M Y, H:i') ?? 'Just now' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="text-xs px-3 py-1 rounded-full 
                                {{ $topup->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($topup->status == 'approved' ? 'bg-green-100 text-green-800' : 
                                   'bg-red-100 text-red-800') }}">
                            {{ ucfirst($topup->status ?? 'pending') }}
                        </span>
                        <p class="font-bold text-gray-900 mt-2">
                            Rp {{ number_format($topup->amount ?? 0, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                        </path>
                    </svg>
                    <p class="font-medium text-gray-600">Tidak ada request top up terbaru</p>
                    <p class="text-sm text-gray-500 mt-1">Semua request telah diproses</p>
                </div>
                @endif
            </div>
        </div>

        {{-- System Info --}}
        <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Info Sistem</h3>

            <div class="space-y-5">
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-gray-200">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Versi Aplikasi</p>
                            <p class="text-sm text-gray-500">Release terbaru</p>
                        </div>
                    </div>
                    <span class="font-bold text-gray-900">v2.1.0</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-gray-200">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Total Transaksi</p>
                            <p class="text-sm text-gray-500">Semua transaksi</p>
                        </div>
                    </div>
                    <span class="font-bold text-gray-900">{{ $totalTransactions ?? '0' }}</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-gray-200">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Waktu Server</p>
                            <p class="text-sm text-gray-500">Waktu terkini</p>
                        </div>
                    </div>
                    <span class="font-bold text-gray-900">{{ now()->format('H:i') }}</span>
                </div>

                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center gap-3">
                        <div class="p-2 rounded-lg bg-gray-200">
                            <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Status Sistem</p>
                            <p class="text-sm text-gray-500">Ketersediaan</p>
                        </div>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">Online</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection