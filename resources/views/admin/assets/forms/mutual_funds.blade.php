<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Jenis Reksadana --}}
    <div>
        <label class="block text-xs font-bold text-green-600 uppercase mb-2">Kategori Reksadana</label>
        <select name="subtype"
            class="w-full bg-green-50 border border-green-200 text-green-800 rounded-xl px-4 py-3 font-bold cursor-pointer focus:ring-1 focus:ring-green-500 outline-none">
            <option value="">-- Pilih Jenis --</option>
            @foreach(['RDPU', 'RDPT', 'RDS', 'Campuran'] as $t)
            <option value="{{ $t }}" {{ (old('subtype', $asset->subtype ?? '') == $t) ? 'selected' : '' }}>{{ $t }}
            </option>
            @endforeach
        </select>
    </div>

    {{-- NAV / Harga --}}
    <div>
        <label class="block text-xs font-bold text-gray-500 uppercase mb-2">NAV / Unit (IDR)</label>
        <div class="relative">
            <span class="absolute left-4 top-3.5 text-gray-400 font-bold">Rp</span>
            <input type="number" step="0.01" name="current_price"
                value="{{ old('current_price', $asset->current_price ?? '') }}"
                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 pl-10 font-bold text-lg focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none transition"
                required>
        </div>
    </div>
</div>

{{-- Nama Produk --}}
<div class="mt-6">
    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Nama Produk Reksadana</label>
    <input type="text" name="name" value="{{ old('name', $asset->name ?? '') }}"
        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-medium focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none transition"
        placeholder="Contoh: Sucorinvest Money Market Fund" required>
</div>

{{-- Kode Unik --}}
<div class="mt-6">
    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Kode Produk (Singkatan)</label>
    <input type="text" name="symbol" value="{{ old('symbol', $asset->symbol ?? '') }}"
        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 font-bold uppercase focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none transition"
        placeholder="SUCORMMF" required>
</div>

{{-- Logo MI --}}
<div class="mt-6">
    <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Logo Manajer Investasi (Opsional)</label>
    <input type="text" name="logo" value="{{ old('logo', $asset->logo ?? '') }}"
        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:bg-white focus:border-black focus:ring-1 focus:ring-black outline-none transition"
        placeholder="URL Logo...">
</div>