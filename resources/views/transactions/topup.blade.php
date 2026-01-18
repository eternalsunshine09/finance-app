@extends('layouts.app')

@section('title', 'Isi Saldo')

@section('content')
<div class="min-h-screen bg-slate-50 py-12">
    <div class="max-w-5xl mx-auto px-4">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">📥 Isi Saldo (Top Up)</h1>
            <p class="text-slate-500 mt-2">Tambah saldo ke dompet digital atau RDN Anda.</p>
        </div>

        @if ($errors->any())
        <div class="bg-red-50 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-r shadow-sm">
            <p class="font-bold">Periksa Kembali:</p>
            <ul class="list-disc ml-5 text-sm">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- KOLOM KIRI: FORMULIR --}}
            <div class="lg:col-span-2">
                <form action="{{ route('topup.process') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf

                    {{-- 1. PILIH DOMPET TUJUAN --}}
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">1. Dompet Tujuan
                        </h3>

                        <div class="relative">
                            <select name="wallet_id"
                                class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-slate-700 appearance-none cursor-pointer"
                                required>
                                <option value="" disabled selected>-- Pilih Akun --</option>
                                @foreach($wallets as $wallet)
                                <option value="{{ $wallet->id }}">
                                    {{ $wallet->bank_name }} - {{ $wallet->account_name }} ({{ $wallet->currency }})
                                </option>
                                @endforeach
                            </select>
                            <div class="absolute left-4 top-4 text-xl">💳</div>
                            <div class="absolute right-4 top-4 text-slate-400"><i class="fas fa-chevron-down"></i></div>
                        </div>
                    </div>

                    {{-- 2. NOMINAL TOP UP --}}
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100" x-data="{ amount: '' }">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">2. Nominal Top Up
                        </h3>

                        <div class="relative mb-4">
                            <span class="absolute left-4 top-4 text-slate-400 font-bold">Rp</span>
                            <input type="number" name="amount" x-model="amount"
                                class="w-full pl-12 pr-4 py-4 text-2xl font-black text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="0" min="10000" required>
                        </div>

                        {{-- Preset Buttons --}}
                        <div class="grid grid-cols-3 gap-3">
                            <button type="button" @click="amount = 100000"
                                class="py-2 px-3 rounded-lg border border-slate-200 text-slate-600 font-bold text-sm hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition">100rb</button>
                            <button type="button" @click="amount = 500000"
                                class="py-2 px-3 rounded-lg border border-slate-200 text-slate-600 font-bold text-sm hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition">500rb</button>
                            <button type="button" @click="amount = 1000000"
                                class="py-2 px-3 rounded-lg border border-slate-200 text-slate-600 font-bold text-sm hover:bg-indigo-50 hover:text-indigo-600 hover:border-indigo-200 transition">1
                                Juta</button>
                        </div>
                    </div>

                    {{-- 3. UPLOAD BUKTI --}}
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">3. Bukti Transfer
                        </h3>

                        <div
                            class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:bg-slate-50 transition relative">
                            <input type="file" name="payment_proof"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" required
                                onchange="previewImage(event)">
                            <div id="uploadPlaceholder">
                                <i class="fas fa-cloud-upload-alt text-4xl text-slate-300 mb-2"></i>
                                <p class="text-slate-500 font-medium">Klik untuk upload foto bukti</p>
                                <p class="text-xs text-slate-400 mt-1">Format: JPG, PNG (Max 2MB)</p>
                            </div>
                            <img id="imagePreview" class="hidden max-h-48 mx-auto rounded-lg shadow-sm" />
                        </div>
                    </div>

                    {{-- TOMBOL SUBMIT --}}
                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-indigo-500/30 transition transform active:scale-95 flex justify-center items-center gap-2">
                        <span>Kirim Permintaan Top Up</span>
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </div>

            {{-- KOLOM KANAN: INSTRUKSI TRANSFER --}}
            <div class="lg:col-span-1">
                <div class="bg-slate-800 text-white p-6 rounded-3xl shadow-xl sticky top-6">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-10 h-10 bg-indigo-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-info"></i>
                        </div>
                        <h3 class="font-bold text-lg">Instruksi Transfer</h3>
                    </div>

                    <div class="space-y-6">
                        <div>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Bank Tujuan</p>
                            <div class="flex items-center gap-2 mt-2">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/1200px-Bank_Central_Asia.svg.png"
                                    class="h-6 bg-white rounded p-1">
                                <span class="font-bold text-lg">BCA</span>
                            </div>
                        </div>

                        <div>
                            <p class="text-slate-400 text-xs font-bold uppercase tracking-widest">Nomor Rekening</p>
                            <div
                                class="flex items-center justify-between bg-slate-700/50 p-3 rounded-xl mt-2 border border-slate-600">
                                <span class="font-mono text-xl tracking-wider font-bold">0000</span>
                                <button onclick="copyToClipboard('883012345678')"
                                    class="text-indigo-400 hover:text-white transition" title="Salin">
                                    <i class="far fa-copy"></i>
                                </button>
                            </div>
                            <p class="text-xs text-slate-400 mt-2">a.n. PT Finance App Admin</p>
                        </div>

                        <div class="border-t border-slate-700 pt-4">
                            <p class="text-xs text-slate-300 leading-relaxed">
                                <i class="fas fa-exclamation-circle text-yellow-500 mr-1"></i>
                                Mohon transfer sesuai nominal hingga 3 digit terakhir agar verifikasi berjalan otomatis.
                                Admin akan memproses dalam 1x24 jam.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
// Script Preview Gambar
function previewImage(event) {
    const reader = new FileReader();
    reader.onload = function() {
        const output = document.getElementById('imagePreview');
        const placeholder = document.getElementById('uploadPlaceholder');
        output.src = reader.result;
        output.classList.remove('hidden');
        placeholder.classList.add('hidden');
    };
    reader.readAsDataURL(event.target.files[0]);
}

// Script Copy Rekening
function copyToClipboard(text) {
    navigator.clipboard.writeText(text);
    alert('Nomor rekening disalin!');
}
</script>
@endsection