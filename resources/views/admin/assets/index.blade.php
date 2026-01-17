<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Aset (Admin)</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-gray-800 font-sans leading-normal tracking-normal text-white">

    <nav class="bg-gray-900 p-4 shadow-lg border-b border-gray-700">
        <div class="container mx-auto flex justify-between items-center">
            <h1 class="font-bold text-xl text-yellow-400">👑 Admin Panel</h1>
            <a href="{{ route('dashboard') }}" class="text-gray-300 hover:text-white">Ke Dashboard User ➡</a>
        </div>
    </nav>

    <div class="container mx-auto mt-8 px-4">

        @if(session('success'))
        <div class="bg-green-600 text-white px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">

            <div class="bg-gray-700 p-6 rounded-lg shadow-lg h-fit">
                <h2 class="text-xl font-bold mb-4 border-b border-gray-600 pb-2">➕ Tambah Aset Baru</h2>
                <form action="{{ route('admin.assets.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm mb-1">Kode Simbol (Ex: GOTO)</label>
                        <input type="text" name="symbol"
                            class="w-full p-2 rounded bg-gray-600 text-white border border-gray-500 focus:border-yellow-400 focus:outline-none uppercase"
                            required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm mb-1">Nama Aset</label>
                        <input type="text" name="name"
                            class="w-full p-2 rounded bg-gray-600 text-white border border-gray-500" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm mb-1">Tipe</label>
                        <select name="type" class="w-full p-2 rounded bg-gray-600 text-white border border-gray-500">
                            <option value="stock">Saham (Stock)</option>
                            <option value="crypto">Kripto (Crypto)</option>
                        </select>
                    </div>
                    <div class="mb-6">
                        <label class="block text-sm mb-1">Harga Awal (Rp)</label>
                        <input type="number" name="current_price"
                            class="w-full p-2 rounded bg-gray-600 text-white border border-gray-500" required>
                    </div>
                    <button type="submit"
                        class="w-full bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 rounded transition">Simpan
                        Aset</button>
                </form>
            </div>

            <div class="md:col-span-2 bg-gray-700 p-6 rounded-lg shadow-lg">
                <h2 class="text-xl font-bold mb-4 border-b border-gray-600 pb-2">📜 Daftar Aset Tersedia</h2>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-800 text-gray-400 text-sm uppercase">
                                <th class="p-3">Simbol</th>
                                <th class="p-3">Nama</th>
                                <th class="p-3">Harga Saat Ini</th>
                                <th class="p-3 text-center">Update Harga</th>
                                <th class="p-3 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            @foreach($assets as $asset)
                            <tr class="border-b border-gray-600 hover:bg-gray-600 transition">
                                <td class="p-3 font-bold text-yellow-400">{{ $asset->symbol }}</td>
                                <td class="p-3">{{ $asset->name }}</td>
                                <td class="p-3">Rp {{ number_format($asset->current_price, 0, ',', '.') }}</td>

                                <td class="p-3">
                                    <form action="{{ route('admin.assets.updatePrice', $asset->id) }}" method="POST"
                                        class="flex gap-2">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="current_price" value="{{ $asset->current_price }}"
                                            class="w-24 p-1 text-black rounded text-xs">
                                        <button type="submit"
                                            class="bg-blue-600 hover:bg-blue-500 p-1 rounded text-xs px-2">💾</button>
                                    </form>
                                </td>

                                <td class="p-3 text-center">
                                    <form action="{{ route('admin.assets.destroy', $asset->id) }}" method="POST"
                                        onsubmit="return confirm('Yakin hapus aset ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-400 hover:text-red-200 font-bold">🗑
                                            Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

</body>

</html>