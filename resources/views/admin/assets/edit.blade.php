@extends('layouts.admin')

@section('title', 'Edit Aset')

@section('content')
<div class="max-w-3xl mx-auto">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('admin.assets.index') }}"
            class="text-slate-400 hover:text-white text-sm mb-4 inline-flex items-center gap-2 transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Daftar
        </a>
        <h1 class="text-3xl font-black text-white">Edit Data Aset</h1>
        <p class="text-slate-400 mt-2">Perbarui detail aset {{ $asset->symbol }}.</p>
    </div>

    {{-- Form Card --}}
    <div class="bg-slate-800 rounded-3xl p-8 border border-slate-700 shadow-2xl relative overflow-hidden"
        x-data="{ assetType: '{{ $asset->type }}' }">

        <form action="{{ route('admin.assets.update', $asset->id) }}" method="POST" class="space-y-6 relative z-10">
            @csrf
            @method('PUT') {{-- PENTING: Method PUT untuk Update --}}

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- KODE SIMBOL --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Kode Simbol</label>
                    <input type="text" name="symbol" value="{{ old('symbol', $asset->symbol) }}"
                        class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white font-bold text-lg uppercase focus:border-indigo-500 focus:outline-none transition"
                        required>
                </div>

                {{-- KATEGORI --}}
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Kategori</label>
                    <select name="type" x-model="assetType"
                        class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:outline-none font-medium transition">
                        <option value="Stock">🏢 Saham (Stock)</option>
                        <option value="Crypto">₿ Crypto Asset</option>
                        <option value="Mutual Fund">📈 Reksa Dana</option>
                        <option value="Gold">🥇 Emas</option>
                        <option value="Currency">💱 Forex</option>
                    </select>
                </div>
            </div>

            {{-- SUB-KATEGORI (Reksa Dana) --}}
            <div x-show="assetType === 'Mutual Fund'"
                class="bg-indigo-500/10 border border-indigo-500/30 p-4 rounded-xl">
                <label class="block text-xs font-bold text-indigo-300 uppercase mb-2">Jenis Reksa Dana</label>
                <select name="subtype"
                    class="w-full bg-slate-900 border border-indigo-500/50 rounded-xl px-4 py-3 text-white focus:border-indigo-400 focus:outline-none">
                    <option value="">-- Pilih Jenis --</option>
                    @foreach(['RDPU', 'RDPT', 'RDS', 'Campuran'] as $t)
                    <option value="{{ $t }}" {{ $asset->subtype == $t ? 'selected' : '' }}>{{ $t }}</option>
                    @endforeach
                </select>
            </div>

            {{-- NAMA ASET --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-2">Nama Lengkap</label>
                <input type="text" name="name" value="{{ old('name', $asset->name) }}"
                    class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:outline-none transition"
                    required>
            </div>

            {{-- URL LOGO --}}
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-2">URL Logo</label>
                <div class="flex gap-2">
                    <input type="text" name="logo" value="{{ old('logo', $asset->logo) }}" placeholder="https://..."
                        class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:outline-none transition">

                    {{-- Preview Logo Saat Ini --}}
                    <div
                        class="w-12 h-12 bg-slate-900 border border-slate-600 rounded-xl flex-shrink-0 flex items-center justify-center overflow-hidden">
                        @if($asset->logo)
                        <img src="{{ $asset->logo }}" class="w-full h-full object-cover">
                        @else
                        <i class="fas fa-image text-slate-500"></i>
                        @endif
                    </div>
                </div>
            </div>

            {{-- API ID --}}
            <div>
                <label class="block text-xs font-bold text-indigo-400 uppercase mb-2 flex items-center gap-2">
                    <i class="fas fa-link"></i> API ID (Sync)
                </label>
                <input type="text" name="api_id" value="{{ old('api_id', $asset->api_id) }}"
                    class="w-full bg-slate-900 border border-slate-600 rounded-xl px-4 py-3 text-white focus:border-indigo-500 focus:outline-none transition">
            </div>

            <hr class="border-slate-700/50 my-6">

            <div class="flex gap-4">
                <a href="{{ route('admin.assets.index') }}"
                    class="w-1/3 py-3.5 text-center text-slate-400 font-bold hover:bg-slate-700 rounded-xl transition">Batal</a>
                <button type="submit"
                    class="w-2/3 bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3.5 rounded-xl shadow-lg transition">
                    <i class="fas fa-save mr-2"></i> Update Data
                </button>
            </div>
        </form>
    </div>
</div>
@endsection