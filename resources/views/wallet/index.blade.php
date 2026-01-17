@extends('layouts.app')

@section('title', 'Dompet Saya')
@section('header', '💳 Dompet & Saldo')

@section('content')

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="lg:col-span-1">
        <div
            class="bg-gradient-to-br from-gray-900 via-gray-800 to-black rounded-2xl shadow-2xl p-6 text-white relative overflow-hidden h-56 flex flex-col justify-between border border-gray-700">

            <div class="absolute top-0 right-0 -mr-10 -mt-10 w-40 h-40 bg-white opacity-5 rounded-full blur-2xl"></div>
            <div
                class="absolute bottom-0 left-0 -ml-10 -mb-10 w-40 h-40 bg-yellow-500 opacity-10 rounded-full blur-2xl">
            </div>

            <div class="flex justify-between items-start z-10">
                <div>
                    <p class="text-xs text-gray-400 tracking-widest uppercase">MyInvest Platinum</p>
                    <img src="https://img.icons8.com/color/48/000000/sim-card-chip.png" class="w-10 mt-2 opacity-80"
                        alt="chip">
                </div>
                <span class="font-bold italic text-xl text-yellow-500">VISA</span>
            </div>

            <div class="z-10 mt-4">
                <p class="font-mono text-xl tracking-widest opacity-80">
                    **** **** **** {{ substr(Auth::user()->id . '12345', 0, 4) }}
                </p>
            </div>

            <div class="flex justify-between items-end z-10">
                <div>
                    <p class="text-[10px] text-gray-400 uppercase mb-1">Pemilik Kartu</p>
                    <p class="font-bold tracking-wide uppercase text-sm">{{ Auth::user()->name }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-gray-400 uppercase mb-1">Saldo Aktif</p>
                    <p class="font-bold text-xl">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-2 gap-4">
            <a href="{{ route('topup') }}"
                class="bg-green-600 hover:bg-green-500 text-white py-3 rounded-xl font-bold text-center shadow-lg transition flex flex-col items-center gap-1">
                <span class="text-xl">📥</span> Top Up
            </a>
            <a href="{{ route('withdraw') }}"
                class="bg-orange-600 hover:bg-orange-500 text-white py-3 rounded-xl font-bold text-center shadow-lg transition flex flex-col items-center gap-1">
                <span class="text-xl">💸</span> Tarik Dana
            </a>
        </div>
    </div>

    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h3 class="font-bold text-gray-700 mb-4 border-b pb-2 flex items-center gap-2">
            📜 Mutasi Rekening (Cash Flow)
        </h3>

        <div class="overflow-y-auto max-h-[500px]">
            <table class="w-full text-left">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase sticky top-0">
                    <tr>
                        <th class="p-3 rounded-l-lg">Tanggal</th>
                        <th class="p-3">Tipe</th>
                        <th class="p-3 text-right">Nominal</th>
                        <th class="p-3 text-center rounded-r-lg">Status</th>
                    </tr>
                </thead>
                <tbody class="text-sm divide-y divide-gray-100">
                    @forelse($cashHistory as $trx)
                    <tr class="hover:bg-gray-50 transition">
                        <td class="p-3">
                            <span class="font-bold text-gray-700">{{ $trx->created_at->format('d M Y') }}</span><br>
                            <span class="text-xs text-gray-400">{{ $trx->created_at->format('H:i') }}</span>
                        </td>
                        <td class="p-3">
                            @if($trx->type == 'TOPUP')
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">TOP UP</span>
                            @else
                            <span
                                class="bg-orange-100 text-orange-700 px-2 py-1 rounded text-xs font-bold">WITHDRAW</span>
                            @endif
                        </td>
                        <td
                            class="p-3 text-right font-mono font-bold {{ $trx->type == 'TOPUP' ? 'text-green-600' : 'text-orange-600' }}">
                            {{ $trx->type == 'TOPUP' ? '+' : '-' }} Rp
                            {{ number_format(abs($trx->amount_cash), 0, ',', '.') }}
                        </td>
                        <td class="p-3 text-center">
                            @if($trx->status == 'approved')
                            <span class="text-green-500 text-lg">✅</span>
                            @elseif($trx->status == 'pending')
                            <span class="text-yellow-500 text-lg" title="Menunggu Admin">⏳</span>
                            @else
                            <span class="text-red-500 text-lg" title="Ditolak">❌</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-400">Belum ada transaksi uang tunai.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection