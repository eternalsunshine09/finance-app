@extends('layouts.admin')

@php
$formView = match($type) {
'Mutual Fund' => 'mutual_fund',
'Crypto' => 'crypto',
'US Stock' => 'us_stock', // 🔥 TAMBAHAN BARU
default => 'stock',
};

$labelMap = [
'Stock' => 'Saham Indonesia',
'US Stock' => 'Saham Amerika', // 🔥 TAMBAHAN BARU
'Mutual Fund' => 'Reksadana',
'Crypto' => 'Crypto Asset',
'Gold' => 'Emas',
'Currency' => 'Valas'
];
$label = $labelMap[$type] ?? $type;
@endphp

@section('title', "Edit $label")
@section('header', "Edit Data $label")

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Edit Data {{ $label }}</h1>
            <p class="text-sm text-gray-500">Memperbarui data <span
                    class="font-bold text-black">{{ $asset->symbol }}</span>.</p>
        </div>
        <a href="{{ route('admin.assets.index', ['type' => $type]) }}"
            class="text-sm font-bold text-gray-500 hover:text-black transition flex items-center gap-2 bg-white px-4 py-2 rounded-lg border border-gray-200">
            ← Kembali
        </a>
    </div>

    {{-- Form Wrapper --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-lg p-8">
        <form action="{{ route('admin.assets.update', $asset->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Input Hidden Tipe --}}
            <input type="hidden" name="type" value="{{ $type }}">

            {{-- Include Form Spesifik --}}
            @include("admin.assets.forms.{$formView}")

            {{-- Tombol Simpan --}}
            <div class="pt-6 border-t border-gray-100 flex justify-end gap-3">
                <a href="{{ route('admin.assets.index', ['type' => $type]) }}"
                    class="px-6 py-3 rounded-xl text-gray-500 hover:bg-gray-100 font-bold transition">Batal</a>
                <button type="submit"
                    class="px-6 py-3 rounded-xl bg-black text-white font-bold hover:bg-gray-800 transition shadow-lg transform active:scale-95">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection