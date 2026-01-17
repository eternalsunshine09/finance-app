<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investment Manager</title>
    @vite('resources/css/app.css') {{-- Ini pemanggil Tailwind --}}
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <nav class="bg-blue-800 p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center text-white">
            <h1 class="font-bold text-xl">🚀 MyInvestment</h1>
            <div>Halo, {{ $user }}</div>
        </div>
    </nav>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Berhasil!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
    @endif

    <div class="container mx-auto mt-8 px-4">

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-green-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Uang Tunai (IDR)</h3>
                <p class="text-2xl font-bold text-gray-800 mt-2">
                    Rp {{ number_format($rekap['uang_tunai'], 0, ',', '.') }}
                </p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-blue-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Nilai Aset Pasar</h3>
                <p class="text-2xl font-bold text-gray-800 mt-2">
                    Rp {{ number_format($rekap['nilai_investasi'], 0, ',', '.') }}
                </p>
            </div>

            <div class="bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                <h3 class="text-gray-500 text-sm font-bold uppercase">Total Kekayaan Bersih</h3>
                <p class="text-2xl font-bold text-gray-800 mt-2">
                    Rp {{ number_format($rekap['total_kekayaan'], 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="bg-gray-50 px-6 py-4 border-b">
                <h2 class="font-bold text-gray-700">📊 Portofolio Aset Saya</h2>
            </div>

            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 text-gray-600 uppercase text-sm leading-normal">
                        <th class="py-3 px-6">Aset</th>
                        <th class="py-3 px-6 text-right">Jumlah</th>
                        <th class="py-3 px-6 text-right">Modal Awal</th>
                        <th class="py-3 px-6 text-right">Nilai Sekarang</th>
                        <th class="py-3 px-6 text-center">Profit/Loss</th>
                        <th class="py-3 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-gray-600 text-sm font-light">
                    @forelse($detail_aset as $item)
                    <tr class="border-b border-gray-200 hover:bg-gray-50">
                        <td class="py-3 px-6 font-bold">{{ $item['aset'] }}</td>
                        <td class="py-3 px-6 text-right">{{ number_format($item['jumlah'], 2) }}</td>
                        <td class="py-3 px-6 text-right">Rp {{ number_format($item['modal'], 0, ',', '.') }}</td>
                        <td class="py-3 px-6 text-right font-medium">Rp
                            {{ number_format($item['nilai_sekarang'], 0, ',', '.') }}</td>

                        <td class="py-3 px-6 text-center">
                            @if($item['cuan'] >= 0)
                            <span class="bg-green-200 text-green-700 py-1 px-3 rounded-full text-xs font-bold">
                                +Rp {{ number_format($item['cuan'], 0, ',', '.') }}
                            </span>
                            @else
                            <span class="bg-red-200 text-red-700 py-1 px-3 rounded-full text-xs font-bold">
                                -Rp {{ number_format(abs($item['cuan']), 0, ',', '.') }}
                            </span>
                            @endif
                        </td>

                        <td class="py-3 px-6 text-center">
                            <button
                                class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600">Jual</button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-4 text-center text-gray-400">Belum ada aset investasi.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-8 flex gap-4">
            <a href="{{ route('topup') }}"
                class="bg-green-600 text-white font-bold py-2 px-6 rounded hover:bg-green-700 transition inline-block">
                + Top Up Saldo
            </a>

            <button
                class="bg-indigo-600 text-white font-bold py-2 px-6 rounded hover:bg-indigo-700 transition opacity-50 cursor-not-allowed">
                + Beli Aset Baru (Segera)
            </button>
        </div>

    </div>
</body>

</html>