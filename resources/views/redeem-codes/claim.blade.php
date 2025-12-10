@extends('layouts.app', ['title' => 'Klaim Redeem Code'])

@section('content')
    <div class="mx-auto max-w-2xl rounded-lg border border-gray-700 bg-gray-800 p-6 sm:p-8 shadow-lg">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-gray-100">Klaim Redeem Code</h1>
            <p class="mt-1 text-sm text-gray-400">Masukkan kode redeem code yang Anda terima dari dosen</p>
        </div>

        <form action="{{ route('redeem-codes.claim.store') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="code" class="mb-2 block text-sm font-semibold text-gray-300">Kode Redeem Code</label>
                <input
                    type="text"
                    id="code"
                    name="code"
                    value="{{ old('code') }}"
                    maxlength="8"
                    required
                    class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm font-mono text-gray-100 placeholder-gray-400 uppercase focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors"
                    placeholder="ABCD1234"
                    style="letter-spacing: 0.1em;"
                >
                <p class="mt-1.5 text-xs text-gray-400">Masukkan 8 karakter kode redeem code (huruf dan angka)</p>
                @error('code')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="rounded-lg border border-indigo-500/30 bg-indigo-500/10 p-4">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 flex-shrink-0 text-indigo-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-indigo-300 mb-1">Cara menggunakan Redeem Code:</p>
                        <ul class="text-xs text-indigo-400 space-y-1 list-disc list-inside">
                            <li>Dapatkan kode redeem code dari dosen Anda</li>
                            <li>Masukkan 8 karakter kode ke form di atas</li>
                            <li>Klik tombol "Klaim Code" untuk menambahkan saldo</li>
                            <li>Setiap kode hanya dapat digunakan sekali</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-gray-50 hover:bg-indigo-500 transition-all duration-200 hover:shadow-lg hover:scale-[1.02] active:scale-[0.98]">
                    Klaim Code
                </button>
            </div>
        </form>
    </div>

    <script>
        // Auto uppercase dan limit to 8 characters
        const codeInput = document.getElementById('code');
        if (codeInput) {
            codeInput.addEventListener('input', function(e) {
                this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 8);
            });
        }
    </script>
@endsection

