<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approval Top Up - Admin</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-800 text-white font-sans">

    <nav class="bg-gray-900 p-4 shadow-lg border-b border-gray-700 mb-8">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="font-bold text-xl text-yellow-400">👑 Admin Approval</h1>
            <a href="{{ route('admin.assets.index') }}" class="text-gray-300 hover:text-white">Kelola Aset ➡</a>
        </div>
    </nav>

    <div class="container mx-auto px-4">

        @if(session('success'))
        <div class="bg-green-600 text-white px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <div class="bg-gray-700 rounded-lg shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-600">
                <h2 class="text-xl font-bold">⏳ Menunggu Persetujuan (Pending)</h2>
            </div>

            <table class="w-full text-left">
                <thead class="bg-gray-900 text-gray-400 uppercase text-sm">
                    <tr>
                        <th class="p-4">Tanggal</th>
                        <th class="p-4">User</th>
                        <th class="p-4">Bukti</th>
                        <th class="p-4 text-right">Jumlah (IDR)</th>
                        <th class="p-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-200 divide-y divide-gray-600">
                    @forelse($transactions as $trx)
                    <tr class="hover:bg-gray-600">
                        <td class="p-4">{{ $trx->created_at->format('d M Y, H:i') }}</td>
                        <td class="p-4 font-bold">{{ $trx->user->name }}</td>

                        <td class="p-4">
                            @if($trx->payment_proof)
                            <a href="{{ asset('storage/' . $trx->payment_proof) }}" target="_blank">
                                <img src="{{ asset('storage/' . $trx->payment_proof) }}"
                                    class="h-12 w-12 object-cover rounded border border-gray-500 hover:scale-150 transition"
                                    alt="Bukti">
                            </a>
                            @else
                            <span class="text-xs text-red-400">Tidak ada bukti</span>
                            @endif
                        </td>
                        <td class="p-4 text-right text-green-400 font-mono text-lg">Rp
                            {{ number_format($trx->amount_cash, 0, ',', '.') }}</td>

                        <td class="p-4 text-center flex justify-center gap-2">
                            <form action="{{ route('admin.transactions.approve', $trx->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="bg-green-600 hover:bg-green-500 text-white font-bold py-1 px-4 rounded shadow">✅</button>
                            </form>
                            <form action="{{ route('admin.transactions.reject', $trx->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <button type="submit"
                                    class="bg-red-600 hover:bg-red-500 text-white font-bold py-1 px-4 rounded shadow">❌</button>
                            </form>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="4" class="p-8 text-center text-gray-400">Tidak ada permintaan top up baru.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</body>

</html>