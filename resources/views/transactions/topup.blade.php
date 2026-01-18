@extends('layouts.app')

@section('title', 'Isi Saldo')

@section('content')
<div class="min-h-screen bg-slate-50 py-12">
    <div class="max-w-5xl mx-auto px-4">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">📥 Isi Saldo (Top Up)</h1>
            <p class="text-slate-500 mt-2">Silakan transfer dana ke rekening resmi Finance App di bawah ini.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- KOLOM KIRI: FORMULIR --}}
            <div class="lg:col-span-2">
                <form action="{{ route('topup.process') }}" method="POST" enctype="multipart/form-data"
                    class="space-y-6">
                    @csrf

                    {{-- 1. PILIH AKUN TUJUAN --}}
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">1. Pilih Akun Tujuan
                        </h3>
                        <div class="relative">
                            <select name="wallet_id" id="walletSelect"
                                class="w-full pl-12 pr-4 py-4 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 font-bold text-slate-700 appearance-none cursor-pointer"
                                required>
                                <option value="" disabled selected>-- Pilih Dompet --</option>
                                @foreach($wallets as $wallet)
                                <option value="{{ $wallet->id }}" data-currency="{{ $wallet->currency }}">
                                    {{ $wallet->bank_name }} - {{ $wallet->account_name }} ({{ $wallet->currency }})
                                </option>
                                @endforeach
                            </select>
                            <div class="absolute left-4 top-4 text-xl">💳</div>
                            <div class="absolute right-4 top-4 text-slate-400"><i class="fas fa-chevron-down"></i></div>
                        </div>
                    </div>

                    {{-- 2. NOMINAL --}}
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100" x-data="{ amount: '' }">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">2. Nominal Deposit
                        </h3>

                        <div class="relative mb-4">
                            <span id="currencySymbol" class="absolute left-4 top-4 text-slate-400 font-bold">Rp</span>

                            {{-- 🔥 PERBAIKAN: step="any" agar bisa input desimal (0.7, 15.04) --}}
                            <input type="number" step="any" name="amount" x-model="amount"
                                class="w-full pl-12 pr-4 py-4 text-2xl font-black text-slate-800 bg-slate-50 border border-slate-200 rounded-xl focus:ring-indigo-500 focus:border-indigo-500"
                                placeholder="0.00" required>
                        </div>

                        {{-- Preset Tombol (Hanya muncul untuk IDR) --}}
                        <div id="idrPresets" class="grid grid-cols-3 gap-3 hidden">
                            <button type="button" @click="amount = 100000"
                                class="py-2 px-3 rounded-lg border border-slate-200 text-slate-600 font-bold text-sm hover:bg-indigo-50 hover:text-indigo-600 transition">100rb</button>
                            <button type="button" @click="amount = 500000"
                                class="py-2 px-3 rounded-lg border border-slate-200 text-slate-600 font-bold text-sm hover:bg-indigo-50 hover:text-indigo-600 transition">500rb</button>
                            <button type="button" @click="amount = 1000000"
                                class="py-2 px-3 rounded-lg border border-slate-200 text-slate-600 font-bold text-sm hover:bg-indigo-50 hover:text-indigo-600 transition">1
                                Juta</button>
                        </div>
                    </div>

                    {{-- 3. UPLOAD BUKTI --}}
                    <div class="bg-white p-6 rounded-3xl shadow-sm border border-slate-100">
                        <h3 class="text-sm font-bold text-slate-400 uppercase tracking-widest mb-4">3. Upload Bukti
                            Transfer</h3>

                        <div
                            class="border-2 border-dashed border-slate-300 rounded-xl p-6 text-center hover:bg-slate-50 transition relative group">
                            <input type="file" name="payment_proof"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required
                                onchange="previewImage(event)">
                            <div id="uploadPlaceholder">
                                <div
                                    class="w-12 h-12 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-3">
                                    <i class="fas fa-camera text-xl"></i>
                                </div>
                                <p class="text-slate-600 font-bold">Klik untuk Pilih Foto</p>
                                <p class="text-xs text-slate-400 mt-1">Format: JPG, PNG (Max 2MB)</p>
                            </div>
                            <img id="imagePreview" class="hidden max-h-48 mx-auto rounded-lg shadow-sm z-0 relative" />
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-4 rounded-xl shadow-lg transition transform active:scale-95 flex justify-center items-center gap-2">
                        <span>Konfirmasi & Kirim</span>
                        <i class="fas fa-check-circle"></i>
                    </button>
                </form>
            </div>

            {{-- KOLOM KANAN: INSTRUKSI TRANSFER DINAMIS --}}
            <div class="lg:col-span-1">
                <div class="bg-slate-800 text-white p-6 rounded-3xl shadow-xl sticky top-6 border border-slate-700">

                    {{-- Judul --}}
                    <div class="flex items-center gap-3 mb-6">
                        <div
                            class="w-10 h-10 bg-emerald-500 rounded-full flex items-center justify-center shadow-lg shadow-emerald-500/50">
                            <i class="fas fa-university"></i>
                        </div>
                        <h3 class="font-bold text-lg">Rekening Tujuan</h3>
                    </div>

                    {{-- Card Info Rekening (Berubah via JS) --}}
                    <div id="bankInfoCard" class="bg-slate-700/50 p-4 rounded-xl border border-slate-600 mb-6">
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-2">Bank Transfer</p>

                        {{-- Logo Bank --}}
                        <div class="flex items-center gap-2 mb-3">
                            <img id="bankLogo"
                                src="https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/1200px-Bank_Central_Asia.svg.png"
                                class="h-8 bg-white rounded px-2 py-1">
                            <span id="bankName" class="font-bold text-lg">BCA</span>
                        </div>

                        {{-- Nomer Rekening --}}
                        <div class="flex justify-between items-center bg-slate-800 p-3 rounded-lg border border-slate-600 cursor-pointer hover:bg-slate-900 transition"
                            onclick="copyRekening()">
                            <span id="bankRekening"
                                class="font-mono text-xl tracking-wider font-bold text-emerald-400">8830123456</span>
                            <i class="far fa-copy text-slate-400"></i>
                        </div>
                        <p class="text-xs text-slate-400 mt-2">a.n. PT Finance App Admin</p>
                    </div>

                    {{-- Alur Transaksi --}}
                    <div class="border-t border-slate-700 pt-6">
                        <p class="text-slate-400 text-xs font-bold uppercase tracking-widest mb-4">Alur Transaksi</p>
                        <ul class="space-y-4 relative">
                            <div class="absolute left-3 top-2 bottom-4 w-0.5 bg-slate-700 -z-10"></div>

                            <li class="flex gap-4">
                                <span
                                    class="w-6 h-6 rounded-full bg-slate-600 flex items-center justify-center text-xs font-bold ring-4 ring-slate-800">1</span>
                                <div class="text-sm text-slate-300">
                                    <span class="font-bold text-white block">Transfer Dana</span>
                                    Kirim uang ke rekening di atas sesuai nominal.
                                </div>
                            </li>
                            <li class="flex gap-4">
                                <span
                                    class="w-6 h-6 rounded-full bg-slate-600 flex items-center justify-center text-xs font-bold ring-4 ring-slate-800">2</span>
                                <div class="text-sm text-slate-300">
                                    <span class="font-bold text-white block">Upload Bukti</span>
                                    Foto/Screenshot bukti transfer lalu upload.
                                </div>
                            </li>
                            <li class="flex gap-4">
                                <span
                                    class="w-6 h-6 rounded-full bg-yellow-500/20 text-yellow-500 flex items-center justify-center text-xs font-bold ring-4 ring-slate-800">3</span>
                                <div class="text-sm text-slate-300">
                                    <span class="font-bold text-yellow-400 block">Menunggu Verifikasi</span>
                                    Status transaksi menjadi <span
                                        class="bg-yellow-500/20 text-yellow-400 px-1 rounded text-xs">Pending</span>.
                                </div>
                            </li>
                            <li class="flex gap-4">
                                <span
                                    class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-500 flex items-center justify-center text-xs font-bold ring-4 ring-slate-800">4</span>
                                <div class="text-sm text-slate-300">
                                    <span class="font-bold text-emerald-400 block">Selesai</span>
                                    Admin menyetujui, saldo masuk otomatis.
                                </div>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
const walletSelect = document.getElementById('walletSelect');
const currencySymbol = document.getElementById('currencySymbol');
const idrPresets = document.getElementById('idrPresets');

// Elemen Info Rekening Kanan
const bankLogo = document.getElementById('bankLogo');
const bankName = document.getElementById('bankName');
const bankRekening = document.getElementById('bankRekening');

// LOGIC GANTI INFO REKENING
walletSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const currency = selectedOption.getAttribute('data-currency');

    // 1. Ubah Simbol Input
    currencySymbol.innerText = (currency === 'USD') ? '$' : 'Rp';

    // 2. Tampilkan Preset cuma kalau IDR
    if (currency === 'IDR') {
        idrPresets.classList.remove('hidden');

        // Info Rekening BCA
        bankName.innerText = "BCA";
        bankRekening.innerText = "8830123456";
        bankLogo.src =
            "https://upload.wikimedia.org/wikipedia/commons/thumb/5/5c/Bank_Central_Asia.svg/1200px-Bank_Central_Asia.svg.png";

    } else {
        // Kalau USD
        idrPresets.classList.add('hidden');

        // Info Rekening Luar Negeri / SWIFT
        bankName.innerText = "US Bank / SWIFT";
        bankRekening.innerText = "US8830567890"; // Contoh Nomor Rekening Luar
        bankLogo.src =
            "https://upload.wikimedia.org/wikipedia/commons/thumb/c/cc/Bank_of_America_logo.svg/2560px-Bank_of_America_logo.svg.png"; // Contoh Logo Bank US
    }
});

function copyRekening() {
    const text = document.getElementById('bankRekening').innerText;
    navigator.clipboard.writeText(text);
    alert('Nomor rekening ' + text + ' disalin!');
}

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
</script>
@endsection