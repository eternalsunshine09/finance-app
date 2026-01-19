@extends('layouts.admin')

@section('content')
<div class="flex justify-between items-end mb-8">
    <div>
        <h2 class="text-3xl font-black text-white">Kelola Kurs Valas Dunia</h2>
        <p class="text-slate-400 mt-1">Atur nilai tukar Rupiah terhadap mata uang asing.</p>
    </div>

    <form action="{{ route('admin.exchange-rates.sync') }}" method="POST">
        @csrf
        <button type="submit"
            class="bg-blue-600 hover:bg-blue-500 text-white px-6 py-3 rounded-2xl font-bold transition flex items-center gap-2 shadow-lg shadow-blue-500/20">
            <i class="fas fa-satellite-dish animate-pulse"></i> Sync Semua Live API
        </button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    <div class="lg:col-span-1">
        <div class="bg-slate-800 rounded-3xl p-6 border border-slate-700 shadow-xl sticky top-6">
            <h3 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                <span class="bg-blue-500/20 text-blue-400 p-2 rounded-lg"><i class="fas fa-plus"></i></span>
                Tambah / Update Kurs
            </h3>

            <form action="{{ route('admin.exchange-rates.store') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Kode Mata Uang</label>
                    <div class="relative">
                        <input type="text" name="currency_code" placeholder="USD, JPY, SGD, MYR"
                            class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white font-black text-lg focus:border-blue-500 focus:outline-none uppercase"
                            required maxlength="3">
                        <div class="absolute right-4 top-4 text-slate-500 text-xs font-bold">3 Huruf</div>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-2">Gunakan kode ISO resmi (Contoh: EUR untuk Euro).</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Rate ke Rupiah (IDR)</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-slate-400 font-bold">Rp</span>
                        <input type="number" name="rate" placeholder="0"
                            class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 pl-10 text-white font-mono text-lg focus:border-blue-500 focus:outline-none"
                            required step="0.01">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3.5 rounded-xl shadow-lg transition transform hover:-translate-y-1">
                    Simpan Kurs
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
                            <th class="px-6 py-4">Mata Uang</th>
                            <th class="px-6 py-4 text-right">Kurs (IDR)</th>
                            <th class="px-6 py-4 text-center">Update Terakhir</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700 text-sm">
                        @forelse($rates as $rate)
                        <tr class="hover:bg-slate-700/50 transition group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    {{-- Bendera Otomatis pakai API --}}
                                    <img src="https://flagcdn.com/w40/{{ strtolower(substr($rate->from_currency, 0, 2)) }}.png"
                                        class="w-10 h-auto rounded shadow-sm opacity-80 group-hover:opacity-100 transition"
                                        onerror="this.src='https://via.placeholder.com/40x25?text=?'">
                                    <div>
                                        <span
                                            class="block font-black text-white text-xl tracking-wide">{{ $rate->from_currency }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5 text-right">
                                <span class="text-slate-500 text-xs font-bold mr-1">1 {{ $rate->from_currency }}
                                    =</span>
                                <span class="font-mono text-emerald-400 font-bold text-lg">
                                    Rp {{ number_format($rate->rate, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-5 text-center text-slate-400 text-xs">
                                {{ $rate->updated_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-5 text-center">
                                <form action="{{ route('admin.exchange-rates.destroy', $rate->from_currency) }}"
                                    method="POST"
                                    onsubmit="return confirm('Hapus mata uang {{ $rate->from_currency }}?')">
                                    @csrf @method('DELETE')
                                    <button
                                        class="w-8 h-8 rounded-lg flex items-center justify-center bg-rose-500/10 text-rose-500 hover:bg-rose-500 hover:text-white transition"
                                        title="Hapus Valas">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-500">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-globe-americas text-4xl mb-3 opacity-50"></i>
                                    <p>Belum ada data valas. Tambahkan USD, JPY, dll.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection