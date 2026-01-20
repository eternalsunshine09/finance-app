@extends('layouts.app')

@section('header', 'Input Dividen Tunai')
@section('header_description', 'Catat pemasukan dividen dari saham yang Anda miliki.')

@section('content')
<div class="max-w-lg mx-auto">
    <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200">

        <form action="{{ route('transactions.process_ca') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="action_type" value="DIV_CASH">

            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Pilih Saham</label>
                <div class="relative">
                    <select name="asset_symbol"
                        class="block w-full rounded-lg border-gray-300 bg-gray-50 text-gray-900 focus:border-black focus:ring-black sm:text-sm p-3 border appearance-none"
                        required>
                        <option value="" disabled selected>-- Pilih Saham dari Portfolio --</option>

                        @forelse($portfolios as $porto)
                        <option value="{{ $porto->asset_symbol }}">
                            {{ $porto->asset_symbol }}
                            @if($porto->asset && $porto->asset->name)
                            - {{ Str::limit($porto->asset->name, 20) }}
                            @endif
                            ({{ number_format($porto->quantity, 0) }} Unit)
                        </option>
                        @empty
                        <option value="" disabled>Anda belum memiliki saham di portfolio.</option>
                        @endforelse
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-500">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </div>
                </div>
                @if($portfolios->isEmpty())
                <p class="text-xs text-red-500 mt-1">* Anda harus membeli saham terlebih dahulu sebelum mencatat
                    dividen.</p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-900 mb-2">Masuk ke Dompet</label>
                <div class="grid grid-cols-1 gap-3">
                    @foreach($wallets as $wallet)
                    <label
                        class="relative flex cursor-pointer rounded-lg border border-gray-200 bg-white p-4 shadow-sm focus:outline-none hover:border-black transition-colors">
                        <input type="radio" name="wallet_id" value="{{ $wallet->id }}" class="sr-only" required>
                        <span class="flex flex-1">
                            <span class="flex flex-col">
                                <span class="block text-sm font-medium text-gray-900">{{ $wallet->bank_name }}</span>
                                <span
                                    class="mt-1 flex items-center text-xs text-gray-500">{{ $wallet->account_name }}</span>
                            </span>
                        </span>
                        <svg class="h-5 w-5 text-black hidden checked-icon" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd" />
                        </svg>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Total Dividen (Rp)</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                            <span class="text-gray-500 sm:text-sm">Rp</span>
                        </div>
                        <input type="number" name="amount_received"
                            class="block w-full rounded-lg border-gray-300 pl-10 focus:border-black focus:ring-black sm:text-sm p-2.5 border bg-gray-50"
                            placeholder="0" required min="1">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-900 mb-2">Tanggal Cair</label>
                    <input type="date" name="date"
                        class="block w-full rounded-lg border-gray-300 focus:border-black focus:ring-black sm:text-sm p-2.5 border bg-gray-50"
                        value="{{ date('Y-m-d') }}" required>
                </div>
            </div>

            <div class="pt-4">
                <button type="submit"
                    class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-black hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-all">
                    Simpan Data Dividen
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* CSS Sederhana untuk Radio Button aktif */
input[type="radio"]:checked+span+svg {
    display: block;
}

input[type="radio"]:checked~span {
    color: black;
}

input[type="radio"]:checked {
    border-color: black;
}

label:has(input[type="radio"]:checked) {
    border-color: #000;
    background-color: #f9fafb;
}
</style>
@endsection