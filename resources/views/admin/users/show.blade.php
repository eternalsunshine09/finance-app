@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <a href="{{ route('admin.users.index') }}"
        class="text-slate-400 hover:text-white text-sm mb-6 inline-flex items-center gap-2 transition">
        <i class="fas fa-arrow-left"></i> Kembali ke Daftar User
    </a>

    {{-- Kartu Profil Ringkas --}}
    <div
        class="bg-gradient-to-r from-slate-800 to-slate-900 rounded-3xl p-8 border border-slate-700 shadow-2xl mb-8 flex flex-col md:flex-row justify-between items-center gap-6">
        <div class="flex items-center gap-6">
            <div
                class="w-20 h-20 rounded-full bg-indigo-500 flex items-center justify-center text-3xl font-black text-white shadow-lg shadow-indigo-500/50">
                {{ substr($user->name, 0, 1) }}
            </div>
            <div>
                <h1 class="text-3xl font-black text-white">{{ $user->name }}</h1>
                <p class="text-slate-400">{{ $user->email }}</p>
                <div
                    class="mt-2 inline-flex items-center gap-2 bg-slate-800 px-3 py-1 rounded-full border border-slate-600">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    <span class="text-xs font-bold text-slate-300">User Aktif</span>
                </div>
            </div>
        </div>
        <div class="text-right">
            <p class="text-slate-500 text-xs font-bold uppercase tracking-widest">Estimasi Total Aset</p>
            <h2 class="text-4xl font-black text-white mt-1">
                Rp {{ number_format($totalWealth, 0, ',', '.') }}
            </h2>
        </div>
    </div>

    {{-- Grid Info --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

        {{-- List Dompet --}}
        <div class="bg-slate-800 rounded-3xl p-6 border border-slate-700 shadow-xl">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-wallet text-indigo-400"></i> Dompet Tunai
            </h3>
            <div class="space-y-3">
                @foreach($user->wallets as $wallet)
                <div
                    class="flex justify-between items-center bg-slate-900/50 p-4 rounded-xl border border-slate-700/50">
                    <div>
                        <p class="font-bold text-slate-300">{{ $wallet->bank_name }}</p>
                        <p class="text-xs text-slate-500">{{ $wallet->account_name }}</p>
                    </div>
                    <div class="text-right">
                        <span class="block font-mono font-bold text-lg text-white">
                            {{ $wallet->currency == 'USD' ? '$' : 'Rp' }} {{ number_format($wallet->balance, 2) }}
                        </span>
                        <span
                            class="text-[10px] uppercase font-bold px-2 py-0.5 rounded bg-slate-700 text-slate-400">{{ $wallet->currency }}</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        {{-- List Portofolio --}}
        <div class="bg-slate-800 rounded-3xl p-6 border border-slate-700 shadow-xl">
            <h3 class="text-lg font-bold text-white mb-4 flex items-center gap-2">
                <i class="fas fa-chart-pie text-emerald-400"></i> Portofolio Investasi
            </h3>
            <div class="space-y-3">
                @forelse($user->portfolios as $porto)
                <div
                    class="flex justify-between items-center bg-slate-900/50 p-4 rounded-xl border border-slate-700/50">
                    <div>
                        <p class="font-bold text-white">{{ $porto->asset->symbol }}</p>
                        <p class="text-xs text-slate-500">{{ $porto->asset->name }}</p>
                    </div>
                    <div class="text-right">
                        <p class="font-mono font-bold text-emerald-400">
                            {{ number_format($porto->quantity, 4) }} Unit
                        </p>
                        <p class="text-xs text-slate-500">
                            Avg: {{ number_format($porto->average_price, 0) }}
                        </p>
                    </div>
                </div>
                @empty
                <div class="text-center py-8 text-slate-500 text-sm">
                    Belum memiliki aset investasi.
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection