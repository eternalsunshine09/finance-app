@extends('layouts.app')
@section('title', 'Riwayat Transaksi')

@section('content')
<div class="min-h-screen bg-slate-50 pt-6 pb-12">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Navigasi & Filter --}}
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('wallet.index') }}"
                    class="w-10 h-10 bg-white rounded-full flex items-center justify-center text-slate-400 hover:text-indigo-600 hover:shadow-md transition">
                    ⬅
                </a>
                <h1 class="text-2xl font-black text-slate-800">📜 Riwayat Transaksi</h1>
            </div>

            <form method="GET" action="{{ route('wallet.history') }}" class="flex items-center gap-2">
                <select name="wallet_id" onchange="this.form.submit()"
                    class="bg-white border border-slate-200 text-slate-700 text-sm rounded-xl px-4 py-2.5 font-bold focus:outline-none focus:border-indigo-500 shadow-sm cursor-pointer">
                    <option value="all">Semua Dompet</option>
                    @foreach($wallets as $w)
                    <option value="{{ $w->id }}" {{ request('wallet_id') == $w->id ? 'selected' : '' }}>
                        {{ $w->account_name }} ({{ $w->bank_name }})
                    </option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- Tabel Transaksi --}}
        <div class="bg-white rounded-[2.5rem] shadow-sm border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead
                        class="bg-slate-50 border-b border-slate-100 text-slate-500 text-xs uppercase font-extrabold tracking-wider">
                        <tr>
                            <th class="px-8 py-5">Tanggal</th>
                            <th class="px-8 py-5">Dompet</th>
                            <th class="px-8 py-5">Tipe</th>
                            <th class="px-8 py-5 text-right">Nominal</th>
                            <th class="px-8 py-5 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 text-sm">
                        @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-8 py-5">
                                <span
                                    class="font-bold text-slate-700 block">{{ $trx->created_at->format('d M Y') }}</span>
                                <span
                                    class="text-xs text-slate-400 font-bold">{{ $trx->created_at->format('H:i') }}</span>
                            </td>
                            <td class="px-8 py-5">
                                <span class="font-bold text-slate-700">{{ $trx->wallet->account_name }}</span>
                                <span class="text-xs text-slate-400 block">{{ $trx->wallet->bank_name }}
                                    ({{ $trx->wallet->currency }})</span>
                            </td>
                            <td class="px-8 py-5">
                                @if($trx->type == 'TOPUP')
                                <span
                                    class="bg-emerald-50 text-emerald-700 px-3 py-1 rounded-lg text-xs font-bold border border-emerald-100">⬇
                                    MASUK</span>
                                @else
                                <span
                                    class="bg-rose-50 text-rose-700 px-3 py-1 rounded-lg text-xs font-bold border border-rose-100">⬆
                                    KELUAR</span>
                                @endif
                            </td>
                            <td
                                class="px-8 py-5 text-right font-black {{ $trx->type == 'TOPUP' ? 'text-emerald-600' : 'text-rose-500' }}">
                                {{ $trx->type == 'TOPUP' ? '+' : '-' }}
                                {{ $trx->wallet->currency == 'USD' ? '$' : 'Rp' }}
                                {{ number_format(abs($trx->amount_cash), 0, ',', '.') }}
                            </td>
                            <td class="px-8 py-5 text-center">
                                @if($trx->status == 'approved') ✅
                                @elseif($trx->status == 'pending') <span class="text-amber-500 animate-pulse">⏳</span>
                                @else ❌ @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-8 py-16 text-center text-slate-400 font-bold">
                                Tidak ada data transaksi yang ditemukan.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Links --}}
            <div class="px-8 py-6 border-t border-slate-50 bg-slate-50/30">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection