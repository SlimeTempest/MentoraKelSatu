@extends('layouts.app', ['title' => 'Edit Kategori'])

@section('content')
    <div class="mx-auto max-w-md rounded-lg border border-gray-700 bg-gray-800 p-8 shadow-lg">
        <div class="mb-6 flex items-center gap-3">
            <a href="{{ route('admin.categories.index') }}" class="rounded-lg border border-gray-600 bg-gray-700 p-2 text-gray-300 transition-all duration-200 hover:bg-gray-600 hover:text-white">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
            </a>
            <div>
                <h1 class="text-2xl font-semibold text-white">Edit Kategori</h1>
                <p class="text-sm text-gray-400">Ubah informasi kategori</p>
            </div>
        </div>

        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-5">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="mb-2 block text-sm font-medium text-gray-300">Nama Kategori</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name', $category->name) }}"
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

            <div class="rounded-lg border border-amber-500/30 bg-amber-500/10 p-4">
                <div class="flex items-start gap-3">
                    <svg class="h-5 w-5 flex-shrink-0 text-amber-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm text-amber-300">
                            <strong>Info:</strong> Kategori ini digunakan oleh <strong>{{ $category->jobs()->count() }}</strong> job. 
                            Mengubah nama kategori tidak akan mempengaruhi job yang sudah ada.
                        </p>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 pt-2">
                <a href="{{ route('admin.categories.index') }}" 
                    class="flex-1 rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-center text-sm font-semibold text-gray-300 transition-all duration-200 hover:bg-gray-600 hover:text-white">
                    Batal
                </a>
                <button type="submit"
                    class="flex-1 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition-all duration-200 hover:bg-blue-500 hover:shadow-lg">
                    Update
                </button>
            </div>
        </form>
    </div>
@endsection

