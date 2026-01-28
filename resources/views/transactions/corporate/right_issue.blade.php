@extends('layouts.app')

@section('title', 'Right Issue')
@section('header', 'Tebus Right Issue (HMETD)')
@section('header_description', 'Beli saham tambahan dengan harga khusus (Exercise Price).')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">

        <form action="{{ route('transactions.process_ca') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="action_type" value="RIGHT_ISSUE">

            {{-- 1. SUMBER DANA --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Bayar Pakai</label>
                <select name="wallet_id"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-medium outline-none">
                    @foreach($wallets as $wallet)
                    <option value="{{ $wallet->id }}">{{ $wallet->bank_name }} (Rp
                        {{ number_format($wallet->balance) }})</option>
                    @endforeach
                </select>
            </div>

            {{-- 2. PILIH ASET --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Aset Induk</label>
                <select name="asset_symbol"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-medium outline-none">
                    @foreach($portfolios as $porto)
                    <option value="{{ $porto->asset_symbol }}">{{ $porto->asset_symbol }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- 3. JUMLAH --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Jumlah Lembar Ditebus</label>
                    <input type="number" step="1" name="quantity"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-bold outline-none"
                        required placeholder="0">
                </div>

                {{-- 4. HARGA TEBUS --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Harga Tebus (Exercise
                        Price)</label>
                    <input type="number" step="any" name="exercise_price"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-bold outline-none"
                        required placeholder="Ex: 500">
                </div>
            </div>

            {{-- 5. TANGGAL --}}
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Tanggal Penebusan</label>
                <input type="datetime-local" name="date"
                    class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 outline-none">
            </div>

            <button type="submit"
                class="w-full bg-black text-white font-bold py-4 rounded-xl hover:bg-gray-800 transition">
                Proses Right Issue
            </button>
        </form>
    </div>
</div>
@endsection