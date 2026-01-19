@extends('layouts.app')
@section('title', 'Tukar Valas')

@section('content')
<div class="min-h-screen bg-slate-50 pt-12 pb-12" x-data="{ 
        inputIDR: '', 
        rate: {{ $currentRate }}, // Default ambil dari controller
        
        // Hitung otomatis: Rupiah / Kurs User
        get resultUSD() {
            if(!this.inputIDR || !this.rate) return 0;
            return (this.inputIDR / this.rate).toFixed(2);
        }
     }">

    <div class="max-w-xl mx-auto px-4">

        {{-- Header --}}
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-black text-slate-800">💱 Tukar Valas</h1>
            <p class="text-slate-500 mt-2">Atur kurs dan nominal sesuai kebutuhan transaksimu.</p>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl p-8 border border-slate-100 relative overflow-hidden">

            {{-- Hiasan Background --}}
            <div class="absolute top-0 right-0 w-32 h-32 bg-indigo-50 rounded-full blur-2xl -mr-10 -mt-10"></div>

            <form action="{{ route('exchange.process') }}" method="POST">
                @csrf

                {{-- 1. INPUT KURS (Editable) --}}
                <div class="mb-6">
                    <label class="block text-xs font-extrabold text-indigo-500 uppercase mb-2 ml-2">
                        Kurs Konversi (1 USD = ?)
                    </label>
                    <div class="relative group">
                        <span class="absolute left-5 top-4 text-indigo-400 font-bold">Rp</span>
                        <input type="number" name="custom_rate" x-model="rate"
                            class="w-full bg-indigo-50 border border-indigo-100 rounded-2xl py-4 pl-12 pr-5 font-bold text-indigo-700 text-lg focus:outline-none focus:border-indigo-500 focus:bg-white transition shadow-inner">
                        <div
                            class="absolute right-4 top-4 text-xs text-indigo-300 font-bold bg-white px-2 py-1 rounded">
                            Editable
                        </div>
                    </div>
                    <p class="text-[10px] text-slate-400 mt-2 ml-2">
                        *Default adalah kurs pasar saat ini. Anda bisa mengubahnya jika menukar di tempat lain dengan
                        rate berbeda.
                    </p>
                </div>

                <hr class="border-slate-100 my-6">

                {{-- 2. INPUT SUMBER DANA (IDR) --}}
                <div class="mb-4 relative">
                    <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 ml-2">Tukar Rupiah
                        (Sumber)</label>
                    <div class="relative">
                        <span class="absolute left-5 top-4 text-slate-400 font-bold">Rp</span>
                        <input type="number" name="amount_idr" x-model="inputIDR" placeholder="0"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-4 pl-12 pr-5 font-black text-slate-700 text-xl focus:outline-none focus:border-indigo-500 transition"
                            required>
                    </div>
                    <div class="flex justify-between items-center mt-2 ml-2">
                        <p class="text-xs text-slate-400">Saldo IDR: <span class="font-bold text-slate-600">Rp
                                {{ number_format($walletIDR->balance ?? 0, 0, ',', '.') }}</span></p>
                    </div>
                </div>

                {{-- Icon Panah --}}
                <div class="flex justify-center -my-2 relative z-10">
                    <div class="bg-white border border-slate-200 rounded-full p-2 text-slate-400 shadow-sm">
                        <i class="fas fa-arrow-down"></i>
                    </div>
                </div>

                {{-- 3. HASIL ESTIMASI (USD) --}}
                <div class="mb-8 mt-4 relative">
                    <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 ml-2">Diterima dalam USD
                        (Hasil)</label>
                    <div class="relative">
                        <span class="absolute left-5 top-4 text-emerald-500 font-bold">$</span>
                        <input type="text" :value="resultUSD" readonly
                            class="w-full bg-emerald-50 border border-emerald-100 rounded-2xl py-4 pl-12 pr-5 font-black text-emerald-600 text-xl focus:outline-none cursor-not-allowed">
                    </div>
                </div>

                {{-- Tombol Aksi --}}
                <button type="submit"
                    class="w-full bg-indigo-600 text-white font-bold text-lg py-4 rounded-2xl shadow-xl shadow-indigo-500/30 hover:bg-indigo-700 hover:scale-[1.02] transition-all flex justify-center items-center gap-2">
                    <span>Proses Tukar</span>
                    <i class="fas fa-exchange-alt"></i>
                </button>

                <div class="text-center mt-6">
                    <a href="{{ route('wallet.index') }}"
                        class="text-slate-400 font-bold text-sm hover:text-slate-600">Batal</a>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection