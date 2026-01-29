<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <!-- Nama Reksadana -->
    <div>
        <label for="name" class="block text-sm font-bold text-gray-700">Nama Reksadana *</label>
        <input type="text" name="name" id="name" required value="{{ old('name') }}"
            placeholder="Contoh: Reksadana Saham Mandiri"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
        @error('name')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Kode Reksadana -->
    <div>
        <label for="symbol" class="block text-sm font-bold text-gray-700">Kode Reksadana *</label>
        <input type="text" name="symbol" id="symbol" required value="{{ old('symbol') }}" placeholder="Contoh: RDSM"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
        @error('symbol')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Logo URL -->
    <div>
        <label for="logo_url" class="block text-sm font-bold text-gray-700">
            URL Logo <span class="text-xs text-gray-500 font-normal">(opsional)</span>
        </label>
        <div class="mt-1 flex items-center space-x-3">
            <input type="url" name="logo_url" id="logo_url" value="{{ old('logo_url') }}"
                placeholder="https://example.com/logo.png"
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

    <!-- Harga Per Unit Saat Ini -->
    <div>
        <label for="current_price" class="block text-sm font-bold text-gray-700">Harga per Unit Saat Ini (Rp) *</label>
        <input type="number" name="current_price" id="current_price" step="0.01" min="0" required
            value="{{ old('current_price') }}" placeholder="Contoh: 1500"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
        @error('current_price')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Tanggal Update Harga -->
    <div>
        <label for="price_updated_at" class="block text-sm font-bold text-gray-700">Tanggal Update Harga *</label>
        <input type="date" name="price_updated_at" id="price_updated_at" required
            value="{{ old('price_updated_at', date('Y-m-d')) }}"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
        @error('price_updated_at')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Biaya Management Tahunan -->
    <div>
        <label for="management_fee" class="block text-sm font-bold text-gray-700">Biaya Management Tahunan (%)</label>
        <input type="number" name="management_fee" id="management_fee" step="0.01" min="0" max="10"
            value="{{ old('management_fee') }}" placeholder="Contoh: 1.5"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
        <p class="mt-1 text-xs text-gray-500 font-medium">Biaya tahunan yang dikenakan oleh manajer investasi</p>
        @error('management_fee')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Minimum Pembelian -->
    <div>
        <label for="minimum_purchase" class="block text-sm font-bold text-gray-700">Minimum Pembelian (Rp)</label>
        <input type="number" name="minimum_purchase" id="minimum_purchase" step="0.01" min="0"
            value="{{ old('minimum_purchase') }}" placeholder="Contoh: 100000"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
        <p class="mt-1 text-xs text-gray-500 font-medium">Jumlah minimum untuk pembelian pertama</p>
        @error('minimum_purchase')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Jenis Reksadana -->
    <div>
        <label for="mutual_fund_type" class="block text-sm font-bold text-gray-700">Jenis Reksadana *</label>
        <select name="mutual_fund_type" id="mutual_fund_type" required
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
            <option value="">Pilih Jenis</option>
            <option value="Pasar Uang" {{ old('mutual_fund_type') == 'Pasar Uang' ? 'selected' : '' }}>Pasar Uang
            </option>
            <option value="Pendapatan Tetap" {{ old('mutual_fund_type') == 'Pendapatan Tetap' ? 'selected' : '' }}>
                Pendapatan Tetap</option>
            <option value="Campuran" {{ old('mutual_fund_type') == 'Campuran' ? 'selected' : '' }}>Campuran</option>
            <option value="Saham" {{ old('mutual_fund_type') == 'Saham' ? 'selected' : '' }}>Saham</option>
            <option value="ETF" {{ old('mutual_fund_type') == 'ETF' ? 'selected' : '' }}>ETF</option>
        </select>
        @error('mutual_fund_type')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Tingkat Risiko -->
    <div>
        <label for="risk_level" class="block text-sm font-bold text-gray-700">Tingkat Risiko</label>
        <select name="risk_level" id="risk_level"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
            <option value="">Pilih Tingkat Risiko</option>
            <option value="Rendah" {{ old('risk_level') == 'Rendah' ? 'selected' : '' }}>Rendah</option>
            <option value="Sedang" {{ old('risk_level') == 'Sedang' ? 'selected' : '' }}>Sedang</option>
            <option value="Tinggi" {{ old('risk_level') == 'Tinggi' ? 'selected' : '' }}>Tinggi</option>
            <option value="Sangat Tinggi" {{ old('risk_level') == 'Sangat Tinggi' ? 'selected' : '' }}>Sangat Tinggi
            </option>
        </select>
        @error('risk_level')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Nama Manajer Investasi -->
    <div>
        <label for="investment_manager" class="block text-sm font-bold text-gray-700">Manajer Investasi *</label>
        <input type="text" name="investment_manager" id="investment_manager" required
            value="{{ old('investment_manager') }}" placeholder="Contoh: Mandiri Investa"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
        @error('investment_manager')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Website Manajer Investasi -->
    <div>
        <label for="manager_website" class="block text-sm font-bold text-gray-700">Website Manajer Investasi</label>
        <input type="url" name="manager_website" id="manager_website" value="{{ old('manager_website') }}"
            placeholder="https://mandiri-investa.co.id"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
        @error('manager_website')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Tanggal Peluncuran -->
    <div>
        <label for="launch_date" class="block text-sm font-bold text-gray-700">Tanggal Peluncuran</label>
        <input type="date" name="launch_date" id="launch_date" value="{{ old('launch_date') }}"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
        <p class="mt-1 text-xs text-gray-500 font-medium">Tanggal pertama kali reksadana diluncurkan</p>
        @error('launch_date')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Kategori -->
    <div>
        <label for="category" class="block text-sm font-bold text-gray-700">Kategori</label>
        <select name="category" id="category"
            class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">
            <option value="">Pilih Kategori</option>
            <option value="Konvensional" {{ old('category') == 'Konvensional' ? 'selected' : '' }}>Konvensional</option>
            <option value="Syariah" {{ old('category') == 'Syariah' ? 'selected' : '' }}>Syariah</option>
        </select>
        @error('category')
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>
</div>

<!-- Catatan / Deskripsi -->
<div class="mt-6">
    <label for="notes" class="block text-sm font-bold text-gray-700">Deskripsi / Catatan</label>
    <textarea name="notes" id="notes" rows="4"
        placeholder="Contoh: Reksadana dengan fokus pada saham blue chip Indonesia, memiliki track record return 15% per tahun..."
        class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 focus:border-black focus:ring-0">{{ old('notes') }}</textarea>
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
    <h3 class="text-sm font-bold text-blue-800 mb-2">📝 Informasi Data Master</h3>
    <p class="text-xs text-blue-600 mb-2 font-medium">
        Data master ini akan ditampilkan di katalog reksadana untuk semua investor.
        Investor nanti akan menambahkan kepemilikan pribadi dengan mengisi jumlah unit dan harga beli masing-masing.
    </p>
    <div class="grid grid-cols-2 md:grid-cols-3 gap-2 mt-3">
        <div class="text-xs">
            <span class="font-bold text-blue-700">📍 Logo URL:</span>
            <p class="text-blue-600 font-medium">Gunakan CDN atau hosting gambar yang stabil</p>
        </div>
        <div class="text-xs">
            <span class="font-bold text-blue-700">💰 Minimum Pembelian:</span>
            <p class="text-blue-600 font-medium">Biasanya Rp 100.000 untuk pertama kali</p>
        </div>
        <div class="text-xs">
            <span class="font-bold text-blue-700">📈 Risiko:</span>
            <p class="text-blue-600 font-medium">Sesuaikan dengan jenis reksadana</p>
        </div>
    </div>
</div>