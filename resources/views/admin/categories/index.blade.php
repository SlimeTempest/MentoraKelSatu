@extends('layouts.app', ['title' => 'Kelola Kategori'])

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">Kelola Kategori</h1>
            <a href="{{ route('admin.categories.create') }}" 
                class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white transition-all duration-200 hover:bg-blue-500 hover:shadow-lg">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Kategori
            </a>
        </div>

        {{-- Search --}}
        <div class="rounded-lg border border-gray-700 bg-gray-800 p-4 shadow-lg">
            <form action="{{ route('admin.categories.index') }}" method="GET" class="flex gap-4">
                <div class="flex-1">
                    <label class="mb-1 block text-xs font-medium text-gray-400">Cari Kategori</label>
                    <input
                        type="text"
                        name="search"
                        value="{{ $search }}"
                        placeholder="Cari nama kategori..."
                        class="w-full rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                    >
                </div>
                <div class="flex items-end">
                    <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-gray-50 hover:bg-blue-500 transition-all duration-200 hover:shadow-lg">
                        Cari
                    </button>
                </div>
            </form>
        </div>

        {{-- Categories Table --}}
        <div class="rounded-lg border border-gray-700 bg-gray-800 shadow-lg overflow-hidden">
            @if ($categories->isEmpty())
                <div class="p-8 text-center">
                    <div class="mb-4 rounded-full bg-gray-700/50 p-4 inline-block">
                        <svg class="h-8 w-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                    <p class="text-sm text-gray-400">Tidak ada kategori yang ditemukan.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700 text-sm">
                        <thead class="bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Nama Kategori</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Jumlah Job</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Dibuat</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700 bg-gray-800">
                            @foreach ($categories as $category)
                                <tr class="hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-indigo-500/20 border border-indigo-500/30">
                                                <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                </svg>
                                            </div>
                                            <span class="font-semibold text-white">{{ $category->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 rounded-full border border-blue-500/30 bg-blue-500/20 px-3 py-1 text-xs font-medium text-blue-300">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            {{ $category->jobs()->count() }} job
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $category->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('admin.categories.edit', $category) }}" 
                                                class="rounded-lg border border-gray-600 bg-gray-700 px-3 py-1.5 text-xs font-medium text-gray-300 hover:bg-gray-600 hover:text-white transition-colors">
                                                Edit
                                            </a>
                                            <form id="delete-form-{{ $category->category_id }}" 
                                                action="{{ route('admin.categories.destroy', $category) }}" 
                                                method="POST" 
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" 
                                                    onclick="customConfirm('Hapus kategori <strong>{{ $category->name }}</strong>? Kategori yang sedang digunakan oleh job tidak bisa dihapus.', function(confirmed) { if(confirmed) document.getElementById('delete-form-{{ $category->category_id }}').submit(); })"
                                                    class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-gray-50 hover:bg-red-500 transition-all duration-200 hover:shadow-lg hover:scale-105">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-700 bg-gray-700/30 px-4 sm:px-6 py-3 sm:py-4">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="text-sm text-gray-400">
                            Menampilkan 
                            <span class="font-semibold text-gray-300">{{ $categories->firstItem() ?? 0 }}</span>
                            sampai 
                            <span class="font-semibold text-gray-300">{{ $categories->lastItem() ?? 0 }}</span>
                            dari 
                            <span class="font-semibold text-gray-300">{{ $categories->total() }}</span>
                            kategori
                        </div>
                        <div class="flex items-center gap-2">
                            {{ $categories->appends(request()->query())->links('pagination::default') }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

