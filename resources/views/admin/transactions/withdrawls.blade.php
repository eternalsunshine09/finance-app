@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-black text-white">Approval Withdraw</h2>
    <p class="text-slate-400 mt-1">Permintaan penarikan dana user.</p>
</div>

<div class="bg-slate-800 rounded-3xl border border-slate-700 shadow-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-slate-900/50 text-slate-400 text-xs uppercase font-bold">
                <tr>
                    <th class="p-6">Tanggal</th>
                    <th class="p-6">User</th>
                    <th class="p-6 text-right">Nominal Withdraw</th>
                    <th class="p-6 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700 text-sm">
                @forelse($transactions as $trx)
                <tr class="hover:bg-slate-700/50 transition">
                    <td class="p-6 text-slate-300">{{ $trx->created_at->format('d M Y H:i') }}</td>
                    <td class="p-6">
                        <span class="block font-bold text-white">{{ $trx->user->name }}</span>
                        <span class="text-xs text-slate-500">{{ $trx->user->email }}</span>
                    </td>
                    <td class="p-6 text-right font-mono text-rose-400 font-bold text-lg">
                        Rp {{ number_format(abs($trx->amount_cash), 0, ',', '.') }}
                    </td>
                    <td class="p-6 text-center flex justify-center gap-2">
                        <form action="{{ route('admin.withdrawals.approve', $trx->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button
                                class="bg-emerald-500 hover:bg-emerald-600 text-white px-3 py-1 rounded-lg text-xs font-bold transition shadow"
                                onclick="return confirm('Sudah transfer manual ke rekening user?')">
                                Selesai Transfer
                            </button>
                        </form>
                        <form action="{{ route('admin.withdrawals.reject', $trx->id) }}" method="POST">
                            @csrf @method('PATCH')
                            <button
                                class="bg-slate-700 hover:bg-rose-600 text-white px-3 py-1 rounded-lg text-xs font-bold transition"
                                onclick="return confirm('Tolak dan kembalikan saldo ke user?')">
                                Tolak
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="p-8 text-center text-slate-500">Tidak ada pending withdraw.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection