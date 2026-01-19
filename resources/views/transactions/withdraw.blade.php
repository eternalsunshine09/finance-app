@extends('layouts.app')

@section('title', 'Tarik Dana')

@section('content')
<div class="min-h-screen bg-slate-50 py-12">
    <div class="max-w-xl mx-auto px-4">

        {{-- Header --}}
        <div class="mb-8 text-center">
            <div
                class="w-16 h-16 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl shadow-sm">
                💸
            </div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">Tarik Dana</h1>
            <p class="text-slate-500 mt-2">Saldo akan dikunci hingga disetujui Admin.</p>
        </div>

        {{-- Error Alert --}}
        @if ($errors->any())
        <div class="bg-rose-50 border-l-4 border-rose-500 text-rose-700 p-4 mb-6 rounded-r shadow-sm">
            <p class="font-bold">Gagal:</p>
            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        {{-- CARD FORM --}}
        <div class="bg-white p-8 rounded-3xl shadow-xl border border-slate-100 relative overflow-hidden">

            {{-- Hiasan Background --}}
            <div class="absolute top-0 right-0 w-32 h-32 bg-orange-50 rounded-bl-full -mr-10 -mt-10 z-0"></div>

            <form action="{{ route('withdraw.process') }}" method="POST" class="space-y-6 relative z-10">
                @csrf
                <input type="hidden" name="user_id" value="{{ Auth::id() }}">

                {{-- 1. PILIH MATA UANG (Agar Controller tahu ambil dari dompet mana) --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wider">Mata
                        Uang</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="currency" value="IDR" class="peer sr-only" checked>
                            <div
                                class="p-4 rounded-xl border-2 border-slate-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 transition text-center">
                                <span class="block font-black text-slate-700 peer-checked:text-orange-600">IDR
                                    (Rupiah)</span>
                            </div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="currency" value="USD" class="peer sr-only">
                            <div
                                class="p-4 rounded-xl border-2 border-slate-200 peer-checked:border-orange-500 peer-checked:bg-orange-50 transition text-center">
                                <span class="block font-black text-slate-700 peer-checked:text-orange-600">USD
                                    (Dollar)</span>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- 2. NOMINAL --}}
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-2 uppercase tracking-wider">Nominal
                        Penarikan</label>
                    <div class="relative">
                        <input type="number" name="amount" min="10000"
                            class="w-full px-4 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-orange-500 focus:border-orange-500 font-bold text-slate-800 text-lg transition placeholder-slate-400"
                            placeholder="Contoh: 100000" required>
                    </div>
                    <p class="text-xs text-slate-400 mt-2 ml-1">
                        <i class="fas fa-info-circle"></i> Saldo akan langsung dipotong saat request diajukan.
                    </p>
                </div>

                {{-- 3. TANGGAL TRANSAKSI (BARU) --}}
                <div class="pt-2">
                    <label
                        class="flex items-center gap-2 text-slate-400 text-xs font-bold cursor-pointer w-fit hover:text-orange-500 transition"
                        onclick="document.getElementById('wdDateContainer').classList.toggle('hidden')">
                        <i class="fas fa-calendar-alt"></i> Atur Tanggal Request (Backdate)
                    </label>
                    <div id="wdDateContainer" class="hidden mt-2">
                        <input type="datetime-local" name="custom_date"
                            class="w-full bg-slate-50 border border-slate-200 text-slate-600 text-sm rounded-xl p-3 focus:ring-orange-500 focus:border-orange-500">
                    </div>
                </div>

                <hr class="border-slate-100">

                {{-- TOMBOL SUBMIT --}}
                <div class="flex gap-4">
                    <a href="{{ route('dashboard') }}"
                        class="w-1/3 py-4 rounded-xl text-slate-500 font-bold text-center bg-slate-100 hover:bg-slate-200 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="w-2/3 bg-orange-600 hover:bg-orange-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-orange-500/30 transition transform active:scale-95 flex justify-center items-center gap-2">
                        <span>Ajukan Penarikan</span>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection