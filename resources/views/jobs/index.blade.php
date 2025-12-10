@extends('layouts.app', ['title' => 'Daftar Job'])

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-100">Daftar Job</h1>
            <p class="mt-1 text-sm text-gray-400">Kelola dan monitor semua job di sistem</p>
        </div>
        @if (!$isAdmin)
            <a href="{{ route('jobs.create') }}"
                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-gray-50 transition-all duration-200 hover:bg-blue-500 hover:shadow-lg hover:scale-105 flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Job Baru
            </a>
        @endif
    </div>

    @if ($errors->has('job'))
        <div class="mb-4 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
            {{ $errors->first('job') }}
        </div>
    @endif

    @if ($isAdmin)
        {{-- Admin Monitoring View --}}
        <div class="space-y-6">
            <section>
                @if (isset($allJobs) && $allJobs->isEmpty())
                    <div class="rounded-lg border border-gray-700 bg-gray-800 shadow-lg">
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-base font-medium text-gray-300 mb-1">Belum ada job di sistem</p>
                            <p class="text-sm text-gray-500">Job akan muncul di sini setelah dibuat oleh pengguna</p>
                        </div>
                    </div>
                @else
                    <div class="rounded-lg border border-gray-700 bg-gray-800 shadow-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700 text-sm">
                                <thead class="bg-gray-700/50">
                                    <tr>
                                        <th class="px-4 sm:px-6 py-3.5 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">
                                            Judul</th>
                                        <th class="px-4 sm:px-6 py-3.5 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">
                                            Dibuat Oleh</th>
                                        <th class="px-4 sm:px-6 py-3.5 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">
                                            Diambil Oleh</th>
                                        <th class="px-4 sm:px-6 py-3.5 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">
                                            Status</th>
                                        <th class="px-4 sm:px-6 py-3.5 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">
                                            Harga</th>
                                        <th class="px-4 sm:px-6 py-3.5 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">
                                            Deadline</th>
                                        <th class="px-4 sm:px-6 py-3.5 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">
                                            Kategori</th>
                                        <th class="px-4 sm:px-6 py-3.5 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700 bg-gray-800">
                                    @foreach ($allJobs as $job)
                                        <tr class="hover:bg-gray-700/50 transition-colors duration-150">
                                            <td class="px-4 sm:px-6 py-4">
                                                <p class="font-semibold text-gray-100 leading-tight">{{ $job->title }}</p>
                                                <p class="text-xs text-gray-400 mt-1 line-clamp-2">
                                                    {{ Str::limit($job->description, 60) }}</p>
                                            </td>
                                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                                <a href="{{ route('users.profile.show', $job->creator) }}"
                                                    class="text-sm font-medium text-blue-400 hover:text-blue-300 transition-colors">
                                                    {{ $job->creator->name }}
                                                </a>
                                            </td>
                                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                                @if ($job->assignee)
                                                    <a href="{{ route('users.profile.show', $job->assignee) }}"
                                                        class="text-sm font-medium text-blue-400 hover:text-blue-300 transition-colors">
                                                        {{ $job->assignee->name }}
                                                    </a>
                                                @else
                                                    <span class="text-sm text-gray-500">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                                @php
                                                    $statusColors = [
                                                        'belum_diambil' => 'bg-gray-500/20 text-gray-300 border-gray-500/30',
                                                        'on_progress' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                                        'selesai' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                                        'kadaluarsa' => 'bg-red-500/20 text-red-300 border-red-500/30',
                                                    ];
                                                    $color =
                                                        $statusColors[$job->status] ?? 'bg-gray-500/20 text-gray-300 border-gray-500/30';
                                                @endphp
                                                <span
                                                    class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium capitalize {{ $color }}">
                                                    {{ str_replace('_', ' ', $job->status) }}
                                                </span>
                                            </td>
                                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-1.5 text-sm font-semibold text-green-400">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <span>Rp {{ number_format($job->price, 0, ',', '.') }}</span>
                                                </div>
                                            </td>
                                            <td class="px-4 sm:px-6 py-4 whitespace-nowrap">
                                                @if ($job->deadline)
                                                    <div class="flex items-center gap-1.5 text-sm text-gray-400">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        <span>{{ $job->deadline->format('d M Y') }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500">-</span>
                                                @endif
                                            </td>
                                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach ($job->categories as $category)
                                                        <span
                                                            class="inline-flex items-center rounded-full border border-blue-500/30 bg-blue-500/20 px-2 py-0.5 text-xs font-medium text-blue-300">
                                                            {{ $category->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                                <div class="flex items-center gap-1.5 sm:gap-2 flex-wrap">
                                                    @can('update', $job)
                                                        <a href="{{ route('jobs.edit', $job) }}"
                                                            class="inline-flex items-center justify-center rounded-lg border border-gray-600 bg-gray-700 p-1.5 sm:p-2 text-gray-300 transition-all duration-200 hover:bg-gray-600 hover:text-white hover:scale-110"
                                                            title="Edit">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
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
                                                                class="inline-flex items-center justify-center rounded-lg border border-red-500/50 bg-red-500/20 p-1.5 sm:p-2 text-red-300 transition-all duration-200 hover:bg-red-500/30 hover:scale-110"
                                                                title="Hapus">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.12m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    @endcan
                                                    @if ($job->status === \App\Models\Job::STATUS_PROGRESS)
                                                        <form action="{{ route('jobs.complete', $job) }}" method="POST"
                                                            class="inline">
                                                            @csrf
                                                            <button type="submit"
                                                                class="rounded-lg bg-green-600 px-2.5 sm:px-3 py-1 sm:py-1.5 text-xs font-medium text-gray-50 hover:bg-green-500 transition-all duration-200 hover:shadow-lg hover:scale-105">
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

                        <div class="border-t border-gray-700 bg-gray-700/30 px-4 sm:px-6 py-3 sm:py-4">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                                <div class="text-xs sm:text-sm text-gray-400">
                                    Menampilkan {{ $allJobs->firstItem() ?? 0 }} sampai {{ $allJobs->lastItem() ?? 0 }} dari {{ $allJobs->total() }} hasil
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
            <section>
                <header class="mb-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 rounded-lg bg-green-500/20 border border-green-500/30">
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white">Job Tersedia</h2>
                    </div>
                    <p class="text-sm text-gray-400 ml-12">Job yang belum diambil. Kamu bisa ambil maksimal 2 sekaligus.</p>
                </header>

                @if ($availableJobs->isEmpty())
                    <div class="rounded-lg border border-gray-700 bg-gray-800 shadow-lg">
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                            </svg>
                            <p class="text-base font-medium text-gray-300 mb-1">Belum ada job tersedia</p>
                            <p class="text-sm text-gray-500">Job yang tersedia akan muncul di sini</p>
                        </div>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($availableJobs as $job)
                            <article class="rounded-lg border border-gray-700 bg-gray-800 shadow-lg hover:shadow-xl transition-all duration-200 hover:border-gray-600">
                                <div class="p-5">
                                    <div class="flex flex-col sm:flex-row items-start justify-between gap-4">
                                        <div class="flex-1 min-w-0 w-full sm:w-auto">
                                            <div class="flex items-center gap-2 mb-2">
                                                <svg class="h-4 w-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <p class="text-xs uppercase tracking-wide text-gray-500">
                                                    Dibuat oleh
                                                    <a href="{{ route('users.profile.show', $job->creator) }}"
                                                        class="font-semibold text-indigo-400 hover:text-indigo-300 hover:underline transition-colors">
                                                        {{ $job->creator->name }}
                                                    </a>
                                                </p>
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-100 mb-2 leading-tight">{{ $job->title }}</h3>
                                            <p class="text-sm text-gray-300 mb-4 leading-relaxed line-clamp-2">{{ Str::limit($job->description, 160) }}</p>
                                            <div class="flex flex-wrap gap-2">
                                                <span class="inline-flex items-center gap-1.5 rounded-full bg-green-500/20 border border-green-500/30 px-3 py-1 text-xs font-medium text-green-300">
                                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    Rp {{ number_format($job->price, 0, ',', '.') }}
                                                </span>
                                                @if ($job->deadline)
                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/20 border border-amber-500/30 px-3 py-1 text-xs font-medium text-amber-300">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        {{ $job->deadline->format('d M Y') }}
                                                    </span>
                                                @endif
                                                @foreach ($job->categories as $category)
                                                    <span class="inline-flex items-center rounded-full bg-indigo-500/20 border border-indigo-500/30 px-3 py-1 text-xs font-medium text-indigo-300">
                                                        {{ $category->name }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </div>
                                        <form action="{{ route('jobs.take', $job) }}" method="POST" class="flex-shrink-0">
                                            @csrf
                                            <button type="submit"
                                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-5 py-2.5 text-sm font-semibold text-gray-50 hover:bg-green-500 transition-all duration-200 hover:shadow-lg hover:scale-105 active:scale-95">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                                Ambil Job
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-xs sm:text-sm text-gray-400">
                            Menampilkan {{ $availableJobs->firstItem() ?? 0 }} sampai {{ $availableJobs->lastItem() ?? 0 }} dari {{ $availableJobs->total() }} hasil
                        </div>
                        <div>
                            {{ $availableJobs->onEachSide(2)->links() }}
                        </div>
                    </div>
                @endif
            </section>

            <section>
                <header class="mb-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 rounded-lg bg-blue-500/20 border border-blue-500/30">
                            <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white">Job Saya</h2>
                    </div>
                    <p class="text-sm text-gray-400 ml-12">Job yang kamu buat</p>
                </header>

                @if ($myJobs->isEmpty())
                    <div class="rounded-lg border border-gray-700 bg-gray-800 shadow-lg">
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-base font-medium text-gray-300 mb-1">Belum ada job yang kamu buat</p>
                            <p class="text-sm text-gray-500">Mulai buat job baru untuk mendapatkan bantuan</p>
                        </div>
                    </div>
                @else
                    <div class="overflow-x-auto rounded-lg border border-gray-700 bg-gray-800 shadow-lg">
                        <table class="min-w-full divide-y divide-gray-700 text-sm">
                            <thead class="bg-gray-700/50 text-left text-gray-300">
                                <tr>
                                    <th class="px-4 py-3 font-semibold">Judul</th>
                                    <th class="px-4 py-3 font-semibold">Status</th>
                                    <th class="px-4 py-3 font-semibold">Diambil Oleh</th>
                                    <th class="px-4 py-3 font-semibold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-700 bg-gray-800">
                                @foreach ($myJobs as $job)
                                    <tr class="hover:bg-gray-700/50 transition-colors duration-150">
                                        <td class="px-4 py-4">
                                            <p class="font-semibold text-gray-100 leading-tight">{{ $job->title }}</p>
                                            <p class="text-xs text-gray-400 mt-1.5 line-clamp-2">{{ Str::limit($job->description, 80) }}</p>
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $statusColors = [
                                                    'belum_diambil' => 'bg-gray-500/20 text-gray-300 border-gray-500/30',
                                                    'on_progress' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                                    'selesai' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                                    'kadaluarsa' => 'bg-red-500/20 text-red-300 border-red-500/30',
                                                ];
                                                $color = $statusColors[$job->status] ?? 'bg-gray-500/20 text-gray-300 border-gray-500/30';
                                            @endphp
                                            <span class="rounded-full border px-2.5 py-0.5 text-xs font-medium capitalize {{ $color }}">{{ str_replace('_', ' ', $job->status) }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-300">
                                            @if ($job->assignee)
                                                <a href="{{ route('users.profile.show', $job->assignee) }}"
                                                    class="text-indigo-400 hover:text-indigo-300 hover:underline transition-colors">
                                                    {{ $job->assignee->name }}
                                                </a>
                                            @else
                                                <span class="text-gray-500">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex items-center gap-2 text-xs">
                                                @can('update', $job)
                                                    <a href="{{ route('jobs.edit', $job) }}"
                                                        class="inline-flex items-center justify-center rounded-lg border border-gray-600 bg-gray-700 p-1.5 text-gray-300 transition-all duration-200 hover:bg-gray-600 hover:text-white hover:scale-110"
                                                        title="Edit">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
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
                                                            class="inline-flex items-center justify-center rounded-lg border border-red-500/50 bg-red-500/20 p-1.5 text-red-300 transition-all duration-200 hover:bg-red-500/30 hover:scale-110"
                                                            title="Hapus">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.12m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @endcan
                                                @if ($job->status === \App\Models\Job::STATUS_DONE && $job->assignee && !$job->feedback)
                                                    <a href="{{ route('jobs.feedback.create', $job) }}"
                                                        class="inline-flex items-center gap-1.5 rounded-lg bg-yellow-500/20 border border-yellow-500/30 px-2.5 py-1.5 text-xs font-medium text-yellow-300 hover:bg-yellow-500/30 transition-colors">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                                                        </svg>
                                                        Rating
                                                    </a>
                                                @endif
                                                @if ($job->status === \App\Models\Job::STATUS_DONE && $job->assignee)
                                                    <a href="{{ route('reports.create', ['job_id' => $job->job_id]) }}"
                                                        class="inline-flex items-center gap-1.5 rounded-lg border border-orange-500/30 bg-orange-500/20 px-2.5 py-1.5 text-xs font-medium text-orange-300 hover:bg-orange-500/30 transition-colors">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                        </svg>
                                                        Laporkan
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-xs sm:text-sm text-gray-400">
                            Menampilkan {{ $myJobs->firstItem() ?? 0 }} sampai {{ $myJobs->lastItem() ?? 0 }} dari {{ $myJobs->total() }} hasil
                        </div>
                        <div>
                            {{ $myJobs->onEachSide(2)->links() }}
                        </div>
                    </div>
                @endif
            </section>

            <section>
                <header class="mb-5">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="p-2 rounded-lg bg-purple-500/20 border border-purple-500/30">
                            <svg class="h-5 w-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-white">Job yang Saya Kerjakan</h2>
                    </div>
                    <p class="text-sm text-gray-400 ml-12">Job yang sedang atau pernah kamu ambil</p>
                </header>

                @if ($assignedJobs->isEmpty())
                    <div class="rounded-lg border border-gray-700 bg-gray-800 shadow-lg">
                        <div class="p-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                            </svg>
                            <p class="text-base font-medium text-gray-300 mb-1">Belum ada job yang kamu kerjakan</p>
                            <p class="text-sm text-gray-500">Ambil job dari "Job Tersedia" untuk mulai bekerja</p>
                        </div>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach ($assignedJobs as $job)
                            <article class="rounded-lg border border-gray-700 bg-gray-800 shadow-lg hover:shadow-xl transition-all duration-200 hover:border-gray-600">
                                <div class="p-5">
                                    <div class="flex flex-col sm:flex-row items-start justify-between gap-4">
                                        <div class="flex-1 min-w-0 w-full sm:w-auto">
                                            <div class="flex items-center gap-2 mb-2">
                                                <svg class="h-4 w-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                                </svg>
                                                <p class="text-xs uppercase tracking-wide text-gray-500">
                                                    Dari
                                                    <a href="{{ route('users.profile.show', $job->creator) }}"
                                                        class="font-semibold text-indigo-400 hover:text-indigo-300 hover:underline transition-colors">
                                                        {{ $job->creator->name }}
                                                    </a>
                                                </p>
                                            </div>
                                            <h3 class="text-lg font-bold text-gray-100 mb-2 leading-tight">{{ $job->title }}</h3>
                                            <p class="text-sm text-gray-300 mb-4 leading-relaxed line-clamp-2">{{ Str::limit($job->description, 160) }}</p>
                                            <div class="flex flex-wrap gap-2">
                                                @php
                                                    $statusColors = [
                                                        'belum_diambil' => 'bg-gray-500/20 text-gray-300 border-gray-500/30',
                                                        'on_progress' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                                        'selesai' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                                        'kadaluarsa' => 'bg-red-500/20 text-red-300 border-red-500/30',
                                                    ];
                                                    $color = $statusColors[$job->status] ?? 'bg-gray-500/20 text-gray-300 border-gray-500/30';
                                                @endphp
                                                <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-medium capitalize {{ $color }}">
                                                    {{ str_replace('_', ' ', $job->status) }}
                                                </span>
                                                @if ($job->deadline)
                                                    <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-500/20 border border-amber-500/30 px-3 py-1 text-xs font-medium text-amber-300">
                                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        {{ $job->deadline->format('d M Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="flex flex-wrap gap-2 flex-shrink-0">
                                            @if ($job->status === \App\Models\Job::STATUS_PROGRESS)
                                                <form action="{{ route('jobs.complete', $job) }}" method="POST">
                                                    @csrf
                                                    <button type="submit"
                                                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-gray-50 hover:bg-indigo-500 transition-all duration-200 hover:shadow-lg hover:scale-105 active:scale-95">
                                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                        Tandai Selesai
                                                    </button>
                                                </form>
                                            @endif
                                            @if ($job->status === \App\Models\Job::STATUS_DONE)
                                                <a href="{{ route('reports.create', ['job_id' => $job->job_id]) }}"
                                                    class="inline-flex items-center gap-2 rounded-lg border border-orange-500/30 bg-orange-500/20 px-4 py-2.5 text-sm font-semibold text-orange-300 hover:bg-orange-500/30 transition-all duration-200">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                                    </svg>
                                                    Laporkan
                                                </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="mt-4 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-xs sm:text-sm text-gray-400">
                            Menampilkan {{ $assignedJobs->firstItem() ?? 0 }} sampai {{ $assignedJobs->lastItem() ?? 0 }} dari {{ $assignedJobs->total() }} hasil
                        </div>
                        <div>
                            {{ $assignedJobs->onEachSide(2)->links() }}
                        </div>
                    </div>
                @endif
            </section>
        </div>
    @endif
@endsection
