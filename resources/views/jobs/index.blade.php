@extends('layouts.app', ['title' => 'Daftar Job'])

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-800">Job</h1>
        @if (!$isAdmin)
            <a href="{{ route('jobs.create') }}" class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                + Job Baru
            </a>
        @endif
    </div>

    @if ($errors->has('job'))
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('job') }}
        </div>
    @endif

    @if ($isAdmin)
        {{-- Admin Monitoring View --}}
        <div class="space-y-6">
            <section>
                <header class="mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Monitoring Semua Job</h2>
                    <p class="text-sm text-gray-500">Daftar lengkap semua job di sistem untuk monitoring dan pengelolaan.</p>
                </header>

                @if (isset($allJobs) && $allJobs->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada job di sistem.</p>
                @else
                    <div class="overflow-x-auto rounded border border-gray-200 bg-white">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-left text-gray-600">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Judul</th>
                                    <th class="px-4 py-3 font-medium">Dibuat Oleh</th>
                                    <th class="px-4 py-3 font-medium">Diambil Oleh</th>
                                    <th class="px-4 py-3 font-medium">Status</th>
                                    <th class="px-4 py-3 font-medium">Harga</th>
                                    <th class="px-4 py-3 font-medium">Deadline</th>
                                    <th class="px-4 py-3 font-medium">Kategori</th>
                                    <th class="px-4 py-3 font-medium">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($allJobs as $job)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-gray-900">{{ $job->title }}</p>
                                            <p class="text-xs text-gray-500">{{ Str::limit($job->description, 60) }}</p>
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $job->creator->name }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $job->assignee->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @php
                                                $statusColors = [
                                                    'belum_diambil' => 'bg-gray-100 text-gray-700',
                                                    'on_progress' => 'bg-blue-100 text-blue-700',
                                                    'selesai' => 'bg-green-100 text-green-700',
                                                    'kadaluarsa' => 'bg-red-100 text-red-700',
                                                ];
                                                $color = $statusColors[$job->status] ?? 'bg-gray-100 text-gray-700';
                                            @endphp
                                            <span class="rounded px-2 py-0.5 text-xs capitalize {{ $color }}">
                                                {{ str_replace('_', ' ', $job->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">
                                            Rp {{ number_format($job->price, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $job->deadline ? $job->deadline->format('d M Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($job->categories as $category)
                                                    <span class="rounded bg-indigo-100 px-1.5 py-0.5 text-xs text-indigo-700">{{ $category->name }}</span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-wrap gap-2 text-xs">
                                                @can('update', $job)
                                                    <a href="{{ route('jobs.edit', $job) }}" class="rounded border border-gray-300 px-2 py-1 text-gray-700 hover:bg-gray-50">Edit</a>
                                                @endcan
                                                @can('delete', $job)
                                                    <form action="{{ route('jobs.destroy', $job) }}" method="POST" onsubmit="return confirm('Hapus job ini?')" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="rounded border border-red-200 px-2 py-1 text-red-600 hover:bg-red-50">Hapus</button>
                                                    </form>
                                                @endcan
                                                @if ($job->status === \App\Models\Job::STATUS_PROGRESS)
                                                    <form action="{{ route('jobs.complete', $job) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="submit" class="rounded bg-indigo-600 px-2 py-1 text-white hover:bg-indigo-500">Selesai</button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>
        </div>
    @else
        {{-- User View --}}
        <div class="space-y-8">
            <section>
                <header class="mb-4">
                    <h2 class="text-lg font-semibold text-gray-800">Job Tersedia</h2>
                    <p class="text-sm text-gray-500">Job yang belum diambil. Kamu bisa ambil maksimal 2 sekaligus.</p>
                </header>

                @if ($availableJobs->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada job tersedia.</p>
                @else
                    <div class="divide-y divide-gray-100 rounded border border-gray-200 bg-white">
                        @foreach ($availableJobs as $job)
                            <article class="p-4">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm uppercase text-gray-500">Dibuat oleh {{ $job->creator->name }}</p>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $job->title }}</h3>
                                        <p class="mt-2 text-sm text-gray-700">{{ Str::limit($job->description, 160) }}</p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span class="rounded bg-gray-100 px-2 py-0.5 text-xs text-gray-700">Rp {{ number_format($job->price, 0, ',', '.') }}</span>
                                            @if ($job->deadline)
                                                <span class="rounded bg-amber-100 px-2 py-0.5 text-xs text-amber-800">Deadline {{ $job->deadline->format('d M Y') }}</span>
                                            @endif
                                            @foreach ($job->categories as $category)
                                                <span class="rounded bg-indigo-100 px-2 py-0.5 text-xs text-indigo-700">{{ $category->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                    <form action="{{ route('jobs.take', $job) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="rounded bg-green-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-green-500">
                                            Ambil Job
                                        </button>
                                    </form>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>

            <section>
                <header class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Job Saya</h2>
                        <p class="text-sm text-gray-500">Job yang kamu buat.</p>
                    </div>
                </header>

                @if ($myJobs->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada job yang kamu buat.</p>
                @else
                    <div class="overflow-x-auto rounded border border-gray-200 bg-white">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-gray-50 text-left text-gray-600">
                                <tr>
                                    <th class="px-4 py-3 font-medium">Judul</th>
                                    <th class="px-4 py-3 font-medium">Status</th>
                                    <th class="px-4 py-3 font-medium">Diambil Oleh</th>
                                    <th class="px-4 py-3 font-medium">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($myJobs as $job)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <p class="font-medium text-gray-900">{{ $job->title }}</p>
                                            <p class="text-xs text-gray-500">{{ Str::limit($job->description, 80) }}</p>
                                        </td>
                                        <td class="px-4 py-3">
                                            <span class="rounded bg-gray-100 px-2 py-0.5 text-xs capitalize text-gray-700">{{ str_replace('_', ' ', $job->status) }}</span>
                                        </td>
                                        <td class="px-4 py-3 text-gray-700">
                                            {{ $job->assignee->name ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="flex flex-wrap gap-2 text-xs">
                                                @can('update', $job)
                                                    <a href="{{ route('jobs.edit', $job) }}" class="rounded border border-gray-300 px-2 py-1 text-gray-700 hover:bg-gray-50">Edit</a>
                                                @endcan
                                                @can('delete', $job)
                                                    <form action="{{ route('jobs.destroy', $job) }}" method="POST" onsubmit="return confirm('Hapus job ini?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="rounded border border-red-200 px-2 py-1 text-red-600 hover:bg-red-50">Hapus</button>
                                                    </form>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </section>

            <section>
                <header class="mb-4 flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Job yang Saya Kerjakan</h2>
                        <p class="text-sm text-gray-500">Job yang sedang atau pernah kamu ambil.</p>
                    </div>
                </header>

                @if ($assignedJobs->isEmpty())
                    <p class="text-sm text-gray-500">Belum ada job yang kamu kerjakan.</p>
                @else
                    <div class="divide-y divide-gray-100 rounded border border-gray-200 bg-white">
                        @foreach ($assignedJobs as $job)
                            <article class="p-4">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    <div>
                                        <p class="text-sm uppercase text-gray-500">Dari {{ $job->creator->name }}</p>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ $job->title }}</h3>
                                        <p class="mt-2 text-sm text-gray-700">{{ Str::limit($job->description, 160) }}</p>
                                        <div class="mt-2 flex flex-wrap gap-2">
                                            <span class="rounded bg-gray-100 px-2 py-0.5 text-xs capitalize text-gray-700">{{ str_replace('_', ' ', $job->status) }}</span>
                                            @if ($job->deadline)
                                                <span class="rounded bg-amber-100 px-2 py-0.5 text-xs text-amber-800">Deadline {{ $job->deadline->format('d M Y') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    @if ($job->status === \App\Models\Job::STATUS_PROGRESS)
                                        <form action="{{ route('jobs.complete', $job) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="rounded bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-500">
                                                Tandai Selesai
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    @endif
@endsection

