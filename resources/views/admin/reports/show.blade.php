@extends('layouts.app', ['title' => 'Detail Laporan'])

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">Detail Laporan</h1>
            <a href="{{ route('admin.reports.index') }}" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                Kembali
            </a>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-white">Informasi Laporan</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Dilaporkan Oleh</p>
                        <div class="mt-1 flex items-center gap-2">
                            <a href="{{ route('users.profile.show', $report->reporter) }}" class="text-sm text-gray-900 hover:text-indigo-600">
                                {{ $report->reporter->name }}
                            </a>
                            @if ($report->reporter->is_suspended)
                                <span class="rounded bg-red-100 px-2 py-0.5 text-xs text-red-700">Ditangguhkan</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-500">{{ $report->reporter->email }}</p>
                        @if ($report->reporter->role !== 'admin' && !$report->reporter->is_suspended)
                            <form id="suspend-reporter-form-{{ $report->report_id }}" action="{{ route('admin.users.suspend', $report->reporter) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="button" class="rounded bg-red-600 px-2 py-1 text-xs text-white hover:bg-red-500" onclick="customConfirm('Tangguhkan akun <strong>{{ $report->reporter->name }}</strong>? User tidak akan bisa login hingga akun diaktifkan kembali.', function(confirmed) { if(confirmed) document.getElementById('suspend-reporter-form-{{ $report->report_id }}').submit(); })">
                                    Tangguhkan Akun
                                </button>
                            </form>
                        @elseif ($report->reporter->is_suspended)
                            <form id="unsuspend-reporter-form-{{ $report->report_id }}" action="{{ route('admin.users.unsuspend', $report->reporter) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="button" class="rounded bg-green-600 px-2 py-1 text-xs text-white hover:bg-green-500" onclick="customConfirm('Aktifkan kembali akun <strong>{{ $report->reporter->name }}</strong>?', function(confirmed) { if(confirmed) document.getElementById('unsuspend-reporter-form-{{ $report->report_id }}').submit(); })">
                                    Aktifkan Akun
                                </button>
                            </form>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">User yang Dilaporkan</p>
                        @if ($report->reportedUser)
                            <div class="mt-1 flex items-center gap-2">
                                <a href="{{ route('users.profile.show', $report->reportedUser) }}" class="text-sm text-gray-900 hover:text-indigo-600">
                                    {{ $report->reportedUser->name }}
                                </a>
                                @if ($report->reportedUser->is_suspended)
                                    <span class="rounded bg-red-100 px-2 py-0.5 text-xs text-red-700">Ditangguhkan</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500">{{ $report->reportedUser->email }}</p>
                            @if ($report->reportedUser->role !== 'admin' && !$report->reportedUser->is_suspended)
                                <form id="suspend-reported-form-{{ $report->report_id }}" action="{{ route('admin.users.suspend', $report->reportedUser) }}" method="POST" class="mt-2">
                                    @csrf
                                    <button type="button" class="rounded bg-red-600 px-2 py-1 text-xs text-white hover:bg-red-500" onclick="customConfirm('Tangguhkan akun <strong>{{ $report->reportedUser->name }}</strong>? User tidak akan bisa login hingga akun diaktifkan kembali.', function(confirmed) { if(confirmed) document.getElementById('suspend-reported-form-{{ $report->report_id }}').submit(); })">
                                        Tangguhkan Akun
                                    </button>
                                </form>
                            @elseif ($report->reportedUser->is_suspended)
                                <form id="unsuspend-reported-form-{{ $report->report_id }}" action="{{ route('admin.users.unsuspend', $report->reportedUser) }}" method="POST" class="mt-2">
                                    @csrf
                                    <button type="button" class="rounded bg-green-600 px-2 py-1 text-xs text-white hover:bg-green-500" onclick="customConfirm('Aktifkan kembali akun <strong>{{ $report->reportedUser->name }}</strong>?', function(confirmed) { if(confirmed) document.getElementById('unsuspend-reported-form-{{ $report->report_id }}').submit(); })">
                                        Aktifkan Akun
                                    </button>
                                </form>
                            @endif
                        @else
                            <p class="text-sm text-gray-500">-</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Status</p>
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-100 text-yellow-700',
                                'on_review' => 'bg-blue-100 text-blue-700',
                                'done' => 'bg-green-100 text-green-700',
                            ];
                            $color = $statusColors[$report->status] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <span class="mt-1 inline-block rounded px-2 py-0.5 text-xs capitalize {{ $color }}">
                            {{ str_replace('_', ' ', $report->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Tanggal Laporan</p>
                        <p class="text-sm text-gray-900">{{ $report->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h3 class="mb-4 text-lg font-semibold text-white">Deskripsi Laporan</h3>
                <p class="whitespace-pre-wrap text-sm text-gray-700">{{ $report->description }}</p>
            </div>
        </div>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h3 class="mb-4 text-lg font-semibold text-white">Ubah Status</h3>
            <form action="{{ route('admin.reports.update-status', $report) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="flex items-center gap-4">
                    <select name="status" class="rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                        <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="on_review" {{ $report->status === 'on_review' ? 'selected' : '' }}>Sedang Ditinjau</option>
                        <option value="done" {{ $report->status === 'done' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

