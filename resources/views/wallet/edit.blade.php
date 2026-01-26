@extends('layouts.app')
@section('title', 'Edit Dompet')

@section('content')
<div class="max-w-lg mx-auto py-10">

    {{-- Tombol Kembali --}}
    <div class="mb-8">
        <a href="{{ route('wallet.index') }}"
            class="text-gray-500 hover:text-black font-bold text-sm inline-flex items-center gap-2 transition group">
            <div
                class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center group-hover:border-black transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </div>
            Kembali ke Daftar
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 relative overflow-hidden">
        <div class="flex justify-between items-center mb-8 pb-6 border-b border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Edit Dompet</h1>
                <p class="text-gray-500 text-xs mt-1">ID: #{{ $wallet->id }}</p>
            </div>
            <div
                class="w-10 h-10 bg-gray-50 rounded-full flex items-center justify-center border border-gray-100 text-gray-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z">
                    </path>
                </svg>
            </div>
        </div>

        <form action="{{ route('wallet.update', $wallet->id) }}" method="POST" class="space-y-6">
            @csrf @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Platform / Bank</label>
                    <input type="text" name="bank_name" value="{{ $wallet->bank_name }}"
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-1 focus:ring-black focus:border-black transition font-bold text-gray-800"
                        required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Label Akun</label>
                    <input type="text" name="account_name" value="{{ $wallet->account_name }}"
                        class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-1 focus:ring-black focus:border-black transition font-bold text-gray-800"
                        required>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Nomor Rekening</label>
                <input type="text" name="account_number" value="{{ $wallet->account_number }}"
                    class="w-full px-4 py-3 bg-white border border-gray-200 rounded-xl focus:ring-1 focus:ring-black focus:border-black transition font-mono text-gray-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-gray-400 uppercase mb-2 ml-1">Mata Uang (Terkunci)</label>
                <input type="text" value="{{ $wallet->currency }}"
                    class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl font-bold text-gray-400 cursor-not-allowed"
                    disabled>
            </div>

            <div class="pt-4 flex gap-4">
                <button type="submit"
                    class="flex-1 bg-black text-white font-bold py-3.5 rounded-xl hover:bg-gray-800 transition shadow-md">Simpan
                    Perubahan</button>
            </div>
        </form>

        <div class="mt-8 pt-6 border-t border-gray-100 flex justify-center">
            <form action="{{ route('wallet.destroy', $wallet->id) }}" method="POST"
                onsubmit="return confirm('Hapus dompet ini beserta semua riwayatnya? Tindakan ini tidak dapat dibatalkan.')">
                @csrf @method('DELETE')
                <button type="submit"
                    class="text-red-500 text-xs font-bold hover:text-red-700 hover:underline flex items-center gap-1 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                        </path>
                    </svg>
                    Hapus Dompet Ini
                </button>
            </form>
        </div>
    </div>
</div>
@endsection