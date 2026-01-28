@extends('layouts.app')

@section('title', 'Kurs Mata Uang')
@section('header', 'Nilai Tukar Valas')
@section('header_description', 'Update Terkini Kurs Rupiah (IDR) vs Mata Uang Asing')

@section('content')
<div class="p-6">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- 1. KALKULATOR KURS --}}
        <div class="lg:col-span-1">
            <div class="bg-black text-white rounded-2xl p-6 shadow-lg sticky top-6">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z">
                        </path>
                    </svg>
                    Kalkulator Konversi
                </h3>

                <div class="space-y-4" x-data="{ amount: 1, rate: 0, result: 0, currency: '' }">
                    {{-- Input Nominal Asing --}}
                    <div>
                        <label class="text-xs text-gray-400 uppercase font-bold">Nominal Asing</label>
                        <input type="number" x-model="amount" @input="result = amount * rate"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white font-bold text-lg focus:ring-1 focus:ring-white outline-none mt-1"
                            placeholder="1">
                    </div>

                    {{-- Pilih Mata Uang --}}
                    <div>
                        <label class="text-xs text-gray-400 uppercase font-bold">Pilih Mata Uang</label>
                        <select x-model="rate"
                            @change="currency = $event.target.options[$event.target.selectedIndex].text.split(' - ')[0]; result = amount * rate"
                            class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-3 text-white font-bold text-lg focus:ring-1 focus:ring-white outline-none mt-1 cursor-pointer">
                            <option value="0">-- Pilih --</option>
                            @foreach($rates as $rate)
                            <option value="{{ $rate->rate }}">{{ $rate->from_currency }} - Rp
                                {{ number_format($rate->rate, 0, ',', '.') }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="border-t border-gray-700 my-4"></div>

                    {{-- Hasil --}}
                    <div>
                        <label class="text-xs text-gray-400 uppercase font-bold">Estimasi Rupiah</label>
                        <div class="text-3xl font-mono font-bold text-green-400 mt-1">
                            Rp <span x-text="new Intl.NumberFormat('id-ID').format(result)">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. LIST KURS --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50">
                    <h3 class="text-lg font-bold text-gray-900">Daftar Kurs Hari Ini</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead
                            class="bg-white text-gray-500 text-xs uppercase font-bold tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Mata Uang</th>
                                <th class="px-6 py-4 text-right">Nilai Tukar (IDR)</th>
                                <th class="px-6 py-4 text-center">Update</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">
                            @forelse($rates as $rate)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        {{-- Flag --}}
                                        <div class="w-10 h-7 rounded shadow-sm overflow-hidden border border-gray-200">
                                            <img src="https://flagcdn.com/w40/{{ strtolower(substr($rate->from_currency, 0, 2)) }}.png"
                                                class="w-full h-full object-cover"
                                                onerror="this.src='https://via.placeholder.com/40x28?text=FX'">
                                        </div>
                                        <div>
                                            <div class="font-bold text-gray-900 text-base">{{ $rate->from_currency }}
                                            </div>
                                            <div class="text-xs text-gray-500">1 {{ $rate->from_currency }} ke IDR</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="font-mono font-bold text-gray-900 text-lg">
                                        Rp {{ number_format($rate->rate, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center text-xs text-gray-400">
                                    {{ $rate->updated_at->diffForHumans() }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-gray-400">
                                    Belum ada data kurs.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection