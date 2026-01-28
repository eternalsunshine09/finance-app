<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Kode Aset --}}
    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Kode Produk</label>
        {{-- Tambahkan attribut uppercase di class agar user otomatis ngetik huruf besar --}}
        <input type="text" name="symbol" value="{{ old('symbol', $asset->symbol ?? '') }}"
            class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-bold text-lg uppercase focus:bg-white focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none transition"
            placeholder="GOLD" required>

        {{-- CATATAN PENTING UNTUK USER --}}
        <p class="text-[10px] text-gray-400 mt-1">
            <span class="text-red-500 font-bold">*PENTING:</span> Gunakan kode <b>GOLD</b> jika ingin harga update
            otomatis mengikuti pasar dunia.
            Gunakan kode lain (cth: ANTAM5G) untuk harga manual (fisik).
        </p>
    </div>

    {{-- Harga --}}
    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Harga Jual (IDR)</label>
        <div class="relative">
            <span class="absolute left-4 top-3.5 text-gray-400 font-bold">Rp</span>

            {{-- 🔥 PERBAIKAN: Tambahkan step="any" agar menerima desimal/koma --}}
            <input type="number" step="any" name="current_price"
                value="{{ old('current_price', $asset->current_price ?? '') }}"
                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 pl-10 font-bold text-lg focus:bg-white focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none transition"
                placeholder="1350000" required>
        </div>
        <p class="text-[10px] text-gray-400 mt-1">Harga per unit/gram.</p>
    </div>
</div>

{{-- Nama Produk --}}
<div class="mt-6">
    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nama Produk Emas</label>
    <input type="text" name="name" value="{{ old('name', $asset->name ?? '') }}"
        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-medium focus:bg-white focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none transition"
        placeholder="Contoh: Tabungan Emas Pegadaian" required>
</div>

{{-- Gambar --}}
<div class="mt-6">
    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Gambar Produk (URL)</label>
    <input type="text" name="logo" value="{{ old('logo', $asset->logo ?? '') }}"
        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-yellow-500 focus:ring-1 focus:ring-yellow-500 outline-none transition"
        placeholder="https://...">
</div>