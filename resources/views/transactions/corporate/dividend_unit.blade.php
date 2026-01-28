@extends('layouts.app')

@section('title', 'Dividen Saham')
@section('header', 'Dividen Saham / Bonus')
@section('header_description', 'Terima tambahan unit saham tanpa mengurangi kas.')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

        <form action="{{ route('transactions.process_ca') }}" method="POST" class="space-y-6">
            @csrf
            {{-- Bisa DIV_UNIT atau BONUS, logicnya sama --}}
            <input type="hidden" name="action_type" value="DIV_UNIT">

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

            {{-- 2. JUMLAH DITERIMA --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Jumlah Unit Diterima</label>
                <input type="number" step="0.00000001" name="quantity_received"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-bold text-lg outline-none focus:ring-1 focus:ring-black"
                    placeholder="0" required>
                <p class="text-[10px] text-gray-400 mt-1">Unit ini akan ditambahkan ke portfolio Anda (Avg Price akan
                    turun).</p>
            </div>

            {{-- 3. TANGGAL --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tanggal Terima</label>
                <input type="datetime-local" name="date"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none">
            </div>

            <button type="submit"
                class="w-full bg-black text-white font-bold py-4 rounded-xl hover:bg-gray-800 transition">
                Simpan Dividen Saham
            </button>
        </form>
    </div>
</div>
@endsection