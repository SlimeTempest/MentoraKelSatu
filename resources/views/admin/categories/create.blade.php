@extends('layouts.app', ['title' => 'Tambah Kategori'])

@section('content')
    <div class="mx-auto max-w-md rounded-lg border border-gray-700 bg-gray-800 p-8 shadow-lg">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('admin.categories.index') }}" class="rounded-lg border border-gray-600 bg-gray-700 p-2 text-gray-300 transition-all duration-200 hover:bg-gray-600 hover:text-white">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-white">Tambah Kategori</h1>
                <p class="text-sm text-gray-400">Buat kategori pekerjaan baru</p>
            </div>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-gray-300">Nama Kategori</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    autofocus
                    placeholder="Contoh: Desain, Pemrograman, Penulisan"
                    class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-white placeholder:text-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors"
                >
                @error('name')
                    <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-400">Nama kategori akan digunakan untuk mengelompokkan job</p>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.categories.index') }}" 
                    class="flex-1 rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-center text-sm font-semibold text-gray-300 transition-all duration-200 hover:bg-gray-600 hover:text-white">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition-all duration-200 hover:bg-blue-500 hover:shadow-lg">
                    Simpan
                </button>
            </div>
        </form>
    </div>
@endsection

