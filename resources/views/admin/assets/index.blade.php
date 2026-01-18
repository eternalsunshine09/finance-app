@extends('layouts.admin')

@section('content')
<div class="mb-8">
    <h2 class="text-3xl font-black text-white">Master Data Aset</h2>
    <p class="text-slate-400 mt-1">Kelola produk investasi dan nilai tukar mata uang.</p>
</div>

<div class="bg-indigo-900/40 border border-indigo-500/30 rounded-3xl p-6 mb-8 shadow-lg relative overflow-hidden">
    <div class="absolute -right-10 -top-10 w-40 h-40 bg-indigo-500/20 rounded-full blur-3xl"></div>

    <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">

        <div class="flex items-center gap-4">
            <div class="bg-indigo-600 p-3 rounded-2xl shadow-lg shadow-indigo-500/40">
                <span class="text-2xl">🇺🇸</span>
            </div>
            <div>
                <p class="text-indigo-300 text-xs font-bold uppercase tracking-widest">Kurs Aktif Saat Ini</p>
                @php
                // Ambil kurs terakhir dari DB
                $currentRate = \App\Models\ExchangeRate::where('from_currency', 'USD')->latest()->first()->rate ??
                15500;
                @endphp
                <h3 class="text-3xl font-black text-white mt-1">
                    1 USD = Rp <span class="text-emerald-400">{{ number_format($currentRate, 0, ',', '.') }}</span>
                </h3>
                <p class="text-xs text-slate-400 mt-1">Digunakan untuk seluruh transaksi user saat ini.</p>
            </div>
        </div>

        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto">

            <form action="{{ route('admin.exchange.update') }}" method="POST"
                class="flex gap-2 bg-slate-900/50 p-1.5 rounded-xl border border-slate-700">
                @csrf
                <input type="number" name="rate" placeholder="Input Manual (Rp)"
                    class="bg-transparent text-white px-3 py-2 w-40 focus:outline-none font-mono font-bold placeholder-slate-600"
                    required>
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-lg font-bold text-sm transition">
                    Set Manual
                </button>
            </form>

            <form action="{{ route('admin.exchange.syncApi') }}" method="POST">
                @csrf
                <button type="submit"
                    class="h-full bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-3 rounded-xl font-bold text-sm transition flex items-center gap-2 shadow-lg shadow-emerald-500/20">
                    <i class="fas fa-globe"></i> Ambil Data Live API
                </button>
            </form>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="lg:col-span-1">
        <div class="bg-slate-800 rounded-3xl p-6 border border-slate-700 shadow-xl sticky top-6">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <span class="bg-emerald-500/20 text-emerald-400 p-2 rounded-lg"><i class="fas fa-plus"></i></span>
                Tambah Aset
            </h3>

            <form action="{{ route('admin.assets.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Kode Simbol</label>
                    <input type="text" name="symbol" placeholder="Ex: BBCA, BTC"
                        class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:outline-none uppercase"
                        required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Nama Aset</label>
                    <input type="text" name="name" placeholder="Ex: Bank Central Asia"
                        class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:outline-none"
                        required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Kategori</label>
                    <div class="relative">
                        <select name="type"
                            class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:outline-none appearance-none cursor-pointer">
                            <option value="Stock">🏢 Saham (Stock)</option>
                            <option value="Crypto">₿ Crypto</option>
                            <option value="Currency">💱 Forex / Kurs</option>
                            <option value="Mutual Fund">📈 Reksa Dana</option>
                            <option value="Gold">🥇 Emas</option>
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">API ID (Opsional)</label>
                    <input type="text" name="api_id" placeholder="Ex: bitcoin (CoinGecko)"
                        class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:outline-none text-sm">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Harga Awal (Rp)</label>
                    <input type="number" name="current_price" placeholder="0"
                        class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:outline-none"
                        required>
                </div>
                <button type="submit"
                    class="w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3.5 rounded-xl shadow-lg transition transform hover:-translate-y-1">
                    Simpan Data
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-slate-800 rounded-3xl border border-slate-700 shadow-xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-900/50 text-slate-400 text-xs uppercase font-bold tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Aset</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4 text-right">Harga (IDR)</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700 text-sm">
                        @forelse($assets as $asset)
                        <tr class="hover:bg-slate-700/50 transition">
                            <td class="px-6 py-4">
                                <span class="block font-black text-white text-lg">{{ $asset->symbol }}</span>
                                <span class="text-slate-400">{{ $asset->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                $colors = [
                                'Stock' => 'text-blue-400 bg-blue-400/10 border-blue-400/20',
                                'Crypto' => 'text-orange-400 bg-orange-400/10 border-orange-400/20',
                                'Currency' => 'text-emerald-400 bg-emerald-400/10 border-emerald-400/20',
                                'Gold' => 'text-yellow-400 bg-yellow-400/10 border-yellow-400/20',
                                'Mutual Fund' => 'text-purple-400 bg-purple-400/10 border-purple-400/20',
                                ];
                                $cls = $colors[$asset->type] ?? 'text-slate-400 bg-slate-400/10 border-slate-400/20';
                                @endphp
                                <span
                                    class="px-3 py-1 rounded-full text-xs font-bold border {{ $cls }}">{{ $asset->type }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    @if($asset->type == 'Crypto')
                                    {{-- Logo Dollar & Harga USD --}}
                                    <span class="text-emerald-400 font-bold">$</span>
                                    <span
                                        class="font-mono text-white">{{ number_format($asset->current_price, 2, '.', ',') }}</span>
                                    @else
                                    {{-- Logo Rupiah & Harga IDR --}}
                                    <span class="text-slate-400 font-bold">Rp</span>
                                    <span
                                        class="font-mono text-white">{{ number_format($asset->current_price, 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.assets.destroy', $asset->id) }}" method="POST"
                                    onsubmit="return confirm('Hapus aset ini?')">
                                    @csrf @method('DELETE')
                                    <button class="text-slate-500 hover:text-rose-500 transition"><i
                                            class="fas fa-trash-alt"></i></button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">Belum ada data aset.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection