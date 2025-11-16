@extends('layouts.app', ['title' => 'Laporkan'])

@section('content')
    <div class="rounded-lg bg-white p-8 shadow">
        <h1 class="mb-6 text-2xl font-semibold text-gray-800">Laporkan Masalah</h1>

        @if ($job)
            <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
                <h3 class="font-semibold text-gray-800">Job: {{ $job->title }}</h3>
                <p class="mt-1 text-sm text-gray-600">
                    @if (auth()->user()->user_id === $job->created_by)
                        Melaporkan: <strong>{{ $job->assignee->name ?? 'Worker' }}</strong>
                    @else
                        Melaporkan: <strong>{{ $job->creator->name }}</strong>
                    @endif
                </p>
            </div>
        @elseif ($reportedUser)
            <div class="mb-6 rounded-lg border border-gray-200 bg-gray-50 p-4">
                <p class="text-sm text-gray-600">
                    Melaporkan: <strong>{{ $reportedUser->name }}</strong>
                </p>
            </div>
        @endif

        <form action="{{ route('reports.store') }}" method="POST">
            @csrf

            @if ($job)
                <input type="hidden" name="job_id" value="{{ $job->job_id }}">
            @elseif ($reportedUser)
                <input type="hidden" name="reported_user_id" value="{{ $reportedUser->user_id }}">
            @endif

            <div class="space-y-4">
                <div>
                    <label for="description" class="mb-2 block text-sm font-medium text-gray-700">Deskripsi Laporan</label>
                    <textarea
                        id="description"
                        name="description"
                        rows="6"
                        required
                        minlength="10"
                        maxlength="1000"
                        class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                        placeholder="Jelaskan masalah yang terjadi (minimal 10 karakter)..."
                    >{{ old('description') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">Minimal 10 karakter, maksimal 1000 karakter</p>
                    @error('description')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="rounded-lg border border-amber-200 bg-amber-50 p-4">
                    <p class="flex items-start gap-2 text-sm text-amber-800">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mt-0.5 flex-shrink-0">
                            <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                            <path d="M12 9v4"/>
                            <path d="M12 17h.01"/>
                        </svg>
                        <span><strong>Peringatan:</strong> Laporan yang tidak benar atau menyesatkan dapat mengakibatkan akun Anda ditangguhkan. Pastikan laporan Anda akurat dan dapat dipertanggungjawabkan.</span>
                    </p>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('jobs.index') }}" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                        Batal
                    </a>
                    <button type="submit" class="rounded bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-500">
                        Kirim Laporan
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

