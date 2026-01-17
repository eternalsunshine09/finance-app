<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Investment Manager</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <nav class="bg-blue-800 p-4 shadow-lg">
        <div class="container mx-auto text-white font-bold flex justify-between">
            <a href="{{ route('dashboard') }}">⬅ Kembali ke Dashboard</a>
            <span>📜 Riwayat Transaksi</span>
        </div>
    </nav>

    <div class="container mx-auto mt-8 px-4">
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="font-bold text-gray-700">Aktivitas Keuangan Saya</h2>
            </div>

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6">Tanggal</th>
                        <th class="py-3 px-6">Tipe</th>
                        <th class="py-3 px-6">Aset</th>
                        <th class="py-3 px-6 text-right">Jumlah Uang</th>
                        <th class="py-3 px-6 text-right">Jumlah Unit</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse($transactions as $trx)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="py-3 px-6 whitespace-nowrap">
                            {{ $trx->created_at->format('d M Y, H:i') }}
                        </td>

                        <td class="py-3 px-6">
                            @if($trx->type == 'TOPUP')
                            <span class="bg-green-200 text-green-700 py-1 px-3 rounded-full text-xs font-bold">TOP
                                UP</span>
                            @elseif($trx->type == 'BUY')
                            <span class="bg-red-200 text-red-700 py-1 px-3 rounded-full text-xs font-bold">BELI</span>
                            @elseif($trx->type == 'SELL')
                            <span class="bg-blue-200 text-blue-700 py-1 px-3 rounded-full text-xs font-bold">JUAL</span>
                            @else
                            <span
                                class="bg-gray-200 text-gray-700 py-1 px-3 rounded-full text-xs font-bold">{{ $trx->type }}</span>
                            @endif
                        </td>

                        <td class="py-3 px-6 font-bold">
                            {{ $trx->asset_symbol ?? '-' }}
                        </td>

                        <td
                            class="py-3 px-6 text-right font-bold {{ $trx->amount_cash >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format($trx->amount_cash, 0, ',', '.') }}
                        </td>

                        <td class="py-3 px-6 text-right">
                            @if($trx->amount_asset)
                            {{ number_format(abs($trx->amount_asset), 2) }} Lembar
                            @else
                            -
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-4 text-center text-gray-400">Belum ada riwayat transaksi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-4">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>

</body>

</html>