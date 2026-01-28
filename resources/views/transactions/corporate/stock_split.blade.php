@extends('layouts.app')

@section('title', 'Stock Split')
@section('header', 'Stock Split / Reverse Split')
@section('header_description', 'Pecah nominal saham (1:X) atau gabungkan saham (Reverse).')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

        <form action="{{ route('transactions.process_ca') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="action_type" value="SPLIT">

            {{-- 1. PILIH ASET --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Pilih Aset</label>
                <select name="asset_symbol"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-medium outline-none focus:ring-1 focus:ring-black">
                    @foreach($portfolios as $porto)
                    <option value="{{ $porto->asset_symbol }}">
                        {{ $porto->asset_symbol }} (Unit: {{ $porto->quantity }})
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- 2. RASIO SPLIT --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Rasio Split (Faktor Pengali)</label>
                <input type="number" step="0.0001" name="split_ratio"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-bold text-lg outline-none focus:ring-1 focus:ring-black"
                    placeholder="Contoh: 5 (1:5)" required>
                <p class="text-[10px] text-gray-400 mt-2">
                    * Masukkan <b>5</b> jika Stock Split 1:5 (Unit dikali 5).<br>
                    * Masukkan <b>0.1</b> jika Reverse Split 10:1 (Unit dibagi 10).
                </p>
            </div>

            {{-- 3. TANGGAL --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tanggal Efektif</label>
                <input type="datetime-local" name="date"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none">
            </div>

            <button type="submit"
                class="w-full bg-black text-white font-bold py-4 rounded-xl hover:bg-gray-800 transition">
                Proses Stock Split
            </button>
        </form>
    </div>
</div>
@endsection