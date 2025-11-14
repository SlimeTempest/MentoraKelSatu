@extends('layouts.app', ['title' => 'Pengaturan Rekening'])

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Pengaturan Rekening Topup</h1>
        <p class="mt-1 text-sm text-gray-500">Kelola nomor rekening dan atas nama untuk topup saldo.</p>
    </div>

    <div class="rounded-lg bg-white p-8 shadow">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-6">
            @csrf

            <div class="grid gap-6 md:grid-cols-2">
                <div class="rounded-lg border border-gray-200 bg-gray-50 p-6">
                    <h2 class="mb-4 text-lg font-semibold text-gray-800">Rekening BCA</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="bca_number" class="mb-2 block text-sm font-medium text-gray-700">Nomor Rekening</label>
                            <input
                                type="text"
                                id="bca_number"
                                name="bca_number"
                                value="{{ old('bca_number', $settings['bca']['number']) }}"
                                required
                                class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                                placeholder="1234567890"
                            >
                            @error('bca_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="bca_name" class="mb-2 block text-sm font-medium text-gray-700">Atas Nama</label>
                            <input
                                type="text"
                                id="bca_name"
                                name="bca_name"
                                value="{{ old('bca_name', $settings['bca']['name']) }}"
                                required
                                class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                                placeholder="MentoraKelSatu"
                            >
                            @error('bca_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="rounded-lg border border-gray-200 bg-gray-50 p-6">
                    <h2 class="mb-4 text-lg font-semibold text-gray-800">Rekening Mandiri</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <label for="mandiri_number" class="mb-2 block text-sm font-medium text-gray-700">Nomor Rekening</label>
                            <input
                                type="text"
                                id="mandiri_number"
                                name="mandiri_number"
                                value="{{ old('mandiri_number', $settings['mandiri']['number']) }}"
                                required
                                class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                                placeholder="9876543210"
                            >
                            @error('mandiri_number')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="mandiri_name" class="mb-2 block text-sm font-medium text-gray-700">Atas Nama</label>
                            <input
                                type="text"
                                id="mandiri_name"
                                name="mandiri_name"
                                value="{{ old('mandiri_name', $settings['mandiri']['name']) }}"
                                required
                                class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                                placeholder="MentoraKelSatu"
                            >
                            @error('mandiri_name')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('dashboard') }}" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                    Batal
                </a>
                <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
@endsection

