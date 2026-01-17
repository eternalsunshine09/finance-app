<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Withdraw - Admin</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-800 text-white font-sans">

    <nav class="bg-gray-900 p-4 shadow-lg border-b border-gray-700 mb-8">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="font-bold text-xl text-yellow-400">👑 Admin Approval</h1>
            <div class="flex gap-4">
                <a href="{{ route('admin.transactions.index') }}" class="text-gray-300 hover:text-white">Cek Top Up</a>
                <a href="{{ route('admin.dashboard') }}" class="text-gray-300 hover:text-white">Dashboard ➡</a>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4">

        @if(session('success'))
        <div class="bg-green-600 text-white px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-gray-700 rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-600 flex justify-between items-center">
                <h2 class="text-xl font-bold text-orange-400">💸 Request Penarikan Dana (Withdraw)</h2>
            </div>

            <table class="w-full text-left">
                <thead class="bg-gray-900 text-gray-400 uppercase text-sm">
                    <tr>
                        <th class="p-4">Tanggal</th>
                        <th class="p-4">User</th>
                        <th class="p-4 text-right">Nominal (IDR)</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-200 divide-y divide-gray-600">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-gray-600">
                        <td class="p-4">{{ $trx->created_at->format('d M Y, H:i') }}</td>
                        <td class="p-4 font-bold">{{ $trx->user->name }} <br><span
                                class="text-xs text-gray-400">{{ $trx->user->email }}</span></td>
                        <td class="p-4 text-right text-orange-400 font-mono text-lg">Rp
                            {{ number_format(abs($trx->amount_cash), 0, ',', '.') }}</td>
                        <td class="p-4 text-center flex justify-center gap-2">
                            <form action="{{ route('admin.withdrawals.approve', $trx->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="bg-green-600 hover:bg-green-500 text-white font-bold py-1 px-4 rounded shadow"
                                    onclick="return confirm('Sudah transfer manual ke user? Klik OK untuk selesaikan.');">
                                    ✅ Selesai
                                </button>
                            </form>

                            <form action="{{ route('admin.withdrawals.reject', $trx->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-500 text-white font-bold py-1 px-4 rounded shadow"
                                    onclick="return confirm('Yakin tolak? Saldo akan dikembalikan ke user.');">
                                    ❌ Tolak & Refund
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-400">Tidak ada permintaan withdraw baru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>