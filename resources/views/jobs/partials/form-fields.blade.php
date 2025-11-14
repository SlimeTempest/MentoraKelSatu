@csrf

<div class="space-y-5">
    <div>
        <label for="title" class="mb-2 block text-sm font-medium text-gray-700">Judul</label>
        <input
            type="text"
            id="title"
            name="title"
            value="{{ old('title', $job->title ?? '') }}"
            required
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
        >
        @error('title')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="description" class="mb-2 block text-sm font-medium text-gray-700">Deskripsi</label>
        <textarea
            id="description"
            name="description"
            rows="5"
            required
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
        >{{ old('description', $job->description ?? '') }}</textarea>
        @error('description')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="price" class="mb-2 block text-sm font-medium text-gray-700">Harga</label>
        <input
            type="number"
            id="price"
            name="price"
            value="{{ old('price', $job->price ?? '') }}"
            min="0"
            step="0.01"
            required
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
        >
        @error('price')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="deadline" class="mb-2 block text-sm font-medium text-gray-700">Deadline</label>
        <input
            type="date"
            id="deadline"
            name="deadline"
            value="{{ old('deadline', isset($job->deadline) ? $job->deadline->format('Y-m-d') : '') }}"
            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
        >
        @error('deadline')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="mb-2 block text-sm font-medium text-gray-700">Kategori</label>
        <div class="grid gap-2 sm:grid-cols-2">
            @forelse ($categories as $category)
                <label class="flex items-center gap-2 text-sm text-gray-700">
                    <input
                        type="checkbox"
                        name="categories[]"
                        value="{{ $category->category_id }}"
                        {{ in_array($category->category_id, old('categories', isset($job) ? $job->categories->pluck('category_id')->all() : [])) ? 'checked' : '' }}
                        class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                    >
                    <span>{{ $category->name }}</span>
                </label>
            @empty
                <p class="text-sm text-gray-500">Belum ada kategori. Tambahkan lewat seeder atau admin.</p>
            @endforelse
        </div>
        @error('categories')
            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('jobs.index') }}" class="rounded border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
            Batal
        </a>
        <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
            {{ $submitLabel }}
        </button>
    </div>
</div>

