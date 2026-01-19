@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-black text-white">Dashboard Overview</h2>
    <p class="text-slate-400 mt-1">Ringkasan aktivitas sistem hari ini.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

    <div class="bg-slate-800 p-6 rounded-3xl border border-slate-700 shadow-lg relative overflow-hidden group">
        <div class="relative z-10">
            <h3 class="text-slate-400 text-xs font-bold uppercase tracking-wider">Pending Top Up</h3>
            <p class="text-4xl font-black text-white mt-2">{{ $pendingTopUp }}</p>
            <a href="{{ route('admin.transactions.index') }}"
                class="text-indigo-400 text-sm font-bold mt-4 inline-block hover:underline">Proses Sekarang →</a>
        </div>
        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition text-indigo-500">
            <i class="fas fa-wallet text-6xl"></i>
        </div>
    </div>

    <div class="bg-slate-800 p-6 rounded-3xl border border-slate-700 shadow-lg relative overflow-hidden group">
        <div class="relative z-10">
            <h3 class="text-slate-400 text-xs font-bold uppercase tracking-wider">Pending Withdraw</h3>
            <p class="text-4xl font-black text-white mt-2">{{ $pendingWithdraw }}</p>
            <a href="{{ route('admin.withdrawals.index') }}"
                class="text-rose-400 text-sm font-bold mt-4 inline-block hover:underline">Proses Sekarang →</a>
        </div>
        <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition text-rose-500">
            <i class="fas fa-hand-holding-usd text-6xl"></i>
        </div>
    </div>

    <div class="bg-slate-800 p-6 rounded-3xl border border-slate-700 shadow-lg relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total User</h3>
            <p class="text-4xl font-black text-white mt-2">{{ $totalUser }}</p>
        </div>
        <div class="absolute right-0 top-0 p-4 opacity-10 text-emerald-500">
            <i class="fas fa-users text-6xl"></i>
        </div>
    </div>

    <div class="bg-slate-800 p-6 rounded-3xl border border-slate-700 shadow-lg relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-slate-400 text-xs font-bold uppercase tracking-wider">Aset Terdaftar</h3>
            <p class="text-4xl font-black text-white mt-2">{{ $totalAset }}</p>
        </div>
        <div class="absolute right-0 top-0 p-4 opacity-10 text-yellow-500">
            <i class="fas fa-chart-line text-6xl"></i>
        </div>
    </div>
</div>
@endsection