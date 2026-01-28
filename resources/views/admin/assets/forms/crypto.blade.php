<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Simbol Koin --}}
    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Simbol Koin</label>
        <input type="text" name="symbol" value="{{ old('symbol', $asset->symbol ?? '') }}"
            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-bold text-lg uppercase focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none transition"
            placeholder="BTC" required>
    </div>

    {{-- Harga USD --}}
    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Harga Saat Ini (USD)</label>
        <div class="relative">
            <span class="absolute left-4 top-3.5 text-gray-400 font-bold">$</span>
            <input type="number" step="any" name="current_price"
                value="{{ old('current_price', $asset->current_price ?? '') }}"
                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 pl-8 font-bold text-lg focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none transition"
                required>
        </div>
    </div>
</div>

{{-- Nama Koin --}}
<div class="mt-6">
    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nama Aset Crypto</label>
    <input type="text" name="name" value="{{ old('name', $asset->name ?? '') }}"
        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-medium focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none transition"
        placeholder="Bitcoin" required>
</div>

{{-- API ID (Khusus Crypto) --}}
<div class="mt-6">
    <label class="block text-xs font-bold text-orange-600 uppercase mb-2">Coingecko API ID</label>
    <input type="text" name="api_id" value="{{ old('api_id', $asset->api_id ?? '') }}"
        class="w-full bg-orange-50 border border-orange-200 text-orange-800 rounded-xl px-4 py-3 text-sm font-mono focus:ring-1 focus:ring-orange-500 outline-none transition"
        placeholder="bitcoin (huruf kecil)" required>
    <p class="text-[10px] text-orange-500 mt-1">*Wajib diisi untuk fitur auto-sync harga & logo.</p>
</div>

{{-- Hidden Logo (Karena Crypto ambil otomatis dari API, kita bisa hide atau biarkan kosong) --}}
<input type="hidden" name="logo" value="{{ old('logo', $asset->logo ?? '') }}">