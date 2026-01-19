@extends('layouts.admin')

@section('title', 'Tambah Aset Baru')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('admin.assets.index') }}"
            class="text-slate-400 hover:text-white text-sm mb-4 inline-flex items-center gap-2 transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar Aset
        </a>
        <h1 class="text-3xl font-black text-white">Tambah Aset Baru</h1>
        <p class="text-slate-400 mt-2">Daftarkan instrumen investasi baru ke dalam sistem.</p>
    </div>

    {{-- Form Card --}}
    <div class="bg-slate-800 rounded-3xl p-8 border border-slate-700 shadow-2xl relative overflow-hidden" x-data="{ 
            assetType: '', 
            currencySymbol: 'Rp',
            updateCurrency() {
                this.currencySymbol = (this.assetType === 'Crypto') ? '$' : 'Rp';
            }
        }">

        {{-- Hiasan Background --}}
        <div
            class="absolute top-0 right-0 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl -mr-16 -mt-16 pointer-events-none">
        </div>

        <form action="{{ route('admin.assets.store') }}" method="POST" class="space-y-6 relative z-10">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- 1. KODE SIMBOL --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Kode Simbol (Ticker)</label>
                    <input type="text" name="symbol" placeholder="Ex: BBCA, BTC, SUCOR-MM"
                        class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white font-bold text-lg focus:border-indigo-500 focus:outline-none uppercase placeholder-slate-600 transition"
                        required>
                    <p class="text-[10px] text-slate-500 mt-1">Gunakan kode unik untuk identifikasi.</p>
                </div>

                {{-- 2. KATEGORI UTAMA --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Kategori Aset</label>
                    <div class="relative">
                        <select name="type" x-model="assetType" @change="updateCurrency()"
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

            {{-- 🔥 SUB-KATEGORI (Khusus Reksa Dana) --}}
            <div x-show="assetType === 'Mutual Fund'" x-transition
                class="bg-indigo-500/10 border border-indigo-500/30 p-4 rounded-xl">
                <label class="block text-xs font-bold text-indigo-300 uppercase mb-2">Jenis Reksa Dana</label>
                <select name="subtype"
                    class="w-full bg-slate-900 border border-indigo-500/50 rounded-xl px-4 py-3 text-white focus:border-indigo-400 focus:outline-none appearance-none cursor-pointer font-medium">
                    <option value="" disabled selected>-- Pilih Jenis RD --</option>
                    <option value="RDPU">Pasar Uang (RDPU)</option>
                    <option value="RDPT">Pendapatan Tetap (RDPT)</option>
                    <option value="RDS">Saham (RDS)</option>
                    <option value="Campuran">Campuran</option>
                </select>
            </div>

            {{-- 3. NAMA ASET --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Nama Lengkap Aset</label>
                <input type="text" name="name" placeholder="Ex: Bank Central Asia Tbk"
                    class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:outline-none placeholder-slate-600 transition"
                    required>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-start">

                {{-- 4. API ID --}}
                <div>
                    <label class="block text-xs font-bold text-indigo-400 uppercase mb-2 flex items-center gap-2">
                        <i class="fas fa-link"></i> API ID (Untuk Auto Sync)
                    </label>
                    <input type="text" name="api_id" placeholder="Isi ID CoinGecko / Yahoo"
                        class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:outline-none placeholder-slate-600 transition">

                    {{-- Cheat Sheet Kecil --}}
                    <div class="mt-2 bg-slate-700/50 p-2 rounded-lg text-[10px] text-slate-300 space-y-1">
                        <p><strong class="text-orange-400">Crypto:</strong> Gunakan ID CoinGecko (contoh:
                            <code>bitcoin</code>, <code>ethereum</code>)</p>
                        <p><strong class="text-blue-400">Saham Indo:</strong> Kode Emiten (contoh: <code>BBCA</code>,
                            <code>TLKM</code>)</p>
                        <p><strong class="text-white">Lainnya:</strong> Kosongkan jika update manual.</p>
                    </div>
                </div>

                {{-- 5. HARGA SAAT INI --}}
                <div>
                    <label class="block text-xs font-bold text-emerald-400 uppercase mb-2">Harga Saat Ini</label>
                    <div class="relative">
                        <span class="absolute left-4 top-3.5 text-slate-400 font-bold" x-text="currencySymbol">Rp</span>
                        <input type="number" step="any" name="current_price" placeholder="0.00"
                            class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 pl-10 text-white font-mono text-lg focus:border-emerald-500 focus:outline-none transition"
                            required>
                    </div>
                    <p class="text-[10px] text-slate-500 mt-1">Gunakan titik (.) untuk desimal.</p>
                </div>
            </div>

            <hr class="border-slate-700/50 my-6">

            {{-- TOMBOL ACTION --}}
            <div class="flex gap-4">
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