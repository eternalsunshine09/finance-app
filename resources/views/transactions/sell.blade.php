@extends('layouts.app')

@section('title', 'Jual Aset')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-slate-50 py-12 px-4">
    <div class="max-w-4xl w-full">

        <div class="text-center mb-10">
            <h1 class="text-4xl font-black text-slate-800 tracking-tighter mb-2">💰 Jual Aset</h1>
            <p class="text-slate-500 font-medium">Cairkan investasi Anda menjadi saldo tunai.</p>
        </div>

        <div
            class="bg-white rounded-[2.5rem] shadow-xl border border-slate-100 overflow-hidden flex flex-col lg:flex-row">

            {{-- KOLOM KIRI (FORM) --}}
            <div class="lg:w-3/5 p-8 lg:p-12 relative">
                <form action="{{ route('sell.process') }}" method="POST" class="space-y-6 relative z-10">
                    @csrf

                    {{-- 1. PILIH ASET YANG DIMILIKI --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 tracking-wider">1.
                            Pilih Aset</label>
                        <div class="relative group">
                            <select name="asset_symbol" id="assetSelect"
                                class="w-full pl-14 pr-10 py-3 bg-white border border-slate-200 rounded-2xl font-bold text-slate-700 appearance-none focus:ring-4 focus:ring-rose-100 focus:border-rose-500 transition-all outline-none"
                                required>
                                <option value="" data-quantity="0">-- Pilih Aset Anda --</option>
                                @foreach($myPortfolio as $item)
                                <option value="{{ $item->asset_symbol }}" data-quantity="{{ $item->quantity }}"
                                    data-price="{{ $item->asset->current_price ?? 0 }}"
                                    data-type="{{ $item->asset->type ?? 'Stock' }}"
                                    {{ (isset($symbol) && $symbol == $item->asset_symbol) ? 'selected' : '' }}>
                                    {{ $item->asset_symbol }} (Tersedia: {{ number_format($item->quantity, 4) }})
                                </option>
                                @endforeach
                            </select>
                            <div class="absolute left-4 top-3.5 text-rose-500"><i class="fas fa-box-open text-lg"></i>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        {{-- 2. HARGA JUAL --}}
                        <div>
                            <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 tracking-wider">2.
                                Harga Jual</label>
                            <div class="relative">
                                <span id="currencyLabel"
                                    class="absolute left-3 top-3.5 text-slate-400 font-bold text-sm">Rp</span>
                                <input type="number" step="any" name="sell_price" id="sellPriceInput"
                                    class="w-full pl-10 pr-4 py-3 bg-slate-50 border-0 rounded-2xl font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-200 transition"
                                    required>
                            </div>
                        </div>

                        {{-- 3. JUMLAH JUAL --}}
                        <div>
                            <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 tracking-wider">3.
                                Unit Dijual</label>
                            <input type="number" step="0.00000001" name="amount" id="amountInput"
                                class="w-full px-4 py-3 bg-rose-50/50 border-2 border-rose-100 rounded-2xl font-bold text-rose-700 focus:border-rose-500 focus:ring-0 transition"
                                placeholder="0.00" required>
                        </div>
                    </div>

                    {{-- 4. FEE BROKER --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 tracking-wider">4. Fee
                            Broker (Nominal)</label>
                        <div class="relative">
                            <span id="feeCurrencyLabel"
                                class="absolute left-3 top-3.5 text-slate-400 font-bold text-xs">Rp</span>
                            <input type="number" step="any" name="fee" id="feeInput"
                                class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-600 focus:bg-white focus:ring-2 focus:ring-slate-200 transition"
                                value="0">
                        </div>
                    </div>

                    <hr class="border-slate-100">

                    {{-- 5. TUJUAN DANA --}}
                    <div>
                        <label class="block text-xs font-extrabold text-slate-400 uppercase mb-2 tracking-wider">5.
                            Masukkan Hasil Ke</label>
                        <select name="wallet_id" id="walletSelect"
                            class="w-full pl-14 py-3 bg-slate-50 border border-slate-200 rounded-2xl font-bold text-slate-700 outline-none"
                            required>
                            @foreach($wallets as $wallet)
                            <option value="{{ $wallet->id }}">{{ $wallet->bank_name }} ({{ $wallet->currency }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" id="realSubmitBtn" class="hidden"></button>
                </form>
            </div>

            {{-- KOLOM KANAN (SUMMARY) --}}
            <div class="lg:w-2/5 bg-slate-900 p-8 lg:p-12 text-white relative flex flex-col justify-between">
                <div class="relative z-10">
                    <h3 class="text-lg font-bold mb-6 flex items-center gap-2"><i
                            class="fas fa-receipt text-rose-400"></i> Estimasi Terima</h3>
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between text-slate-400"><span>Gross Total</span> <span
                                id="summaryGross" class="font-mono">0</span></div>
                        <div class="flex justify-between text-slate-400"><span>Fee Broker</span> <span id="summaryFee"
                                class="font-mono text-rose-400">- 0</span></div>
                        <div class="h-px bg-slate-700 my-4"></div>
                        <div class="flex justify-between items-end">
                            <span class="text-slate-300 font-bold">Net Received</span>
                            <span id="totalDisplay" class="text-3xl font-black text-emerald-400">0</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8 relative z-10">
                    <button type="button" onclick="document.getElementById('realSubmitBtn').click()"
                        class="w-full bg-rose-600 hover:bg-rose-500 text-white font-bold py-4 rounded-xl shadow-lg transition-all flex justify-center items-center gap-3">
                        <span>Konfirmasi Jual</span>
                        <i class="fas fa-check-circle"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const assetSelect = document.getElementById('assetSelect');
const sellPriceInput = document.getElementById('sellPriceInput');
const amountInput = document.getElementById('amountInput');
const feeInput = document.getElementById('feeInput');

const summaryGross = document.getElementById('summaryGross');
const summaryFee = document.getElementById('summaryFee');
const totalDisplay = document.getElementById('totalDisplay');

function calculate() {
    const option = assetSelect.options[assetSelect.selectedIndex];
    const price = parseFloat(sellPriceInput.value) || 0;
    const amount = parseFloat(amountInput.value) || 0;
    const fee = parseFloat(feeInput.value) || 0;

    const gross = price * amount;
    const net = gross - fee;

    const formatter = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR'
    });

    summaryGross.innerText = formatter.format(gross);
    summaryFee.innerText = "- " + formatter.format(fee);
    totalDisplay.innerText = formatter.format(net);
}

assetSelect.addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    sellPriceInput.value = option.getAttribute('data-price');
    calculate();
});

[sellPriceInput, amountInput, feeInput].forEach(el => el.addEventListener('input', calculate));
</script>
@endsection