@extends('layouts.admin')

@section('title', 'Kelola Kurs Valas')
@section('header', 'Manajemen Nilai Tukar')

@section('content')
<div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-8 gap-4">
    <div>
        <h2 class="text-xl font-bold text-gray-900">Kurs Mata Uang Asing</h2>
        <p class="text-sm text-gray-500 mt-1">Kelola nilai tukar Rupiah (IDR) terhadap mata uang global.</p>
    </div>

    <form action="{{ route('admin.exchange-rates.sync') }}" method="POST">
        @csrf
        <button type="submit"
            class="bg-black hover:bg-gray-800 text-white px-5 py-2.5 rounded-lg text-sm font-medium transition flex items-center gap-2 shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                </path>
            </svg>
            Sync Live API
        </button>
    </form>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

    {{-- FORM INPUT --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl p-6 border border-gray-200 shadow-sm sticky top-6">
            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <div class="p-2 rounded-md bg-gray-100 text-black">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </div>
                Tambah / Update Kurs
            </h3>

            <form action="{{ route('admin.exchange-rates.store') }}" method="POST" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Kode Mata
                        Uang</label>
                    <div class="relative">
                        <input type="text" name="currency_code" placeholder="USD"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 text-gray-900 font-bold text-lg focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none uppercase placeholder-gray-400 transition"
                            required maxlength="3">
                        <div
                            class="absolute right-4 top-4 text-[10px] font-bold text-gray-400 bg-white border border-gray-200 px-2 py-0.5 rounded">
                            ISO 3</div>
                    </div>
                    <p class="text-[10px] text-gray-400 mt-2 ml-1">Contoh: USD, EUR, JPY, SGD.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Rate ke
                        IDR</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-gray-400 font-bold text-sm">Rp</span>
                        <input type="number" name="rate" placeholder="0"
                            class="w-full bg-gray-50 border border-gray-200 rounded-lg px-4 py-3 pl-10 text-gray-900 font-mono text-lg focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none placeholder-gray-400 transition"
                            required step="0.01">
                    </div>
                </div>

                <button type="submit"
                    class="w-full bg-black hover:bg-gray-800 text-white font-bold py-3 rounded-lg shadow-md transition transform active:scale-95 text-sm">
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    {{-- LIST TABLE --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead
                        class="bg-gray-50 text-gray-500 text-xs uppercase font-semibold tracking-wider border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-4">Mata Uang</th>
                            <th class="px-6 py-4 text-right">Nilai Tukar (IDR)</th>
                            <th class="px-6 py-4 text-center">Update Terakhir</th>
                            <th class="px-6 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-sm">
                        @forelse($rates as $rate)
                        <tr class="hover:bg-gray-50 transition group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    {{-- Flag Icon using FlagCDN --}}
                                    <div
                                        class="w-10 h-7 overflow-hidden rounded shadow-sm border border-gray-100 bg-gray-100 flex items-center justify-center">
                                        <img src="https://flagcdn.com/w40/{{ strtolower(substr($rate->from_currency, 0, 2)) }}.png"
                                            class="w-full h-full object-cover" alt="{{ $rate->from_currency }}"
                                            onerror="this.style.display='none'">
                                    </div>
                                    <span
                                        class="block font-bold text-gray-900 text-base tracking-wide">{{ $rate->from_currency }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-xs text-gray-400 font-medium mr-1">1 {{ $rate->from_currency }}
                                    =</span>
                                <span class="font-mono text-gray-900 font-bold text-base">
                                    Rp {{ number_format($rate->rate, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center text-xs text-gray-500 font-mono">
                                {{ $rate->updated_at->format('d M H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <form action="{{ route('admin.exchange-rates.destroy', $rate->from_currency) }}"
                                    method="POST"
                                    onsubmit="return confirm('Hapus mata uang {{ $rate->from_currency }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                        class="w-8 h-8 rounded-md flex items-center justify-center text-gray-400 hover:bg-red-50 hover:text-red-600 border border-transparent hover:border-red-100 transition"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                <div class="flex flex-col items-center">
                                    <div class="p-3 bg-gray-100 rounded-full mb-3">
                                        <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                            </path>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-gray-600">Belum ada data valas.</p>
                                    <p class="text-xs mt-1">Silakan tambahkan USD, JPY, atau mata uang lainnya.</p>
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