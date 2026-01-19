@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-black text-white">Data Pengguna Aplikasi</h2>
    <p class="text-slate-400 mt-1">Daftar nasabah terdaftar beserta ringkasan saldonya.</p>
</div>

<div class="bg-slate-800 rounded-3xl border border-slate-700 shadow-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="bg-slate-900/50 text-slate-400 text-xs uppercase font-bold">
                <tr>
                    <th class="px-6 py-4">Nama User</th>
                    <th class="px-6 py-4">Email</th>
                    <th class="px-6 py-4">Saldo (IDR)</th>
                    <th class="px-6 py-4">Saldo (USD)</th>
                    <th class="px-6 py-4 text-center">Bergabung</th>
                    <th class="px-6 py-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-700 text-sm text-slate-300">
                @forelse($users as $user)
                @php
                $walletIDR = $user->wallets->where('currency', 'IDR')->first();
                $walletUSD = $user->wallets->where('currency', 'USD')->first();
                @endphp
                <tr class="hover:bg-slate-700/50 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-xs border border-blue-500/30">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <span class="font-bold text-white">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">{{ $user->email }}</td>
                    <td class="px-6 py-4 font-mono text-slate-400">
                        Rp {{ number_format($walletIDR->balance ?? 0, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 font-mono text-emerald-400">
                        $ {{ number_format($walletUSD->balance ?? 0, 2, '.', ',') }}
                    </td>
                    <td class="px-6 py-4 text-center text-xs">
                        {{ $user->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <a href="{{ route('admin.users.show', $user->id) }}"
                            class="text-indigo-400 hover:text-indigo-300 text-xs font-bold border border-indigo-500/30 px-3 py-1 rounded-lg hover:bg-indigo-500/10 transition">
                            Lihat Detail
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-slate-500">Belum ada user terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4">
        {{ $users->links() }}
    </div>
</div>
@endsection