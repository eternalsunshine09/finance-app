@extends('layouts.app')
@section('title', 'Edit Dompet')

@section('content')
<div class="min-h-screen bg-slate-50 pt-12 pb-12">
    <div class="max-w-2xl mx-auto px-4">

        <a href="{{ route('wallet.index') }}"
            class="text-slate-500 font-bold hover:text-indigo-600 mb-6 inline-flex items-center gap-2 transition">
            ⬅ Kembali ke Daftar
        </a>

        <div class="bg-white rounded-[2.5rem] shadow-xl p-8 md:p-12">
            <div class="flex justify-between items-center mb-8 border-b border-slate-100 pb-6">
                <div>
                    <h1 class="text-3xl font-black text-slate-800">Edit Dompet</h1>
                    <p class="text-slate-400 mt-1">Perbarui informasi rekening {{ $wallet->account_name }}.</p>
                </div>
                <div class="w-12 h-12 bg-indigo-50 rounded-2xl flex items-center justify-center text-2xl">✏️</div>
            </div>

            <form action="{{ route('wallet.update', $wallet->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-500 uppercase ml-1">Nama Akun</label>
                        <input type="text" name="account_name" value="{{ $wallet->account_name }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 font-bold text-slate-700 focus:outline-none focus:border-indigo-500 transition"
                            required>
                    </div>
                    <div class="space-y-2">
                        <label class="text-xs font-extrabold text-slate-500 uppercase ml-1">Bank / Platform</label>
                        <input type="text" name="bank_name" value="{{ $wallet->bank_name }}"
                            class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 font-bold text-slate-700 focus:outline-none focus:border-indigo-500 transition"
                            required>
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="text-xs font-extrabold text-slate-500 uppercase ml-1">Nomor Rekening</label>
                    <input type="number" name="account_number" value="{{ $wallet->account_number }}"
                        class="w-full bg-slate-50 border border-slate-200 rounded-2xl px-5 py-4 font-bold text-slate-700 font-mono focus:outline-none focus:border-indigo-500 transition">
                </div>

                <div class="space-y-2 opacity-50 cursor-not-allowed">
                    <label class="text-xs font-extrabold text-slate-500 uppercase ml-1">Mata Uang (Tidak dapat
                        diubah)</label>
                    <input type="text" value="{{ $wallet->currency }}"
                        class="w-full bg-slate-100 border border-slate-200 rounded-2xl px-5 py-4 font-bold text-slate-500"
                        disabled>
                </div>

                <div class="pt-6 flex gap-4">
                    <button type="submit"
                        class="flex-1 bg-indigo-600 text-white font-bold text-lg py-4 rounded-2xl hover:bg-indigo-700 hover:scale-[1.02] transition shadow-lg shadow-indigo-200">
                        Simpan Perubahan
                    </button>
                </div>
            </form>

            <div class="mt-8 pt-8 border-t border-slate-100 text-center">
                <form action="{{ route('wallet.destroy', $wallet->id) }}" method="POST"
                    onsubmit="return confirm('Yakin ingin menghapus dompet ini secara permanen?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="text-rose-500 font-bold hover:text-rose-700 text-sm py-2 px-4 rounded-xl hover:bg-rose-50 transition">
                        🗑 Hapus Dompet Ini
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection