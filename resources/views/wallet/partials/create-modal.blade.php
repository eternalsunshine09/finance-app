{{-- Modal Create --}}
<div x-show="showCreateModal" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center p-4">
    {{-- Backdrop Gelap --}}
    <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm" @click="showCreateModal = false" x-transition.opacity>
    </div>

    {{-- Konten Modal --}}
    <div class="relative bg-white rounded-[2.5rem] w-full max-w-md shadow-2xl p-8 transform transition-all"
        x-transition.scale>
        <div class="flex justify-between items-center mb-8">
            <div>
                <h3 class="text-2xl font-black text-slate-800">Dompet Baru</h3>
                <p class="text-slate-400 text-sm mt-1">Tambahkan akun sumber dana.</p>
            </div>
            <button @click="showCreateModal = false"
                class="w-10 h-10 rounded-full bg-slate-50 hover:bg-slate-100 flex items-center justify-center text-slate-400 transition">✖</button>
        </div>

        <form action="{{ route('wallet.store') }}" method="POST" class="space-y-6">
            @csrf
            <div class="space-y-5">
                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Nama Akun /
                        Label</label>
                    <input type="text" name="account_name" placeholder="Contoh: Tabungan USD, RDN Bibit"
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-indigo-500 font-bold text-slate-700 focus:outline-none transition"
                        required>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Mata Uang</label>
                        <select name="currency"
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-indigo-500 font-bold text-slate-700 focus:outline-none transition">
                            <option value="IDR">🇮🇩 IDR (Rp)</option>
                            <option value="USD">🇺🇸 USD ($)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Bank /
                            Platform</label>
                        <input type="text" name="bank_name" placeholder="BCA, PayPal"
                            class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-indigo-500 font-bold text-slate-700 focus:outline-none transition"
                            required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-extrabold text-slate-500 uppercase mb-2 ml-1">Nomor Rekening
                        (Opsional)</label>
                    <input type="number" name="account_number" placeholder="1234567890"
                        class="w-full px-5 py-4 bg-slate-50 border border-slate-200 rounded-2xl focus:border-indigo-500 font-bold text-slate-700 font-mono focus:outline-none transition">
                </div>
            </div>

            <button type="submit"
                class="w-full bg-indigo-600 text-white font-bold text-lg py-4 rounded-2xl shadow-xl shadow-indigo-500/30 hover:bg-indigo-700 hover:scale-[1.02] transition-all">
                Buat Dompet Sekarang
            </button>
        </form>
    </div>
</div>