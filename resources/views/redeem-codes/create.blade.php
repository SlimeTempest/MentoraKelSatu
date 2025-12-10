@extends('layouts.app', ['title' => 'Buat Redeem Code'])

@section('content')
    <div class="mx-auto max-w-2xl rounded-lg border border-gray-700 bg-gray-800 p-6 sm:p-8 shadow-lg">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-100">Buat Redeem Code</h1>
            <p class="mt-1 text-sm text-gray-400">Buat kode redeem untuk dibagikan ke mahasiswa</p>
        </div>

        <form action="{{ route('redeem-codes.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="amount" class="mb-2 block text-sm font-semibold text-gray-300">Jumlah Saldo</label>
                <input
                    type="number"
                    id="amount"
                    name="amount"
                    value="{{ old('amount') }}"
                    min="1000"
                    step="1000"
                    required
                    class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-gray-100 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors"
                    placeholder="Minimum Rp 1.000"
                >
                <p class="mt-1.5 text-xs text-gray-400">Minimum: Rp 1.000</p>
                @error('amount')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="expires_at" class="mb-2 block text-sm font-semibold text-gray-300">Tanggal Kadaluarsa (Opsional)</label>
                <input
                    type="datetime-local"
                    id="expires_at"
                    name="expires_at"
                    value="{{ old('expires_at') }}"
                    class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-gray-100 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors"
                >
                <p class="mt-1.5 text-xs text-gray-400">Kosongkan jika tidak ingin ada batas waktu</p>
                @error('expires_at')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-lg border border-indigo-500/30 bg-indigo-500/10 p-4">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 flex-shrink-0 text-indigo-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-sm text-indigo-300">
                        <strong class="font-semibold">Info:</strong> Redeem code akan dibuat secara otomatis dengan kode unik 8 karakter. Kode ini dapat digunakan oleh mahasiswa untuk menambah saldo mereka.
                    </p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="submit" class="flex-1 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-gray-50 hover:bg-indigo-500 transition-all duration-200 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]">
                    Buat Redeem Code
                </button>
                <a href="{{ route('redeem-codes.index') }}" class="rounded-lg border border-gray-600 bg-gray-700 px-5 py-2.5 text-center text-sm font-semibold text-gray-300 hover:bg-gray-600 hover:text-gray-100 transition-all duration-200">
                    Batal
                </a>
            </div>
        </form>
    </div>
@endsection

