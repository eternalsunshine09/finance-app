@extends('layouts.app')
@section('title', 'Tukar Valas')

@section('content')
<div class="min-h-screen bg-slate-50 pt-6 pb-12" x-data="exchangeApp()">

    <div class="max-w-xl mx-auto px-4">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-black text-slate-800">💱 Tukar Valas</h1>
            <p class="text-slate-500 mt-2 font-medium">Pindahkan dana antar dompet Anda.</p>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-xl p-8 border border-slate-100 relative overflow-hidden">

            <form action="{{ route('exchange.process') }}" method="POST">
                @csrf

                {{-- 1. PILIH SUMBER DANA --}}
                <div class="mb-6">
                    <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 ml-2">1. Sumber Dana
                        (Dari)</label>

                    <select name="source_wallet_id" x-model="sourceId" @change="resetTarget()"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3 px-4 font-bold text-slate-700 focus:outline-none focus:border-indigo-500 cursor-pointer">
                        <option value="" disabled>Pilih Dompet Asal</option>

                        {{-- GUNAKAN BLADE LOOP AGAR PASTI MUNCUL --}}
                        @foreach($wallets as $wallet)
                        <option value="{{ $wallet->id }}">
                            {{ $wallet->bank_name }} - {{ $wallet->currency }} (Rp
                            {{ number_format($wallet->balance, 0, ',', '.') }})
                        </option>
                        @endforeach
                    </select>

                    {{-- Input Nominal --}}
                    <div class="relative mt-2">
                        {{-- Tampilkan mata uang sesuai ID yang dipilih --}}
                        <span class="absolute left-5 top-4 text-slate-400 font-bold"
                            x-text="getCurrency(sourceId)"></span>
                        <input type="number" name="amount_source" x-model="amount" placeholder="0"
                            class="w-full bg-white border border-slate-200 rounded-2xl py-4 pl-12 pr-5 font-black text-slate-700 text-xl focus:outline-none focus:border-indigo-500 transition"
                            required>
                    </div>
                </div>

                {{-- RATE DIVIDER --}}
                <div class="relative py-4">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-100"></div>
                    </div>
                    <div class="relative flex justify-center">
                        <div class="bg-white px-4 flex flex-col items-center">
                            <label class="text-[10px] font-bold text-indigo-400">Rate Pembagi</label>
                            <input type="number" name="rate" x-model="rate"
                                class="w-24 bg-indigo-50 text-center font-bold text-indigo-600 rounded-full py-1 text-sm border border-indigo-100 focus:outline-none">
                        </div>
                    </div>
                </div>

                {{-- 2. PILIH TUJUAN --}}
                <div class="mb-8">
                    <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 ml-2">2. Terima Di
                        (Tujuan)</label>

                    <select name="target_wallet_id" x-model="targetId"
                        class="w-full bg-emerald-50 border border-emerald-100 rounded-2xl py-3 px-4 font-bold text-emerald-800 focus:outline-none focus:border-emerald-500 cursor-pointer">
                        <option value="" disabled>Pilih Dompet Tujuan</option>

                        {{-- GUNAKAN BLADE LOOP + ALPINE HIDDEN --}}
                        @foreach($wallets as $wallet)
                        {{-- Opsi ini akan HIDDEN (sembunyi) jika ID-nya sama dengan Source ID --}}
                        <option value="{{ $wallet->id }}" :hidden="sourceId == {{ $wallet->id }}">
                            {{ $wallet->bank_name }} - {{ $wallet->currency }}
                        </option>
                        @endforeach
                    </select>

                    {{-- Hasil Estimasi --}}
                    <div class="relative mt-2">
                        <span class="absolute left-5 top-4 text-emerald-500 font-bold"
                            x-text="getCurrency(targetId)"></span>
                        <input type="text" :value="resultAmount" readonly
                            class="w-full bg-emerald-50/50 border border-emerald-100 rounded-2xl py-4 pl-12 pr-5 font-black text-emerald-600 text-xl focus:outline-none cursor-not-allowed">
                    </div>

                    {{-- Pesan Error jika mata uang sama --}}
                    <div x-show="sourceId && targetId && getCurrency(sourceId) == getCurrency(targetId)"
                        class="mt-2 text-center">
                        <span class="text-xs text-orange-500 font-bold bg-orange-100 px-2 py-1 rounded">
                            ⚠️ Perhatian: Mata uang asal dan tujuan sama.
                        </span>
                    </div>
                </div>

                <button type="submit" :disabled="!sourceId || !targetId || !amount"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-lg py-4 rounded-2xl shadow-xl transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                    Proses Tukar
                </button>

            </form>
        </div>
    </div>
</div>

<script>
function exchangeApp() {
    return {
        // Kita ubah data Blade menjadi Object Javascript agar mudah dicari Currency-nya
        walletsData: {
            @foreach($wallets as $w) {
                {
                    $w - > id
                }
            }: '{{ $w->currency }}',
            @endforeach
        },

        sourceId: '',
        targetId: '',
        amount: '',
        rate: {
            {
                $currentRate ?? 15500
            }
        },

        // Fungsi Helper Cari Currency berdasarkan ID
        getCurrency(id) {
            if (!id) return '?';
            // Jika currency USD return $, jika IDR return Rp
            let curr = this.walletsData[id];
            return curr === 'USD' ? '$' : 'Rp';
        },

        // Reset target jika sumber diganti
        resetTarget() {
            this.targetId = '';
        },

        // Hitung Hasil
        get resultAmount() {
            if (!this.amount || !this.rate) return 0;
            let res = this.amount / this.rate;
            return new Intl.NumberFormat('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(res);
        }
    }
}
</script>
@endsection