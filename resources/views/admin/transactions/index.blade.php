@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-black text-white">Approval Top Up</h2>
    <p class="text-slate-400 mt-1">Verifikasi bukti transfer dari user.</p>
</div>

<div class="bg-slate-800 rounded-3xl border border-slate-700 shadow-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-900/50 text-slate-400 text-xs uppercase font-bold">
                <tr>
                    <th class="p-6">Tanggal</th>
                    <th class="p-6">User</th>
                    <th class="p-6">Bukti Transfer</th>
                    <th class="p-6 text-right">Nominal</th>
                    <th class="p-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700 text-sm">
                @forelse($transactions as $trx)
                <tr class="hover:bg-slate-700/50 transition">
                    <td class="p-6 text-slate-300">{{ $trx->created_at->format('d M Y H:i') }}</td>
                    <td class="p-6 font-bold text-white">{{ $trx->user->name }}</td>
                    <td class="p-6">
                        @if($trx->payment_proof)
                        <a href="{{ asset('storage/'.$trx->payment_proof) }}" target="_blank"
                            class="text-indigo-400 hover:underline text-xs flex items-center gap-1">
                            <i class="fas fa-image"></i> Lihat Bukti
                        </a>
                        @else
                        <span class="text-rose-500 text-xs">Tanpa Bukti</span>
                        @endif
                    </td>
                    <td class="p-6 text-right font-mono text-emerald-400 font-bold text-lg">
                        Rp {{ number_format($trx->amount_cash, 0, ',', '.') }}
                    </td>
                    <td class="p-6 text-center flex justify-center gap-2">
                        <form action="{{ route('admin.transactions.approve', $trx->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button
                                class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1 rounded-lg text-xs font-bold transition shadow">Terima</button>
                        </form>
                        <form action="{{ route('admin.transactions.reject', $trx->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button
                                class="bg-slate-700 hover:bg-rose-600 text-white px-3 py-1 rounded-lg text-xs font-bold transition">Tolak</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-slate-500">Tidak ada pending top up.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection