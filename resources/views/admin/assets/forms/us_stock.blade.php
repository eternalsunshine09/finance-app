<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    {{-- Kode Saham (Ticker) --}}
    <div>
        <label for="symbol" class="block text-sm font-bold text-gray-700">Kode Saham (Ticker) *</label>
        <input type="text" name="symbol" id="symbol" required value="{{ old('symbol', $asset->symbol ?? '') }}"
            placeholder="Contoh: AAPL, TSLA"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
        @error('symbol')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-500 font-medium">Kode ticker di Nasdaq/NYSE (huruf besar)</p>
    </div>

    {{-- Nama Perusahaan --}}
    <div>
        <label for="name" class="block text-sm font-bold text-gray-700">Nama Perusahaan *</label>
        <input type="text" name="name" id="name" required value="{{ old('name', $asset->name ?? '') }}"
            placeholder="Contoh: Apple Inc."
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
        @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Logo URL --}}
    <div>
        <label for="logo_url" class="block text-sm font-bold text-gray-700">
            URL Logo <span class="text-xs text-gray-500 font-normal">(opsional)</span>
        </label>
        <div class="mt-1 flex items-center space-x-3">
            <input type="url" name="logo_url" id="logo_url" value="{{ old('logo_url', $asset->logo_url ?? '') }}"
                placeholder="https://logo.clearbit.com/apple.com"
                class="block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
            <button type="button" onclick="previewLogo()"
                class="whitespace-nowrap px-4 py-3 bg-gray-100 hover:bg-gray-200 rounded-xl text-sm font-bold transition">
                Preview
            </button>
        </div>
        <div id="logo-preview" class="mt-2 hidden">
            <p class="text-xs text-gray-500 mb-1 font-medium">Preview Logo:</p>
            <img id="logo-preview-image" src="" alt="Logo Preview"
                class="h-12 w-auto rounded-lg border border-gray-200">
        </div>
        <p class="mt-1 text-xs text-gray-500 font-medium">
            Rekomendasi: Gunakan logo dengan rasio 1:1 (persegi)
        </p>
        @error('logo_url')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Harga Saat Ini (USD) --}}
    <div>
        <label for="current_price" class="block text-sm font-bold text-gray-700">Harga Saham Saat Ini (USD) *</label>
        <div class="relative">
            <span class="absolute left-3 top-3.5 text-gray-500 font-bold">$</span>
            <input type="number" name="current_price" id="current_price" step="0.01" min="0" required
                value="{{ old('current_price', $asset->current_price ?? '') }}" placeholder="Contoh: 150.25"
                class="mt-1 block w-full rounded-xl border border-gray-300 pl-8 px-4 py-3 focus:border-black focus:ring-0">
        </div>
        @error('current_price')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
        <p class="mt-1 text-xs text-gray-500 font-medium">Gunakan titik (.) untuk desimal</p>
    </div>

    {{-- Tanggal Update Harga --}}
    <div>
        <label for="price_updated_at" class="block text-sm font-bold text-gray-700">Tanggal Update Harga *</label>
        <input type="date" name="price_updated_at" id="price_updated_at" required
            value="{{ old('price_updated_at', $asset->price_updated_at ?? date('Y-m-d')) }}"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
        @error('price_updated_at')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Sektor Industri --}}
    <div>
        <label for="sector" class="block text-sm font-bold text-gray-700">Sektor Industri</label>
        <select name="sector" id="sector"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
            <option value="">Pilih Sektor</option>
            <option value="Technology" {{ old('sector', $asset->sector ?? '') == 'Technology' ? 'selected' : '' }}>
                Technology</option>
            <option value="Healthcare" {{ old('sector', $asset->sector ?? '') == 'Healthcare' ? 'selected' : '' }}>
                Healthcare</option>
            <option value="Financial" {{ old('sector', $asset->sector ?? '') == 'Financial' ? 'selected' : '' }}>
                Financial</option>
            <option value="Consumer Cyclical"
                {{ old('sector', $asset->sector ?? '') == 'Consumer Cyclical' ? 'selected' : '' }}>Consumer Cyclical
            </option>
            <option value="Industrial" {{ old('sector', $asset->sector ?? '') == 'Industrial' ? 'selected' : '' }}>
                Industrial</option>
            <option value="Communication Services"
                {{ old('sector', $asset->sector ?? '') == 'Communication Services' ? 'selected' : '' }}>Communication
                Services</option>
            <option value="Consumer Defensive"
                {{ old('sector', $asset->sector ?? '') == 'Consumer Defensive' ? 'selected' : '' }}>Consumer Defensive
            </option>
            <option value="Energy" {{ old('sector', $asset->sector ?? '') == 'Energy' ? 'selected' : '' }}>Energy
            </option>
            <option value="Utilities" {{ old('sector', $asset->sector ?? '') == 'Utilities' ? 'selected' : '' }}>
                Utilities</option>
            <option value="Real Estate" {{ old('sector', $asset->sector ?? '') == 'Real Estate' ? 'selected' : '' }}>
                Real Estate</option>
            <option value="Materials" {{ old('sector', $asset->sector ?? '') == 'Materials' ? 'selected' : '' }}>
                Materials</option>
        </select>
        @error('sector')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Bursa Saham --}}
    <div>
        <label for="exchange" class="block text-sm font-bold text-gray-700">Bursa Saham</label>
        <select name="exchange" id="exchange"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
            <option value="">Pilih Bursa</option>
            <option value="NASDAQ" {{ old('exchange', $asset->exchange ?? '') == 'NASDAQ' ? 'selected' : '' }}>NASDAQ
            </option>
            <option value="NYSE" {{ old('exchange', $asset->exchange ?? '') == 'NYSE' ? 'selected' : '' }}>NYSE</option>
            <option value="NYSE American"
                {{ old('exchange', $asset->exchange ?? '') == 'NYSE American' ? 'selected' : '' }}>NYSE American
            </option>
            <option value="OTC" {{ old('exchange', $asset->exchange ?? '') == 'OTC' ? 'selected' : '' }}>OTC
                (Over-the-Counter)</option>
        </select>
        @error('exchange')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Market Cap (Kapitalisasi Pasar) --}}
    <div>
        <label for="market_cap" class="block text-sm font-bold text-gray-700">Kapitalisasi Pasar (USD)</label>
        <div class="relative">
            <span class="absolute left-3 top-3.5 text-gray-500 font-bold">$</span>
            <input type="number" name="market_cap" id="market_cap" step="0.01" min="0"
                value="{{ old('market_cap', $asset->market_cap ?? '') }}" placeholder="Contoh: 2500000000000"
                class="mt-1 block w-full rounded-xl border border-gray-300 pl-8 px-4 py-3 focus:border-black focus:ring-0">
        </div>
        <p class="mt-1 text-xs text-gray-500 font-medium">Dalam USD (contoh: 2.5 triliun = 2500000000000)</p>
        @error('market_cap')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Negara Asal --}}
    <div>
        <label for="country" class="block text-sm font-bold text-gray-700">Negara Asal</label>
        <input type="text" name="country" id="country" value="{{ old('country', $asset->country ?? 'United States') }}"
            placeholder="Contoh: United States"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
        @error('country')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- Website Perusahaan --}}
    <div>
        <label for="company_website" class="block text-sm font-bold text-gray-700">Website Perusahaan</label>
        <input type="url" name="company_website" id="company_website"
            value="{{ old('company_website', $asset->company_website ?? '') }}" placeholder="https://apple.com"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
        @error('company_website')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    {{-- CEO / Direktur Utama --}}
    <div>
        <label for="ceo" class="block text-sm font-bold text-gray-700">CEO / Direktur Utama</label>
        <input type="text" name="ceo" id="ceo" value="{{ old('ceo', $asset->ceo ?? '') }}"
            placeholder="Contoh: Tim Cook"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
        @error('ceo')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<!-- Catatan / Deskripsi -->
<div class="mt-6">
    <label for="notes" class="block text-sm font-bold text-gray-700">Deskripsi / Catatan</label>
    <textarea name="notes" id="notes" rows="4"
        placeholder="Contoh: Apple Inc. adalah perusahaan teknologi multinasional Amerika yang mengkhususkan diri dalam elektronik konsumen, perangkat lunak komputer, dan layanan online..."
        class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">{{ old('notes', $asset->notes ?? '') }}</textarea>
    @error('notes')
    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

<!-- JavaScript untuk Preview Logo -->
<script>
function previewLogo() {
    const logoUrl = document.getElementById('logo_url').value;
    const previewDiv = document.getElementById('logo-preview');
    const previewImg = document.getElementById('logo-preview-image');

    if (logoUrl) {
        previewImg.src = logoUrl;
        previewDiv.classList.remove('hidden');

        // Handle error jika gambar tidak bisa dimuat
        previewImg.onerror = function() {
            previewDiv.innerHTML = `
                <p class="text-xs text-red-500 mt-1 font-medium">
                    ❌ Tidak dapat memuat gambar dari URL tersebut. 
                    Pastikan URL gambar valid dan dapat diakses.
                </p>
            `;
        };
    } else {
        previewDiv.classList.add('hidden');
    }
}

// Auto-preview saat URL berubah
document.getElementById('logo_url').addEventListener('input', function() {
    // Tunggu 500ms setelah selesai mengetik
    clearTimeout(this.timer);
    this.timer = setTimeout(previewLogo, 500);
});

// Preview saat halaman dimuat (jika ada data old)
document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('logo_url').value) {
        previewLogo();
    }
});
</script>

<!-- Informasi tambahan untuk data master -->
<div class="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
    <h3 class="text-sm font-bold text-blue-800 mb-2">📝 Informasi Data Master Saham US</h3>
    <p class="text-xs text-blue-600 mb-2 font-medium">
        Data master ini akan ditampilkan di katalog saham AS untuk semua investor.
        Investor nanti akan menambahkan kepemilikan pribadi dengan mengisi jumlah lot dan harga beli masing-masing.
    </p>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mt-3">
        <div class="text-xs">
            <span class="font-bold text-blue-700">📍 Ticker:</span>
            <p class="text-blue-600 font-medium">Kode unik di bursa (NASDAQ/NYSE)</p>
        </div>
        <div class="text-xs">
            <span class="font-bold text-blue-700">💰 Harga USD:</span>
            <p class="text-blue-600 font-medium">Harga dalam dolar AS (menggunakan titik desimal)</p>
        </div>
        <div class="text-xs">
            <span class="font-bold text-blue-700">🏢 Sektor:</span>
            <p class="text-blue-600 font-medium">Klasifikasi industri perusahaan</p>
        </div>
    </div>
</div>