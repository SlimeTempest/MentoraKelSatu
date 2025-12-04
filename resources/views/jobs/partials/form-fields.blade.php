@csrf

<div class="space-y-5">
    <div>
        <label for="title" class="mb-2 block text-sm font-medium text-gray-300">Judul</label>
        <input type="text" id="title" name="title" value="{{ old('title', $job->title ?? '') }}" required
            class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors"
            placeholder="Masukkan judul job">
        @error('title')
            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-medium text-gray-300">Deskripsi</label>
        <textarea id="description" name="description" rows="5" required
            class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors resize-none"
            placeholder="Masukkan deskripsi lengkap job">{{ old('description', $job->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="price" class="mb-2 block text-sm font-medium text-gray-300">Harga</label>
        <input type="number" id="price" name="price" value="{{ old('price', $job->price ?? '') }}" min="0"
            step="0.01" required
            class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors"
            placeholder="Masukkan harga job">
        @error('price')
            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="deadline" class="mb-2 block text-sm font-medium text-gray-300">Deadline</label>
        <input type="date" id="deadline" name="deadline"
            value="{{ old('deadline', isset($job->deadline) ? $job->deadline->format('Y-m-d') : '') }}"
            class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors">
        @error('deadline')
            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-medium text-gray-300">Kategori</label>
        <div class="grid gap-3 sm:grid-cols-2">
            @forelse ($categories as $category)
                <label class="flex items-center gap-2 rounded-lg border border-gray-700 bg-gray-700/50 px-3 py-2 text-sm text-gray-300 transition-all duration-200 hover:bg-gray-700 hover:border-gray-600 cursor-pointer">
                    <input type="checkbox" name="categories[]" value="{{ $category->category_id }}"
                        {{ in_array($category->category_id, old('categories', isset($job) ? $job->categories->pluck('category_id')->all() : [])) ? 'checked' : '' }}
                        class="rounded border-gray-600 bg-gray-700 text-blue-600 focus:ring-blue-500 focus:ring-2 transition-colors">
                    <span>{{ $category->name }}</span>
                </label>
            @empty
                <p class="text-sm text-gray-400">Belum ada kategori. Tambahkan lewat seeder atau admin.</p>
            @endforelse
        </div>
        @error('categories')
            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex justify-end gap-3 pt-4">
        <a href="{{ route('jobs.index') }}"
            class="rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 text-sm font-semibold text-gray-300 transition-all duration-200 hover:bg-gray-600 hover:text-white">
            Batal
        </a>
        <button type="submit"
            class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition-all duration-200 hover:bg-blue-500 hover:shadow-lg">
            {{ $submitLabel }}
        </button>
    </div>
</div>
