@extends('layouts.admin')

@section('title', 'Tambah Aset Baru')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('admin.assets.index') }}"
            class="text-slate-400 hover:text-white text-sm mb-4 inline-flex items-center gap-2 transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Aset
        </a>
        <h1 class="text-3xl font-black text-white">Tambah Aset Baru</h1>
        <p class="text-slate-400 mt-2">Daftarkan instrumen investasi baru ke dalam sistem.</p>
    </div>

    {{-- Form Card dengan Alpine Data --}}
    <div class="bg-slate-800 rounded-3xl p-8 border border-slate-700 shadow-2xl relative overflow-hidden"
        x-data="{ assetType: '' }">

        {{-- Hiasan --}}
        <div
            class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none">
        </div>

        <form action="{{ route('admin.assets.store') }}" method="POST" class="space-y-6 relative z-10">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- 1. KODE SIMBOL --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Kode Simbol (Ticker)</label>
                    <input type="text" name="symbol" placeholder="Ex: BBCA / SUCOR-MM"
                        class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white font-bold text-lg focus:border-indigo-500 focus:outline-none uppercase placeholder-slate-600 transition"
                        required>
                </div>

                {{-- 2. KATEGORI UTAMA --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Kategori Aset</label>
                    <div class="relative">
                        <select name="type" id="typeSelect" x-model="assetType"
                            class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:outline-none appearance-none cursor-pointer font-medium transition"
                            required>
                            <option value="" disabled selected>-- Pilih Kategori --</option>
                            <option value="Stock">🏢 Saham (Stock)</option>
                            <option value="Crypto">₿ Crypto Asset</option>
                            <option value="Mutual Fund">📈 Reksa Dana</option>
                            <option value="Gold">🥇 Emas / Logam Mulia</option>
                            <option value="Currency">💱 Forex / Mata Uang</option>
                        </select>
                        <div
                            class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-slate-400">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>

            {{-- 🔥 SUB-KATEGORI KHUSUS REKSA DANA (Muncul jika assetType == Mutual Fund) --}}
            <div x-show="assetType === 'Mutual Fund'" x-transition.opacity
                class="bg-indigo-500/10 border border-indigo-500/30 p-4 rounded-xl">
                <label class="block text-xs font-bold text-indigo-300 uppercase mb-2">Jenis Reksa Dana</label>
                <div class="relative">
                    <select name="subtype"
                        class="w-full bg-slate-900 border border-indigo-500/50 rounded-xl px-4 py-3 text-white focus:border-indigo-400 focus:outline-none appearance-none cursor-pointer font-medium transition">
                        <option value="" disabled selected>-- Pilih Jenis RD --</option>
                        <option value="RDPU">RDPU (Pasar Uang)</option>
                        <option value="RDPT">RDPT (Pendapatan Tetap)</option>
                        <option value="RDS">RDS (Saham)</option>
                        <option value="Campuran">RD Campuran</option>
                        <option value="Indeks">RD Indeks / ETF</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-indigo-400">
                        <i class="fas fa-layer-group text-xs"></i>
                    </div>
                </div>
            </div>

            {{-- 3. NAMA ASET --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Nama Lengkap Aset</label>
                <input type="text" name="name" placeholder="Ex: Sucorinvest Money Market Fund"
                    class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:outline-none placeholder-slate-600 transition"
                    required>
            </div>

            <hr class="border-slate-700/50">

            {{-- 4. HARGA AWAL --}}
            <div>
                <label class="block text-xs font-bold text-emerald-400 uppercase mb-2">Harga Awal / NAV Unit</label>
                <div class="relative">
                    <span id="currencyLabel" class="absolute left-4 top-3.5 text-slate-400 font-bold"
                        x-text="assetType === 'Crypto' ? '$' : 'Rp'">Rp</span>
                    <input type="number" step="any" name="current_price" placeholder="0.00"
                        class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 pl-10 text-white font-mono text-lg focus:border-emerald-500 focus:outline-none transition"
                        required>
                </div>
                <p class="text-[10px] text-slate-500 mt-1">Gunakan titik (.) untuk desimal. Contoh: 15000.50</p>
            </div>

            {{-- 5. API ID (OPSIONAL) --}}
            <div class="bg-slate-900/50 p-4 rounded-xl border border-slate-700/50">
                <label class="block text-xs font-bold text-indigo-400 uppercase mb-2 flex items-center gap-2">
                    <i class="fas fa-link"></i> API ID (Opsional)
                </label>
                <input type="text" name="api_id" placeholder="Ex: bitcoin (untuk CoinGecko)"
                    class="w-full bg-slate-800 border border-slate-600 rounded-lg px-4 py-2 text-white text-sm focus:border-indigo-500 focus:outline-none placeholder-slate-600">
            </div>

            {{-- TOMBOL SUBMIT --}}
            <div class="pt-4 flex gap-4">
                <a href="{{ route('admin.assets.index') }}"
                    class="w-1/3 py-3.5 text-center text-slate-400 font-bold hover:text-white hover:bg-slate-700 rounded-xl transition">
                    Batal
                </a>
                <button type="submit"
                    class="w-2/3 bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-emerald-500/20 transition transform hover:-translate-y-1 flex justify-center items-center gap-2">
                    <i class="fas fa-save"></i> Simpan Aset Baru
                </button>
            </div>

        </form>
    </div>
</div>
@endsection