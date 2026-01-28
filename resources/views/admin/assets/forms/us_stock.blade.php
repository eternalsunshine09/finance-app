<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Kode Saham --}}
    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Kode Saham (Ticker US)</label>
        <input type="text" name="symbol" value="{{ old('symbol', $asset->symbol ?? '') }}"
            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-bold text-lg uppercase focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none transition"
            placeholder="AAPL" required>
        <p class="text-[10px] text-gray-400 mt-1">*Kode Ticker Nasdaq/NYSE (Contoh: AAPL, TSLA).</p>
    </div>

    {{-- Harga Saham --}}
    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Harga Saat Ini (USD)</label>
        <div class="relative">
            <span class="absolute left-4 top-3.5 text-gray-400 font-bold">$</span>
            <input type="number" step="0.01" name="current_price"
                value="{{ old('current_price', $asset->current_price ?? '') }}"
                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 pl-8 font-bold text-lg focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none transition"
                placeholder="150.25" required>
        </div>
        <p class="text-[10px] text-gray-400 mt-1">*Gunakan titik (.) untuk desimal.</p>
    </div>
</div>

{{-- Nama Perusahaan --}}
<div class="mt-6">
    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nama Perusahaan</label>
    <input type="text" name="name" value="{{ old('name', $asset->name ?? '') }}"
        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-medium focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none transition"
        placeholder="Contoh: Apple Inc." required>
</div>

{{-- Logo --}}
<div class="mt-6">
    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Logo Emiten (URL)</label>
    <input type="text" name="logo" value="{{ old('logo', $asset->logo ?? '') }}"
        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none transition"
        placeholder="https://logo.clearbit.com/apple.com">
</div>