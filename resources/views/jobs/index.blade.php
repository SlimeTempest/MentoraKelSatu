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
                        <div class="p-8 text-center">
                            <p class="text-sm text-gray-400">Belum ada job di sistem.</p>
                        </div>
                    </div>
                @else
                    <div class="rounded-lg border border-gray-700 bg-gray-800 shadow-lg overflow-hidden">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-700 text-sm">
                                <thead class="bg-gray-700/50">
                                    <tr>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">
                                            Judul</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">
                                            Dibuat Oleh</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">
                                            Diambil Oleh</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">
                                            Status</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">
                                            Harga</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">
                                            Deadline</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">
                                            Kategori</th>
                                        <th class="px-4 sm:px-6 py-3 sm:py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">
                                            Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-700 bg-gray-800">
                                    @foreach ($allJobs as $job)
                                        <tr class="hover:bg-gray-700/50 transition-colors">
                                            <td class="px-4 sm:px-6 py-3 sm:py-4">
                                                <p class="font-semibold text-gray-100">{{ $job->title }}</p>
                                                <p class="text-xs text-gray-400 mt-0.5">
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
                                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap">
                                                <div class="text-sm font-semibold text-green-400">Rp
                                                    {{ number_format($job->price, 0, ',', '.') }}</div>
                                            </td>
                                            <td class="px-4 sm:px-6 py-3 sm:py-4 whitespace-nowrap text-sm text-gray-400">
                                                {{ $job->deadline ? $job->deadline->format('d M Y') : '-' }}
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
                <header class="mb-4">
                    <h2 class="text-xl font-bold text-white">Job Tersedia</h2>
                    <p class="text-sm text-gray-400 mt-1">Job yang belum diambil. Kamu bisa ambil maksimal 2 sekaligus.</p>
                </header>

                @if ($availableJobs->isEmpty())
                    <p class="text-sm text-gray-400">Belum ada job tersedia.</p>
                @else
                    <div class="divide-y divide-gray-700 rounded-lg border border-gray-700 bg-gray-800 shadow-lg">
                        @foreach ($availableJobs as $job)
                            <article class="p-4 hover:bg-gray-700/50 transition-colors">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm uppercase text-gray-400">
                                            Dibuat oleh
                                            <a href="{{ route('users.profile.show', $job->creator) }}"
                                                class="font-semibold text-indigo-400 hover:text-indigo-300 hover:underline transition-colors">
                                                {{ $job->creator->name }}
                                            </a>
                                        </p>
                                        <h3 class="text-lg font-semibold text-gray-100 mt-1">{{ $job->title }}</h3>
                                        <p class="mt-2 text-sm text-gray-300">{{ Str::limit($job->description, 160) }}</p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span class="rounded-full bg-green-500/20 border border-green-500/30 px-2.5 py-0.5 text-xs font-medium text-green-300">Rp
                                                {{ number_format($job->price, 0, ',', '.') }}</span>
                                            @if ($job->deadline)
                                                <span
                                                    class="rounded-full bg-amber-500/20 border border-amber-500/30 px-2.5 py-0.5 text-xs font-medium text-amber-300">Deadline
                                                    {{ $job->deadline->format('d M Y') }}</span>
                                            @endif
                                            @foreach ($job->categories as $category)
                                                <span
                                                    class="rounded-full bg-indigo-500/20 border border-indigo-500/30 px-2.5 py-0.5 text-xs font-medium text-indigo-300">{{ $category->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <form action="{{ route('jobs.take', $job) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-gray-50 hover:bg-green-500 transition-all duration-200 hover:shadow-lg hover:scale-105">
                                            Ambil Job
                                        </button>
                                    </form>
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
                <header class="mb-4">
                    <h2 class="text-xl font-bold text-white">Job Saya</h2>
                    <p class="text-sm text-gray-400 mt-1">Job yang kamu buat</p>
                </header>

                @if ($myJobs->isEmpty())
                    <p class="text-sm text-gray-400">Belum ada job yang kamu buat.</p>
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
                                    <tr class="hover:bg-gray-700/50 transition-colors">
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-gray-100">{{ $job->title }}</p>
                                            <p class="text-xs text-gray-400 mt-0.5">{{ Str::limit($job->description, 80) }}</p>
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
                                                        class="inline-flex items-center gap-1 rounded-lg bg-yellow-500/20 border border-yellow-500/30 px-2 py-1 text-yellow-300 hover:bg-yellow-500/30 transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                            height="14" viewBox="0 0 24 24" fill="currentColor"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <polygon
                                                                points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2" />
                                                        </svg>
                                                        Rating
                                                    </a>
                                                @endif
                                                @if ($job->status === \App\Models\Job::STATUS_DONE && $job->assignee)
                                                    <a href="{{ route('reports.create', ['job_id' => $job->job_id]) }}"
                                                        class="inline-flex items-center gap-1 rounded-lg border border-orange-500/30 bg-orange-500/20 px-2 py-1 text-orange-300 hover:bg-orange-500/30 transition-colors">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                            height="14" viewBox="0 0 24 24" fill="none"
                                                            stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                            <path
                                                                d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                                                            <line x1="4" x2="4" y1="22"
                                                                y2="15" />
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
                <header class="mb-4">
                    <h2 class="text-xl font-bold text-white">Job yang Saya Kerjakan</h2>
                    <p class="text-sm text-gray-400 mt-1">Job yang sedang atau pernah kamu ambil</p>
                </header>

                @if ($assignedJobs->isEmpty())
                    <p class="text-sm text-gray-400">Belum ada job yang kamu kerjakan.</p>
                @else
                    <div class="divide-y divide-gray-700 rounded-lg border border-gray-700 bg-gray-800 shadow-lg">
                        @foreach ($assignedJobs as $job)
                            <article class="p-4 hover:bg-gray-700/50 transition-colors">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm uppercase text-gray-400">
                                            Dari
                                            <a href="{{ route('users.profile.show', $job->creator) }}"
                                                class="font-semibold text-indigo-400 hover:text-indigo-300 hover:underline transition-colors">
                                                {{ $job->creator->name }}
                                            </a>
                                        </p>
                                        <h3 class="text-lg font-semibold text-gray-100 mt-1">{{ $job->title }}</h3>
                                        <p class="mt-2 text-sm text-gray-300">{{ Str::limit($job->description, 160) }}</p>
                                        <div class="mt-2 flex flex-wrap gap-2">
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
                                            @if ($job->deadline)
                                                <span
                                                    class="rounded-full bg-amber-500/20 border border-amber-500/30 px-2.5 py-0.5 text-xs font-medium text-amber-300">Deadline
                                                    {{ $job->deadline->format('d M Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex flex-wrap gap-2">
                                        @if ($job->status === \App\Models\Job::STATUS_PROGRESS)
                                            <form action="{{ route('jobs.complete', $job) }}" method="POST">
                                                @csrf
                                                <button type="submit"
                                                    class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-gray-50 hover:bg-indigo-500 transition-all duration-200 hover:shadow-lg hover:scale-105">
                                                    Tandai Selesai
                                                </button>
                                            </form>
                                        @endif
                                        @if ($job->status === \App\Models\Job::STATUS_DONE)
                                            <a href="{{ route('reports.create', ['job_id' => $job->job_id]) }}"
                                                class="inline-flex items-center gap-1.5 rounded-lg border border-orange-500/30 bg-orange-500/20 px-3 py-1.5 text-sm font-semibold text-orange-300 hover:bg-orange-500/30 transition-all duration-200">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                                    stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z" />
                                                    <line x1="4" x2="4" y1="22" y2="15" />
                                                </svg>
                                                Laporkan
                                            </a>
                                        @endif
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
