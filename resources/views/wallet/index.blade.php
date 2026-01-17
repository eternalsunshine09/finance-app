@extends('layouts.app')

@section('title', 'Dompet Saya')
@section('header', '💳 Manajemen RDN & Dompet')

@section('content')

<div class="mb-8">
    <div class="flex justify-between items-center mb-4">
        <h3 class="font-bold text-gray-700">Daftar Rekening & RDN</h3>
        <button onclick="document.getElementById('addWalletModal').classList.remove('hidden')"
            class="text-sm bg-gray-800 text-white px-3 py-2 rounded-lg hover:bg-gray-700 transition">
            + Tambah Akun
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

        @foreach($wallets as $index => $w)
        @php
        $colors = [
        'from-gray-900 via-gray-800 to-black', // Hitam (Default)
        'from-blue-900 via-blue-800 to-blue-600', // Biru (BCA/Mandiri)
        'from-green-900 via-green-800 to-green-600', // Hijau (Gopay/Tokped)
        'from-purple-900 via-purple-800 to-purple-600' // Ungu (OVO/Ajaib)
        ];
        $bgClass = $colors[$index % count($colors)]; // Rotasi warna
        @endphp

        <div
            class="bg-gradient-to-br {{ $bgClass }} rounded-2xl shadow-xl p-6 text-white relative overflow-hidden h-56 flex flex-col justify-between border border-white/10 hover-scale cursor-pointer group">

            <div
                class="absolute top-0 right-0 -mr-10 -mt-10 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl group-hover:opacity-20 transition">
            </div>

            <div class="flex justify-between items-start z-10">
                <div>
                    <p class="text-xs text-white/70 tracking-widest uppercase">{{ $w->bank_name }}</p>
                    <p class="font-bold text-lg">{{ $w->account_name }}</p>
                </div>
                <img src="https://img.icons8.com/color/48/ffffff/sim-card-chip.png" class="w-8 opacity-80" alt="chip">
            </div>

            <div class="z-10">
                <p class="font-mono text-xl tracking-widest opacity-90">
                    {{ chunk_split($w->account_number ?? '0000', 4, ' ') }}
                </p>
            </div>

            <div class="flex justify-between items-end z-10">
                <div>
                    <p class="text-[10px] text-white/60 uppercase mb-1">Pemilik</p>
                    <p class="font-medium text-sm">{{ substr(Auth::user()->name, 0, 15) }}</p>
                </div>
                <div class="text-right">
                    <p class="text-[10px] text-white/60 uppercase mb-1">Saldo</p>
                    <p class="font-bold text-xl">Rp {{ number_format($w->balance, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>
        @endforeach

    </div>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <h3 class="font-bold text-gray-700 mb-4 border-b pb-2 flex items-center gap-2">
        📜 Mutasi Gabungan (Semua Dompet)
    </h3>
    <div class="overflow-y-auto max-h-[500px]">
        <table class="w-full text-left">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase sticky top-0">
                <tr>
                    <th class="p-3">Tanggal</th>
                    <th class="p-3">Akun</th>
                    <th class="p-3">Tipe</th>
                    <th class="p-3 text-right">Nominal</th>
                    <th class="p-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="text-sm divide-y divide-gray-100">
                @forelse($cashHistory as $trx)
                <tr class="hover:bg-gray-50 transition">
                    <td class="p-3">
                        <span class="font-bold text-gray-700">{{ $trx->created_at->format('d M') }}</span>
                        <span class="text-xs text-gray-400 ml-1">{{ $trx->created_at->format('H:i') }}</span>
                    </td>
                    <td class="p-3">
                        <span class="text-xs font-bold bg-gray-100 px-2 py-1 rounded text-gray-600">
                            {{ $trx->wallet->account_name ?? 'Hapus' }}
                        </span>
                    </td>
                    <td class="p-3">
                        @if($trx->type == 'TOPUP')
                        <span class="text-green-600 font-bold text-xs">TOP UP</span>
                        @else
                        <span class="text-orange-600 font-bold text-xs">TARIK</span>
                        @endif
                    </td>
                    <td
                        class="p-3 text-right font-mono font-bold {{ $trx->type == 'TOPUP' ? 'text-green-600' : 'text-orange-600' }}">
                        {{ $trx->type == 'TOPUP' ? '+' : '-' }} {{ number_format(abs($trx->amount_cash), 0, ',', '.') }}
                    </td>
                    <td class="p-3 text-center">
                        @if($trx->status == 'approved') ✅ @elseif($trx->status == 'pending') ⏳ @else ❌ @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="p-8 text-center text-gray-400">Belum ada transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="addWalletModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center">
    <div class="bg-white rounded-xl shadow-2xl p-6 w-full max-w-md mx-4">
        <h3 class="text-xl font-bold mb-4">➕ Tambah Dompet / RDN Baru</h3>

        <form action="{{ route('wallet.store') }}" method="POST">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-bold mb-1">Nama Akun</label>
                <input type="text" name="account_name" placeholder="Cth: RDN Bibit, Tabungan BCA"
                    class="w-full border rounded p-2" required>
            </div>
            <div class="mb-4">
                <label class="block text-sm font-bold mb-1">Bank / Platform</label>
                <input type="text" name="bank_name" placeholder="Cth: Bank Jago, Ajaib, Tokocrypto"
                    class="w-full border rounded p-2" required>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-bold mb-1">Nomor Rekening (Opsional)</label>
                <input type="number" name="account_number" placeholder="Cth: 1234567890"
                    class="w-full border rounded p-2">
            </div>

            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('addWalletModal').classList.add('hidden')"
                    class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</button>
                <button type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white font-bold rounded hover:bg-indigo-700">Simpan</button>
            </div>
        </form>
    </div>
</div>

@endsection