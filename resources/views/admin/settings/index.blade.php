@extends('layouts.app', ['title' => 'Pengaturan Rekening'])

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Pengaturan Rekening Topup</h1>
        <p class="mt-1 text-sm text-gray-400">Kelola nomor rekening dan atas nama untuk topup saldo.</p>
    </div>

    <div class="rounded-lg border border-gray-700 bg-gray-800 p-8 shadow-lg">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid gap-6 md:grid-cols-2">
                <div class="rounded-lg border border-gray-700 bg-gray-800/50 p-6">
                    <h2 class="mb-4 text-lg font-semibold text-white">Rekening BCA</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="bca_number" class="mb-2 block text-sm font-medium text-gray-300">Nomor Rekening</label>
                            <input
                                type="text"
                                id="bca_number"
                                name="bca_number"
                                value="{{ old('bca_number', $settings['bca']['number']) }}"
                                required
                                class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors"
                                placeholder="Masukkan nomor rekening BCA"
                            >
                            @error('bca_number')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bca_name" class="mb-2 block text-sm font-medium text-gray-300">Atas Nama</label>
                            <input
                                type="text"
                                id="bca_name"
                                name="bca_name"
                                value="{{ old('bca_name', $settings['bca']['name']) }}"
                                required
                                class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors"
                                placeholder="Masukkan nama pemilik rekening"
                            >
                            @error('bca_name')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-700 bg-gray-800/50 p-6">
                    <h2 class="mb-4 text-lg font-semibold text-white">Rekening Mandiri</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="mandiri_number" class="mb-2 block text-sm font-medium text-gray-300">Nomor Rekening</label>
                            <input
                                type="text"
                                id="mandiri_number"
                                name="mandiri_number"
                                value="{{ old('mandiri_number', $settings['mandiri']['number']) }}"
                                required
                                class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors"
                                placeholder="Masukkan nomor rekening Mandiri"
                            >
                            @error('mandiri_number')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mandiri_name" class="mb-2 block text-sm font-medium text-gray-300">Atas Nama</label>
                            <input
                                type="text"
                                id="mandiri_name"
                                name="mandiri_name"
                                value="{{ old('mandiri_name', $settings['mandiri']['name']) }}"
                                required
                                class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors"
                                placeholder="Masukkan nama pemilik rekening"
                            >
                            @error('mandiri_name')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 text-sm font-semibold text-gray-300 transition-all duration-200 hover:bg-gray-600 hover:text-white">
                    Batal
                </a>
                <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition-all duration-200 hover:bg-blue-500 hover:shadow-lg">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection

