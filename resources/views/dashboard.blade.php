<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Investment Manager</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body class="bg-gray-100 font-sans leading-normal tracking-normal">

    <nav class="bg-blue-800 p-4 shadow-lg">
        <div class="container mx-auto flex justify-between items-center text-white">
            <h1 class="font-bold text-xl">🚀 MyInvestment</h1>

            <div class="flex items-center gap-4">
                <a href="{{ route('profile.edit') }}"
                    class="flex items-center gap-2 hover:bg-blue-700 p-2 rounded transition">
                    @if(Auth::user()->avatar)
                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}"
                        class="w-8 h-8 rounded-full object-cover border border-white">
                    @else
                    <div
                        class="w-8 h-8 rounded-full bg-white text-blue-800 flex items-center justify-center font-bold text-xs">
                        {{ substr(Auth::user()->name, 0, 1) }}
                    </div>
                    @endif
                    <span class="font-bold">{{ Auth::user()->name }}</span>
                </a>

                @if(Auth::user()->role == 'admin')
                <a href="{{ route('admin.transactions.index') }}"
                    class="text-sm bg-yellow-500 text-black font-bold px-3 py-1 rounded hover:bg-yellow-400 transition shadow-lg border border-yellow-600">
                    👑 Admin Panel
                </a>
                @endif

                <a href="{{ route('history') }}"
                    class="text-sm bg-blue-700 px-3 py-1 rounded hover:bg-blue-600 transition">
                    📜 History
                </a>

                <form action="{{ route('logout') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-sm bg-red-600 px-3 py-1 rounded hover:bg-red-700 transition">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container mx-auto mt-8 px-4">

        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
            <strong class="font-bold">Berhasil!</strong>
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">

            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-2 gap-6">
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

                <div class="md:col-span-2 bg-white rounded-lg shadow p-6 border-l-4 border-purple-500">
                    <h3 class="text-gray-500 text-sm font-bold uppercase">Total Kekayaan Bersih</h3>
                    <p class="text-3xl font-bold text-gray-800 mt-2">
                        Rp {{ number_format($rekap['total_kekayaan'], 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-4 flex flex-col items-center justify-center">
                <h3 class="text-gray-500 text-sm font-bold uppercase mb-4">Alokasi Aset</h3>
                <div class="w-full h-48"> <canvas id="myChart"></canvas>
                </div>
            </div>

        </div>

        <div class="bg-white shadow-md rounded-lg overflow-hidden mb-8">
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
                        <td class="py-3 px-6">
                            <span class="font-bold">{{ $item['aset'] }}</span><br>
                            <span class="text-xs text-gray-400">{{ $item['nama_lengkap'] ?? '' }}</span>
                        </td>
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
                            <a href="{{ route('sell', ['symbol' => $item['aset']]) }}"
                                class="bg-blue-500 text-white px-3 py-1 rounded text-xs hover:bg-blue-600 transition inline-block">
                                Jual
                            </a>
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

        <div class="flex gap-4 mb-8">
            <a href="{{ route('topup') }}"
                class="bg-green-600 text-white font-bold py-2 px-6 rounded hover:bg-green-700 transition inline-block">
                + Top Up Saldo
            </a>

            <a href="{{ route('withdraw') }}"
                class="bg-orange-500 text-white font-bold py-2 px-6 rounded hover:bg-orange-600 transition inline-block">
                💸 Tarik Dana
            </a>
            <a href="{{ route('buy') }}"
                class="bg-indigo-600 text-white font-bold py-2 px-6 rounded hover:bg-indigo-700 transition inline-block">
                + Beli Aset Baru
            </a>
        </div>

    </div>

    <script>
    const ctx = document.getElementById('myChart');

    // Mengambil data yang dikirim dari Controller
    const labels = @json($chartLabels ?? []);
    const data = @json($chartValues ?? []);

    // Hanya render jika ada data
    if (labels.length > 0) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nilai Aset (Rp)',
                    data: data,
                    backgroundColor: [
                        '#10B981', // Hijau (Cash)
                        '#3B82F6', // Biru
                        '#F59E0B', // Kuning
                        '#EF4444', // Merah
                        '#8B5CF6', // Ungu
                        '#EC4899' // Pink
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });
    }
    </script>
</body>

</html>