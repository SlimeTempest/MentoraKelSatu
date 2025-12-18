@extends('layouts.app', ['title' => 'Daftar Job'])

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    {{-- Header Section --}}
    <div class="mb-8 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="space-y-1">
            <h1 class="text-3xl font-bold text-white tracking-tight">Daftar Job</h1>
            <p class="text-sm text-gray-400">Kelola dan monitor semua job di sistem</p>
        </div>
        @if (!$isAdmin)
            <a href="{{ route('jobs.create') }}"
                class="group inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 transition-all duration-300 hover:from-blue-500 hover:to-indigo-500 hover:shadow-xl hover:shadow-blue-500/30 hover:scale-[1.02] active:scale-[0.98]">
                <svg class="h-5 w-5 transition-transform duration-300 group-hover:rotate-90" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                <span>Job Baru</span>
            </a>
        @endif
    </div>

    {{-- Error Alert --}}
    @if ($errors->has('job'))
        <div
            class="mb-6 rounded-xl border border-red-500/30 bg-gradient-to-r from-red-500/10 to-red-600/10 px-4 py-3 backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <svg class="h-5 w-5 flex-shrink-0 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="text-sm font-medium text-red-300">{{ $errors->first('job') }}</p>
            </div>
        </div>
    @endif

    @if ($isAdmin)
        {{-- Admin Monitoring View --}}
        <div class="space-y-6">
            <section class="rounded-2xl border border-gray-700/50 bg-gray-800/50 backdrop-blur-sm shadow-2xl">
                @if (isset($allJobs) && $allJobs->isEmpty())
                    <div class="flex flex-col items-center justify-center p-12 text-center">
                        <div class="mb-4 rounded-full bg-gray-700/50 p-4">
                            <svg class="h-8 w-8 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <h3 class="mb-2 text-lg font-semibold text-gray-300">Belum ada job di sistem</h3>
                        <p class="text-sm text-gray-500">Job akan muncul di sini ketika user membuat job baru.</p>
                    </div>
                @else
                    <div class="p-4 border-b border-gray-700/50">
                        <form action="{{ route('jobs.index') }}" method="GET"
                            class="flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                            <div class="flex items-center gap-2 flex-wrap">
                                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                                    placeholder="Cari judul/creator..."
                                    class="rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                <div x-data='{
                                    open: false,
                                    query: "",
                                    items: @json($categories->map(fn($c) => ['id' => (int) $c->category_id, 'name' => $c->name])),
                                    selected: @json(array_map('intval', (array) ($filters['categories'] ?? []))),
                                    toggle(id) {
                                        if (!this.isSelected(id)) this.selected.push(id);
                                        else this.selected = this.selected.filter(i => i !== id);
                                    },
                                    isSelected(id) { return this.selected.includes(id); },
                                    filtered() { return this.items.filter(i => i.name.toLowerCase().includes(this.query.toLowerCase())); }
                                }'
                                    class="relative">
                                    <button type="button" @click="open = !open" @keydown.escape="open = false"
                                        class="w-full text-left rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-white flex items-center gap-2">
                                        <div class="flex-1 flex flex-wrap gap-1">
                                            <template x-if="selected.length == 0">
                                                <span class="text-gray-400">Pilih kategori</span>
                                            </template>
                                            <template x-for="id in selected" :key="id">
                                                <span
                                                    class="inline-flex items-center gap-1 bg-indigo-600/30 text-indigo-100 px-2 py-0.5 rounded-full text-xs">
                                                    <span x-text="items.find(i => i.id == id)?.name"></span>
                                                    <button type="button" @click.stop="toggle(id)"
                                                        class="ml-1 text-indigo-200 hover:text-white">&times;</button>
                                                </span>
                                            </template>
                                        </div>
                                        <svg class="h-4 w-4 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div x-show="open" x-cloak @click.away="open = false"
                                        class="absolute z-50 mt-2 w-full max-h-56 overflow-auto rounded-md border border-gray-700 bg-gray-800 p-2 shadow-lg">
                                        <input type="text" x-model="query" placeholder="Cari kategori..."
                                            class="w-full rounded-md bg-gray-700 px-3 py-2 text-sm text-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <ul class="mt-2 space-y-1">
                                            <template x-for="item in filtered()" :key="item.id">
                                                <li>
                                                    <label
                                                        class="flex items-center gap-2 p-2 rounded hover:bg-gray-700 cursor-pointer">
                                                        <input type="checkbox" :checked="isSelected(item.id)"
                                                            @click.stop="toggle(item.id)"
                                                            class="h-4 w-4 rounded bg-gray-700 text-indigo-500">
                                                        <span x-text="item.name" class="text-sm text-gray-200"></span>
                                                        <span class="ml-auto text-xs text-gray-400"
                                                            x-show="isSelected(item.id)">Dipilih</span>
                                                    </label>
                                                </li>
                                            </template>
                                            <div x-show="filtered().length == 0" class="p-2 text-sm text-gray-400">Tidak ada
                                                kategori</div>
                                        </ul>
                                    </div>

                                    <template x-for="id in selected" :key="id">
                                        <input type="hidden" name="categories[]" :value="id">
                                    </template>
                                </div>
                                <input type="number" name="min_price" min="0"
                                    value="{{ $filters['min_price'] ?? '' }}" placeholder="Min"
                                    class="rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-white">
                                <input type="number" name="max_price" min="0"
                                    value="{{ $filters['max_price'] ?? '' }}" placeholder="Max"
                                    class="rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-white">
                            </div>
                            <div class="flex items-center gap-2">
                                <button type="submit"
                                    class="rounded-lg bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-500">Filter</button>
                                <a href="{{ route('jobs.index') }}"
                                    class="rounded-lg border border-gray-600 px-3 py-2 text-sm text-gray-300">Reset</a>
                            </div>
                        </form>
                    </div>
                    <div class="overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700/50">
                                <thead class="bg-gradient-to-r from-gray-700/50 to-gray-700/30">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-300">
                                            Job Details</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-300 hidden lg:table-cell">
                                            Creator</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-300 hidden md:table-cell">
                                            Assignee</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-300">
                                            Status</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-300 hidden sm:table-cell">
                                            Price</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-300 hidden lg:table-cell">
                                            Deadline</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-300 hidden xl:table-cell">
                                            Categories</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-300">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700/30 bg-gray-800/30">
                                    @foreach ($allJobs as $job)
                                        <tr class="group transition-all duration-200 hover:bg-gray-700/30">
                                            <td class="px-6 py-4">
                                                <div class="space-y-1.5">
                                                    <p
                                                        class="font-semibold text-gray-100 group-hover:text-white transition-colors">
                                                        {{ $job->title }}</p>
                                                    <p class="text-xs text-gray-400 line-clamp-1">
                                                        {{ Str::limit($job->description, 60) }}</p>
                                                    @if ($job->feedback && $job->feedback->comment)
                                                        <div
                                                            class="mt-2 rounded-lg border border-gray-600/50 bg-gray-700/30 p-2.5">
                                                            <div class="flex items-center gap-2 mb-1.5">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                                    height="12" viewBox="0 0 24 24"
                                                                    fill="currentColor" class="text-yellow-400">
                                                                    <polygon
                                                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                                                </svg>
                                                                <span
                                                                    class="text-xs font-semibold text-yellow-400">{{ $job->feedback->rating }}/5</span>
                                                                <span class="text-xs text-gray-500">•</span>
                                                                <span class="text-xs text-gray-400">Feedback</span>
                                                            </div>
                                                            <p class="text-xs text-gray-300 italic line-clamp-2">
                                                                "{{ Str::limit($job->feedback->comment, 80) }}"</p>
                                                        </div>
                                                    @endif
                                                    <div class="flex flex-wrap items-center gap-2 lg:hidden">
                                                        <span class="text-xs text-gray-500">By:
                                                            <a href="{{ route('users.profile.show', $job->creator) }}"
                                                                class="font-medium text-blue-400 hover:text-blue-300 transition-colors">
                                                                {{ $job->creator->name }}
                                                            </a>
                                                        </span>
                                                        @if ($job->assignee)
                                                            <span class="text-xs text-gray-500">• Taken by:
                                                                <a href="{{ route('users.profile.show', $job->assignee) }}"
                                                                    class="font-medium text-blue-400 hover:text-blue-300 transition-colors">
                                                                    {{ $job->assignee->name }}
                                                                </a>
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <div class="flex flex-wrap items-center gap-2 sm:hidden">
                                                        <span
                                                            class="inline-flex items-center gap-1 rounded-lg bg-green-500/20 px-2 py-0.5 text-xs font-semibold text-green-300">
                                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                                                <path
                                                                    d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z">
                                                                </path>
                                                                <path fill-rule="evenodd"
                                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                                                    clip-rule="evenodd"></path>
                                                            </svg>
                                                            Rp {{ number_format($job->price, 0, ',', '.') }}
                                                        </span>
                                                        @if ($job->deadline)
                                                            <span
                                                                class="text-xs text-gray-400">{{ $job->deadline->format('d M Y') }}</span>
                                                        @endif
                                                    </div>
                                                    <div class="flex flex-wrap gap-1.5 xl:hidden">
                                                        @foreach ($job->categories->take(2) as $category)
                                                            <span
                                                                class="inline-flex items-center rounded-full border border-blue-500/30 bg-blue-500/20 px-2 py-0.5 text-xs font-medium text-blue-300">
                                                                {{ $category->name }}
                                                            </span>
                                                        @endforeach
                                                        @if ($job->categories->count() > 2)
                                                            <span
                                                                class="text-xs text-gray-400">+{{ $job->categories->count() - 2 }}</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap hidden lg:table-cell">
                                                <a href="{{ route('users.profile.show', $job->creator) }}"
                                                    class="inline-flex items-center gap-2 text-sm font-medium text-blue-400 transition-colors hover:text-blue-300">
                                                    <div
                                                        class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-xs font-bold text-white">
                                                        {{ substr($job->creator->name, 0, 1) }}
                                                    </div>
                                                    <span>{{ $job->creator->name }}</span>
                                                </a>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap hidden md:table-cell">
                                                @if ($job->assignee)
                                                    <a href="{{ route('users.profile.show', $job->assignee) }}"
                                                        class="inline-flex items-center gap-2 text-sm font-medium text-blue-400 transition-colors hover:text-blue-300">
                                                        <div
                                                            class="h-8 w-8 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-xs font-bold text-white">
                                                            {{ substr($job->assignee->name, 0, 1) }}
                                                        </div>
                                                        <span>{{ $job->assignee->name }}</span>
                                                    </a>
                                                @else
                                                    <span class="text-sm text-gray-500">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                @php
                                                    $statusConfig = [
                                                        'belum_diambil' => [
                                                            'bg' => 'bg-gray-500/20',
                                                            'text' => 'text-gray-300',
                                                            'border' => 'border-gray-500/30',
                                                            'icon' =>
                                                                'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z',
                                                        ],
                                                        'on_progress' => [
                                                            'bg' => 'bg-blue-500/20',
                                                            'text' => 'text-blue-300',
                                                            'border' => 'border-blue-500/30',
                                                            'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                                                        ],
                                                        'selesai' => [
                                                            'bg' => 'bg-green-500/20',
                                                            'text' => 'text-green-300',
                                                            'border' => 'border-green-500/30',
                                                            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z',
                                                        ],
                                                        'kadaluarsa' => [
                                                            'bg' => 'bg-red-500/20',
                                                            'text' => 'text-red-300',
                                                            'border' => 'border-red-500/30',
                                                            'icon' =>
                                                                'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z',
                                                        ],
                                                    ];
                                                    $status =
                                                        $statusConfig[$job->status] ?? $statusConfig['belum_diambil'];
                                                @endphp
                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-semibold capitalize {{ $status['bg'] }} {{ $status['text'] }} {{ $status['border'] }}">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="{{ $status['icon'] }}"></path>
                                                    </svg>
                                                    {{ str_replace('_', ' ', $job->status) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                                                <div
                                                    class="inline-flex items-center gap-1.5 rounded-lg bg-green-500/20 px-3 py-1.5">
                                                    <svg class="h-4 w-4 text-green-400" fill="currentColor"
                                                        viewBox="0 0 20 20">
                                                        <path
                                                            d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z">
                                                        </path>
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    <span class="text-sm font-bold text-green-400">Rp
                                                        {{ number_format($job->price, 0, ',', '.') }}</span>
                                                </div>
                                            </td>
                                            <td
                                                class="px-6 py-4 whitespace-nowrap text-sm text-gray-400 hidden lg:table-cell">
                                                @if ($job->deadline)
                                                    <div class="inline-flex items-center gap-1.5">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                        <span>{{ $job->deadline->format('d M Y') }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-gray-500">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 hidden xl:table-cell">
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach ($job->categories as $category)
                                                        <span
                                                            class="inline-flex items-center rounded-full border border-indigo-500/30 bg-indigo-500/20 px-2.5 py-0.5 text-xs font-medium text-indigo-300">
                                                            {{ $category->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-2">
                                                    @can('update', $job)
                                                        <a href="{{ route('jobs.edit', $job) }}"
                                                            class="group inline-flex items-center justify-center rounded-lg border border-gray-600 bg-gray-700/50 p-2 text-gray-300 transition-all duration-200 hover:border-blue-500 hover:bg-blue-500/20 hover:text-blue-300 hover:scale-110 active:scale-95"
                                                            title="Edit">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                            </svg>
                                                        </a>
                                                    @endcan
                                                    @can('delete', $job)
                                                        <form id="delete-form-{{ $job->job_id }}"
                                                            action="{{ route('jobs.destroy', $job) }}" method="POST"
                                                            class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                onclick="customConfirm('Hapus job ini?', function(confirmed) { if(confirmed) document.getElementById('delete-form-{{ $job->job_id }}').submit(); })"
                                                                class="group inline-flex items-center justify-center rounded-lg border border-red-500/50 bg-red-500/20 p-2 text-red-300 transition-all duration-200 hover:border-red-400 hover:bg-red-500/30 hover:text-red-200 hover:scale-110 active:scale-95"
                                                                title="Hapus">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.12m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    @if ($job->status === \App\Models\Job::STATUS_PROGRESS && $job->assignee)
                                                        <form action="{{ route('jobs.complete', $job) }}" method="POST"
                                                            class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-green-600 to-emerald-600 px-3 py-1.5 text-xs font-semibold text-white shadow-md shadow-green-500/25 transition-all duration-200 hover:from-green-500 hover:to-emerald-500 hover:shadow-lg hover:shadow-green-500/30 hover:scale-105 active:scale-95">
                                                                <svg class="h-3.5 w-3.5" fill="none"
                                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                                Selesai
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div
                            class="border-t border-gray-700/50 bg-gradient-to-r from-gray-700/30 to-gray-700/20 px-6 py-4">
                            <div class="flex flex-col items-center justify-between gap-4 sm:flex-row">
                                <div class="text-sm text-gray-400">
                                    Menampilkan <span
                                        class="font-semibold text-gray-300">{{ $allJobs->firstItem() ?? 0 }}</span> sampai
                                    <span class="font-semibold text-gray-300">{{ $allJobs->lastItem() ?? 0 }}</span> dari
                                    <span class="font-semibold text-gray-300">{{ $allJobs->total() }}</span> hasil
                                </div>
                                <div>
                                    {{ $allJobs->onEachSide(2)->links() }}
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </section>
        </div>
    @else
        {{-- User View --}}
        <div class="space-y-8">
            {{-- Available Jobs Section --}}
            <section class="rounded-2xl border border-gray-700/50 bg-gray-800/50 backdrop-blur-sm shadow-2xl">
                <div class="border-b border-gray-700/50 bg-gradient-to-r from-gray-700/50 to-gray-700/30 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-green-500/20 p-2">
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Job Tersedia</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Job yang belum diambil. Kamu bisa ambil maksimal 2
                                sekaligus.</p>
                        </div>
                    </div>
                </div>

                {{-- filter --}}
                {{-- <div class="rounded-lg border border-gray-700 bg-gray-800 p-4 shadow-lg">
                    <form action="{{ route('jobs.index') }}" method="GET" class="space-y-4">
                        <div class="grid gap-4 md:grid-cols-4">
                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-400">Cari</label>
                                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                                    placeholder="Cari judul, deskripsi atau pembuat..."
                                    class="w-full rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-400">Kategori</label>
                                <div x-data='{
                                    open: false,
                                    query: "",
                                    items: @json($categories->map(fn($c) => ['id' => (int) $c->category_id, 'name' => $c->name])),
                                    selected: @json(array_map('intval', (array) ($filters['categories'] ?? []))),
                                    toggle(id) {
                                        if (!this.isSelected(id)) this.selected.push(id);
                                        else this.selected = this.selected.filter(i => i !== id);
                                    },
                                    isSelected(id) { return this.selected.includes(id); },
                                    filtered() { return this.items.filter(i => i.name.toLowerCase().includes(this.query.toLowerCase())); }
                                }'
                                    class="relative">
                                    <button type="button" @click="open = !open" @keydown.escape="open = false"
                                        class="w-full text-left rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-white flex items-center gap-2">
                                        <div class="flex-1 flex flex-wrap gap-1">
                                            <template x-if="selected.length == 0">
                                                <span class="text-gray-400">Pilih kategori</span>
                                            </template>
                                            <template x-for="id in selected" :key="id">
                                                <span
                                                    class="inline-flex items-center gap-1 bg-indigo-600/30 text-indigo-100 px-2 py-0.5 rounded-full text-xs">
                                                    <span x-text="items.find(i => i.id == id)?.name"></span>
                                                    <button type="button" @click.stop="toggle(id)"
                                                        class="ml-1 text-indigo-200 hover:text-white">&times;</button>
                                                </span>
                                            </template>
                                        </div>
                                        <svg class="h-4 w-4 text-gray-300" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div x-show="open" x-cloak @click.away="open = false"
                                        class="absolute z-50 mt-2 w-full max-h-56 overflow-auto rounded-md border border-gray-700 bg-gray-800 p-2 shadow-lg">
                                        <input type="text" x-model="query" placeholder="Cari kategori..."
                                            class="w-full rounded-md bg-gray-700 px-3 py-2 text-sm text-white placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                        <ul class="mt-2 space-y-1">
                                            <template x-for="item in filtered()" :key="item.id">
                                                <li>
                                                    <label
                                                        class="flex items-center gap-2 p-2 rounded hover:bg-gray-700 cursor-pointer">
                                                        <input type="checkbox" :checked="isSelected(item.id)"
                                                            @click.stop="toggle(item.id)"
                                                            class="h-4 w-4 rounded bg-gray-700 text-indigo-500">
                                                        <span x-text="item.name" class="text-sm text-gray-200"></span>
                                                        <span class="ml-auto text-xs text-gray-400"
                                                            x-show="isSelected(item.id)">Dipilih</span>
                                                    </label>
                                                </li>
                                            </template>
                                            <div x-show="filtered().length == 0" class="p-2 text-sm text-gray-400">Tidak
                                                ada kategori</div>
                                        </ul>
                                    </div>

                                    <template x-for="id in selected" :key="id">
                                        <input type="hidden" name="categories[]" :value="id">
                                    </template>
                                </div>
                                <p class="mt-1 text-xs text-gray-400">(Tekan ctrl/command untuk memilih lebih dari satu)
                                </p>
                            </div>

                            <div>
                                <label class="mb-1 block text-xs font-medium text-gray-400">Price Range</label>
                                <div class="flex gap-2">
                                    <input type="number" name="min_price" min="0"
                                        value="{{ $filters['min_price'] ?? '' }}" placeholder="Min"
                                        class="w-1/2 rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                    <input type="number" name="max_price" min="0"
                                        value="{{ $filters['max_price'] ?? '' }}" placeholder="Max"
                                        class="w-1/2 rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                </div>
                            </div>

                            <div class="flex items-end gap-2">
                                <button type="submit"
                                    class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-gray-50 hover:bg-blue-500 transition-all duration-200 hover:shadow-lg">Cari</button>
                                <a href="{{ route('jobs.index') }}"
                                    class="w-full inline-block text-center rounded-lg border border-gray-600 px-4 py-2 text-sm text-gray-300 hover:bg-gray-700">Reset</a>
                            </div>
                        </div>
                    </form>
                </div> --}}

                <div class="p-6">
                    @if ($availableJobs->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="mb-4 rounded-full bg-gray-700/50 p-4">
                                <svg class="h-8 w-8 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="mb-2 text-lg font-semibold text-gray-300">Belum ada job tersedia</h3>
                            <p class="text-sm text-gray-500">Job baru akan muncul di sini ketika ada yang membuat job.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($availableJobs as $job)
                                <article
                                    class="group relative overflow-hidden rounded-xl border border-gray-700/50 bg-gradient-to-br from-gray-800/50 to-gray-800/30 p-6 transition-all duration-300 hover:border-green-500/50 hover:shadow-lg hover:shadow-green-500/10">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="flex-1 space-y-3">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="flex-1">
                                                    <div class="mb-2 flex items-center gap-2">
                                                        <div
                                                            class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-xs font-bold text-white">
                                                            {{ substr($job->creator->name, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <p class="text-xs font-medium text-gray-400">Dibuat oleh</p>
                                                            <a href="{{ route('users.profile.show', $job->creator) }}"
                                                                class="text-sm font-semibold text-indigo-400 transition-colors hover:text-indigo-300">
                                                                {{ $job->creator->name }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <h3
                                                        class="text-lg font-bold text-white group-hover:text-green-400 transition-colors">
                                                        {{ $job->title }}</h3>
                                                    <p class="mt-2 text-sm leading-relaxed text-gray-300 line-clamp-2">
                                                        {{ Str::limit($job->description, 160) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                <span
                                                    class="inline-flex items-center gap-1.5 rounded-lg bg-gradient-to-r from-green-500/20 to-emerald-500/20 border border-green-500/30 px-3 py-1.5 text-xs font-bold text-green-300">
                                                    <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 20 20">
                                                        <path
                                                            d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z">
                                                        </path>
                                                        <path fill-rule="evenodd"
                                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z"
                                                            clip-rule="evenodd"></path>
                                                    </svg>
                                                    Rp {{ number_format($job->price, 0, ',', '.') }}
                                                </span>
                                                @if ($job->deadline)
                                                    <span
                                                        class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500/20 border border-amber-500/30 px-3 py-1.5 text-xs font-medium text-amber-300">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                        Deadline {{ $job->deadline->format('d M Y') }}
                                                    </span>
                                                @endif
                                                @foreach ($job->categories as $category)
                                                    <span
                                                        class="inline-flex items-center rounded-full border border-indigo-500/30 bg-indigo-500/20 px-2.5 py-0.5 text-xs font-medium text-indigo-300">
                                                        {{ $category->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <form action="{{ route('jobs.take', $job) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="group relative inline-flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-green-500/25 transition-all duration-300 hover:from-green-500 hover:to-emerald-500 hover:shadow-xl hover:shadow-green-500/30 hover:scale-105 active:scale-95 sm:w-auto">
                                                    <svg class="h-5 w-5 transition-transform duration-300 group-hover:rotate-90"
                                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M12 4v16m8-8H4"></path>
                                                    </svg>
                                                    <span>Ambil Job</span>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        <div
                            class="mt-6 flex flex-col items-center justify-between gap-4 border-t border-gray-700/50 pt-4 sm:flex-row">
                            <div class="text-sm text-gray-400">
                                Menampilkan <span
                                    class="font-semibold text-gray-300">{{ $availableJobs->firstItem() ?? 0 }}</span>
                                sampai <span
                                    class="font-semibold text-gray-300">{{ $availableJobs->lastItem() ?? 0 }}</span> dari
                                <span class="font-semibold text-gray-300">{{ $availableJobs->total() }}</span> hasil
                            </div>
                            <div>
                                {{ $availableJobs->onEachSide(2)->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            {{-- My Jobs Section --}}
            <section class="rounded-2xl border border-gray-700/50 bg-gray-800/50 backdrop-blur-sm shadow-2xl">
                <div class="border-b border-gray-700/50 bg-gradient-to-r from-gray-700/50 to-gray-700/30 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-blue-500/20 p-2">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Job Saya</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Job yang kamu buat</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if ($myJobs->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="mb-4 rounded-full bg-gray-700/50 p-4">
                                <svg class="h-8 w-8 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="mb-2 text-lg font-semibold text-gray-300">Belum ada job yang kamu buat</h3>
                            <a href="{{ route('jobs.create') }}"
                                class="mt-4 inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-blue-500/25 transition-all duration-300 hover:from-blue-500 hover:to-indigo-500 hover:shadow-xl hover:shadow-blue-500/30 hover:scale-105 active:scale-95">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                Buat Job Pertama
                            </a>
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700/30">
                                <thead class="bg-gradient-to-r from-gray-700/50 to-gray-700/30">
                                    <tr>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-300">
                                            Job Details</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-300 hidden sm:table-cell">
                                            Status</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-300 hidden md:table-cell">
                                            Assignee</th>
                                        <th
                                            class="px-6 py-4 text-left text-xs font-bold uppercase tracking-wider text-gray-300">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700/30 bg-gray-800/30">
                                    @foreach ($myJobs as $job)
                                        <tr class="group transition-all duration-200 hover:bg-gray-700/30">
                                            <td class="px-6 py-4">
                                                <div class="space-y-1.5">
                                                    <p
                                                        class="font-semibold text-gray-100 group-hover:text-white transition-colors">
                                                        {{ $job->title }}</p>
                                                    <p class="text-xs text-gray-400 line-clamp-1">
                                                        {{ Str::limit($job->description, 80) }}</p>
                                                    @if ($job->feedback && $job->feedback->comment)
                                                        <div
                                                            class="mt-2 rounded-lg border border-gray-600/50 bg-gray-700/30 p-2.5">
                                                            <div class="flex items-center gap-2 mb-1.5">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="12"
                                                                    height="12" viewBox="0 0 24 24"
                                                                    fill="currentColor" class="text-yellow-400">
                                                                    <polygon
                                                                        points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                                                </svg>
                                                                <span
                                                                    class="text-xs font-semibold text-yellow-400">{{ $job->feedback->rating }}/5</span>
                                                                <span class="text-xs text-gray-500">•</span>
                                                                <span class="text-xs text-gray-400">Feedback
                                                                    diberikan</span>
                                                            </div>
                                                            <p class="text-xs text-gray-300 italic line-clamp-2">
                                                                "{{ Str::limit($job->feedback->comment, 100) }}"</p>
                                                        </div>
                                                    @endif
                                                    <div class="flex flex-wrap items-center gap-2 sm:hidden">
                                                        @php
                                                            $statusColors = [
                                                                'belum_diambil' =>
                                                                    'bg-gray-500/20 text-gray-300 border-gray-500/30',
                                                                'on_progress' =>
                                                                    'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                                                'selesai' =>
                                                                    'bg-green-500/20 text-green-300 border-green-500/30',
                                                                'kadaluarsa' =>
                                                                    'bg-red-500/20 text-red-300 border-red-500/30',
                                                            ];
                                                            $color =
                                                                $statusColors[$job->status] ??
                                                                'bg-gray-500/20 text-gray-300 border-gray-500/30';
                                                        @endphp
                                                        <span
                                                            class="rounded-full border px-2.5 py-0.5 text-xs font-medium capitalize {{ $color }}">{{ str_replace('_', ' ', $job->status) }}</span>
                                                        @if ($job->assignee)
                                                            <span class="text-xs text-gray-400">Oleh:
                                                                <a href="{{ route('users.profile.show', $job->assignee) }}"
                                                                    class="font-medium text-indigo-400 hover:text-indigo-300 transition-colors">
                                                                    {{ $job->assignee->name }}
                                                                </a>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 hidden sm:table-cell">
                                                @php
                                                    $statusColors = [
                                                        'belum_diambil' =>
                                                            'bg-gray-500/20 text-gray-300 border-gray-500/30',
                                                        'on_progress' =>
                                                            'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                                        'selesai' =>
                                                            'bg-green-500/20 text-green-300 border-green-500/30',
                                                        'kadaluarsa' => 'bg-red-500/20 text-red-300 border-red-500/30',
                                                    ];
                                                    $color =
                                                        $statusColors[$job->status] ??
                                                        'bg-gray-500/20 text-gray-300 border-gray-500/30';
                                                @endphp
                                                <span
                                                    class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold capitalize {{ $color }}">{{ str_replace('_', ' ', $job->status) }}</span>
                                            </td>
                                            <td class="px-6 py-4 hidden md:table-cell">
                                                @if ($job->assignee)
                                                    <a href="{{ route('users.profile.show', $job->assignee) }}"
                                                        class="inline-flex items-center gap-2 text-sm font-medium text-indigo-400 transition-colors hover:text-indigo-300">
                                                        <div
                                                            class="h-8 w-8 rounded-full bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center text-xs font-bold text-white">
                                                            {{ substr($job->assignee->name, 0, 1) }}
                                                        </div>
                                                        <span>{{ $job->assignee->name }}</span>
                                                    </a>
                                                @else
                                                    <span class="text-sm text-gray-500">-</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    @can('update', $job)
                                                        <a href="{{ route('jobs.edit', $job) }}"
                                                            class="group inline-flex items-center justify-center rounded-lg border border-gray-600 bg-gray-700/50 p-2 text-gray-300 transition-all duration-200 hover:border-blue-500 hover:bg-blue-500/20 hover:text-blue-300 hover:scale-110 active:scale-95"
                                                            title="Edit">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                            </svg>
                                                        </a>
                                                    @endcan
                                                    @can('delete', $job)
                                                        <form id="delete-form-user-{{ $job->job_id }}"
                                                            action="{{ route('jobs.destroy', $job) }}" method="POST"
                                                            class="inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="button"
                                                                onclick="customConfirm('Hapus job ini?', function(confirmed) { if(confirmed) document.getElementById('delete-form-user-{{ $job->job_id }}').submit(); })"
                                                                class="group inline-flex items-center justify-center rounded-lg border border-red-500/50 bg-red-500/20 p-2 text-red-300 transition-all duration-200 hover:border-red-400 hover:bg-red-500/30 hover:text-red-200 hover:scale-110 active:scale-95"
                                                                title="Hapus">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor"
                                                                    viewBox="0 0 24 24" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                        d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.12m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    @if ($job->status === \App\Models\Job::STATUS_DONE && $job->assignee && !$job->feedback)
                                                        <a href="{{ route('jobs.feedback.create', $job) }}"
                                                            class="group inline-flex items-center gap-1.5 rounded-lg border border-yellow-500/50 bg-yellow-500/20 px-3 py-1.5 text-xs font-semibold text-yellow-300 transition-all duration-200 hover:border-yellow-400 hover:bg-yellow-500/30 hover:scale-105 active:scale-95"
                                                            title="Beri Rating">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                                height="14" viewBox="0 0 24 24" fill="currentColor"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <polygon
                                                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                                            </svg>
                                                            <span class="hidden sm:inline">Rating</span>
                                                        </a>
                                                    @endif
                                                    @if ($job->feedback)
                                                        <div class="group inline-flex items-center gap-1.5 rounded-lg border border-green-500/50 bg-green-500/20 px-3 py-1.5 text-xs font-semibold text-green-300"
                                                            title="Rating: {{ $job->feedback->rating }}/5">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                                height="14" viewBox="0 0 24 24" fill="currentColor"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <polygon
                                                                    points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                                            </svg>
                                                            <span
                                                                class="hidden sm:inline">{{ $job->feedback->rating }}/5</span>
                                                        </div>
                                                    @endif
                                                    @if ($job->status === \App\Models\Job::STATUS_DONE && $job->assignee)
                                                        <a href="{{ route('reports.create', ['job_id' => $job->job_id]) }}"
                                                            class="group inline-flex items-center gap-1.5 rounded-lg border border-orange-500/50 bg-orange-500/20 px-3 py-1.5 text-xs font-semibold text-orange-300 transition-all duration-200 hover:border-orange-400 hover:bg-orange-500/30 hover:scale-105 active:scale-95"
                                                            title="Laporkan">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                                height="14" viewBox="0 0 24 24" fill="none"
                                                                stroke="currentColor" stroke-width="2"
                                                                stroke-linecap="round" stroke-linejoin="round">
                                                                <path
                                                                    d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                                                                <line x1="4" x2="4" y1="22"
                                                                    y2="15" />
                                                            </svg>
                                                            <span class="hidden sm:inline">Laporkan</span>
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div
                            class="mt-6 flex flex-col items-center justify-between gap-4 border-t border-gray-700/50 bg-gradient-to-r from-gray-700/30 to-gray-700/20 px-6 py-4 sm:flex-row">
                            <div class="text-sm text-gray-400">
                                Menampilkan <span
                                    class="font-semibold text-gray-300">{{ $myJobs->firstItem() ?? 0 }}</span> sampai
                                <span class="font-semibold text-gray-300">{{ $myJobs->lastItem() ?? 0 }}</span> dari <span
                                    class="font-semibold text-gray-300">{{ $myJobs->total() }}</span> hasil
                            </div>
                            <div>
                                {{ $myJobs->onEachSide(2)->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </section>

            {{-- Assigned Jobs Section --}}
            <section class="rounded-2xl border border-gray-700/50 bg-gray-800/50 backdrop-blur-sm shadow-2xl">
                <div class="border-b border-gray-700/50 bg-gradient-to-r from-gray-700/50 to-gray-700/30 px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="rounded-lg bg-indigo-500/20 p-2">
                            <svg class="h-5 w-5 text-indigo-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                </path>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-white">Job yang Saya Kerjakan</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Job yang sedang atau pernah kamu ambil</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    @if ($assignedJobs->isEmpty())
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="mb-4 rounded-full bg-gray-700/50 p-4">
                                <svg class="h-8 w-8 text-gray-500" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="mb-2 text-lg font-semibold text-gray-300">Belum ada job yang kamu kerjakan</h3>
                            <p class="text-sm text-gray-500">Ambil job dari bagian "Job Tersedia" untuk mulai bekerja.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach ($assignedJobs as $job)
                                <article
                                    class="group relative overflow-hidden rounded-xl border border-gray-700/50 bg-gradient-to-br from-gray-800/50 to-gray-800/30 p-6 transition-all duration-300 hover:border-indigo-500/50 hover:shadow-lg hover:shadow-indigo-500/10">
                                    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                                        <div class="flex-1 space-y-3">
                                            <div class="flex items-start justify-between gap-4">
                                                <div class="flex-1">
                                                    <div class="mb-2 flex items-center gap-2">
                                                        <div
                                                            class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-xs font-bold text-white">
                                                            {{ substr($job->creator->name, 0, 1) }}
                                                        </div>
                                                        <div>
                                                            <p class="text-xs font-medium text-gray-400">Dari</p>
                                                            <a href="{{ route('users.profile.show', $job->creator) }}"
                                                                class="text-sm font-semibold text-indigo-400 transition-colors hover:text-indigo-300">
                                                                {{ $job->creator->name }}
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <h3
                                                        class="text-lg font-bold text-white group-hover:text-indigo-400 transition-colors">
                                                        {{ $job->title }}</h3>
                                                    <p class="mt-2 text-sm leading-relaxed text-gray-300 line-clamp-2">
                                                        {{ Str::limit($job->description, 160) }}</p>
                                                </div>
                                            </div>
                                            <div class="flex flex-wrap items-center gap-2">
                                                @php
                                                    $statusColors = [
                                                        'belum_diambil' =>
                                                            'bg-gray-500/20 text-gray-300 border-gray-500/30',
                                                        'on_progress' =>
                                                            'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                                        'selesai' =>
                                                            'bg-green-500/20 text-green-300 border-green-500/30',
                                                        'kadaluarsa' => 'bg-red-500/20 text-red-300 border-red-500/30',
                                                    ];
                                                    $color =
                                                        $statusColors[$job->status] ??
                                                        'bg-gray-500/20 text-gray-300 border-gray-500/30';
                                                @endphp
                                                <span
                                                    class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold capitalize {{ $color }}">
                                                    {{ str_replace('_', ' ', $job->status) }}
                                                </span>
                                                @if ($job->deadline)
                                                    <span
                                                        class="inline-flex items-center gap-1.5 rounded-lg bg-amber-500/20 border border-amber-500/30 px-3 py-1.5 text-xs font-medium text-amber-300">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z">
                                                            </path>
                                                        </svg>
                                                        Deadline {{ $job->deadline->format('d M Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2 sm:flex-shrink-0">
                                            @if ($job->status === \App\Models\Job::STATUS_PROGRESS)
                                                <form action="{{ route('jobs.complete', $job) }}" method="POST"
                                                    class="w-full sm:w-auto">
                                                    @csrf
                                                    <button type="submit"
                                                        class="group relative inline-flex w-full items-center justify-center gap-2 overflow-hidden rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-indigo-500/25 transition-all duration-300 hover:from-indigo-500 hover:to-purple-500 hover:shadow-xl hover:shadow-indigo-500/30 hover:scale-105 active:scale-95 sm:w-auto">
                                                        <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        <span>Tandai Selesai</span>
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($job->status === \App\Models\Job::STATUS_DONE)
                                                <a href="{{ route('reports.create', ['job_id' => $job->job_id]) }}"
                                                    class="group inline-flex w-full items-center justify-center gap-1.5 rounded-xl border border-orange-500/50 bg-orange-500/20 px-4 py-3 text-sm font-semibold text-orange-300 transition-all duration-200 hover:border-orange-400 hover:bg-orange-500/30 hover:scale-105 active:scale-95 sm:w-auto">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                        viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                        <path
                                                            d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                                                        <line x1="4" x2="4" y1="22"
                                                            y2="15" />
                                                    </svg>
                                                    <span>Laporkan</span>
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                        <div
                            class="mt-6 flex flex-col items-center justify-between gap-4 border-t border-gray-700/50 pt-4 sm:flex-row">
                            <div class="text-sm text-gray-400">
                                Menampilkan <span
                                    class="font-semibold text-gray-300">{{ $assignedJobs->firstItem() ?? 0 }}</span>
                                sampai <span
                                    class="font-semibold text-gray-300">{{ $assignedJobs->lastItem() ?? 0 }}</span> dari
                                <span class="font-semibold text-gray-300">{{ $assignedJobs->total() }}</span> hasil
                            </div>
                            <div>
                                {{ $assignedJobs->onEachSide(2)->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            </section>
        </div>
    @endif
@endsection
