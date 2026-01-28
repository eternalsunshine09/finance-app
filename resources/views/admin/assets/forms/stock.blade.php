<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Kode Saham --}}
    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Kode Saham (Ticker)</label>
        <input type="text" name="symbol" value="{{ old('symbol', $asset->symbol ?? '') }}"
            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-bold text-lg uppercase focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none transition"
            placeholder="BBCA" required>
        <p class="text-[10px] text-gray-400 mt-1">*Kode 4 huruf emiten IDX.</p>
    </div>

    {{-- Harga Saham --}}
    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Harga Saat Ini (IDR)</label>
        <div class="relative">
            <span class="absolute left-4 top-3.5 text-gray-400 font-bold">Rp</span>
            <input type="number" name="current_price" value="{{ old('current_price', $asset->current_price ?? '') }}"
                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 pl-10 font-bold text-lg focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none transition"
                required>
        </div>
    </div>
</div>

{{-- Nama Perusahaan --}}
<div class="mt-6">
    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nama Perusahaan Tbk</label>
    <input type="text" name="name" value="{{ old('name', $asset->name ?? '') }}"
        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-medium focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none transition"
        placeholder="Contoh: Bank Central Asia Tbk" required>
</div>

{{-- Logo --}}
<div class="mt-6">
    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Logo Emiten (Opsional)</label>
    <input type="text" name="logo" value="{{ old('logo', $asset->logo ?? '') }}"
        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none transition"
        placeholder="https://...">
</div>