@extends('layouts.app', ['title' => 'Detail Laporan'])

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-gray-100">Detail Laporan</h1>
            <a href="{{ route('admin.reports.index') }}" class="rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 text-sm font-semibold text-gray-300 hover:bg-gray-600 hover:text-gray-100 transition-all duration-200">
                Kembali
            </a>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-lg border border-gray-700 bg-gray-800 p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-semibold text-gray-100">Informasi Laporan</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm font-medium text-gray-400">Dilaporkan Oleh</p>
                        <div class="mt-1 flex items-center gap-2">
                            <a href="{{ route('users.profile.show', $report->reporter) }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                {{ $report->reporter->name }}
                            </a>
                            @if ($report->reporter->is_suspended)
                                <span class="rounded-full border border-red-500/30 bg-red-500/20 px-2 py-0.5 text-xs text-red-300">Ditangguhkan</span>
                            @endif
                        </div>
                        <p class="text-xs text-gray-400">{{ $report->reporter->email }}</p>
                        @if ($report->reporter->role !== 'admin' && !$report->reporter->is_suspended)
                            <form id="suspend-reporter-form-{{ $report->report_id }}" action="{{ route('admin.users.suspend', $report->reporter) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="button" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-gray-50 hover:bg-red-500 transition-all duration-200 hover:shadow-lg" onclick="customConfirm('Tangguhkan akun <strong>{{ $report->reporter->name }}</strong>? User tidak akan bisa login hingga akun diaktifkan kembali.', function(confirmed) { if(confirmed) document.getElementById('suspend-reporter-form-{{ $report->report_id }}').submit(); })">
                                    Tangguhkan Akun
                                </button>
                            </form>
                        @elseif ($report->reporter->is_suspended)
                            <form id="unsuspend-reporter-form-{{ $report->report_id }}" action="{{ route('admin.users.unsuspend', $report->reporter) }}" method="POST" class="mt-2">
                                @csrf
                                <button type="button" class="rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-gray-50 hover:bg-green-500 transition-all duration-200 hover:shadow-lg" onclick="customConfirm('Aktifkan kembali akun <strong>{{ $report->reporter->name }}</strong>?', function(confirmed) { if(confirmed) document.getElementById('unsuspend-reporter-form-{{ $report->report_id }}').submit(); })">
                                    Aktifkan Akun
                                </button>
                            </form>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">User yang Dilaporkan</p>
                        @if ($report->reportedUser)
                            <div class="mt-1 flex items-center gap-2">
                                <a href="{{ route('users.profile.show', $report->reportedUser) }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors">
                                    {{ $report->reportedUser->name }}
                                </a>
                                @if ($report->reportedUser->is_suspended)
                                    <span class="rounded-full border border-red-500/30 bg-red-500/20 px-2 py-0.5 text-xs text-red-300">Ditangguhkan</span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-400">{{ $report->reportedUser->email }}</p>
                            @if ($report->reportedUser->role !== 'admin' && !$report->reportedUser->is_suspended)
                                <form id="suspend-reported-form-{{ $report->report_id }}" action="{{ route('admin.users.suspend', $report->reportedUser) }}" method="POST" class="mt-2">
                                    @csrf
                                    <button type="button" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-gray-50 hover:bg-red-500 transition-all duration-200 hover:shadow-lg" onclick="customConfirm('Tangguhkan akun <strong>{{ $report->reportedUser->name }}</strong>? User tidak akan bisa login hingga akun diaktifkan kembali.', function(confirmed) { if(confirmed) document.getElementById('suspend-reported-form-{{ $report->report_id }}').submit(); })">
                                        Tangguhkan Akun
                                    </button>
                                </form>
                            @elseif ($report->reportedUser->is_suspended)
                                <form id="unsuspend-reported-form-{{ $report->report_id }}" action="{{ route('admin.users.unsuspend', $report->reportedUser) }}" method="POST" class="mt-2">
                                    @csrf
                                    <button type="button" class="rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-gray-50 hover:bg-green-500 transition-all duration-200 hover:shadow-lg" onclick="customConfirm('Aktifkan kembali akun <strong>{{ $report->reportedUser->name }}</strong>?', function(confirmed) { if(confirmed) document.getElementById('unsuspend-reported-form-{{ $report->report_id }}').submit(); })">
                                        Aktifkan Akun
                                    </button>
                                </form>
                            @endif
                        @else
                            <p class="text-sm text-gray-400">-</p>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Status</p>
                        @php
                            $statusColors = [
                                'pending' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
                                'on_review' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                'done' => 'bg-green-500/20 text-green-300 border-green-500/30',
                            ];
                            $color = $statusColors[$report->status] ?? 'bg-gray-500/20 text-gray-300 border-gray-500/30';
                        @endphp
                        <span class="mt-1 inline-block rounded-full border px-2.5 py-0.5 text-xs font-medium capitalize {{ $color }}">
                            {{ str_replace('_', ' ', $report->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-400">Tanggal Laporan</p>
                        <p class="text-sm text-gray-300">{{ $report->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-lg border border-gray-700 bg-gray-800 p-6 shadow-lg">
                <h3 class="mb-4 text-lg font-semibold text-gray-100">Deskripsi Laporan</h3>
                <p class="whitespace-pre-wrap text-sm text-gray-300 leading-relaxed">{{ $report->description }}</p>
            </div>
        </div>

        <div class="rounded-lg border border-gray-700 bg-gray-800 p-6 shadow-lg">
            <h3 class="mb-4 text-lg font-semibold text-gray-100">Ubah Status</h3>
            <form action="{{ route('admin.reports.update-status', $report) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="flex items-center gap-4">
                    <select name="status" class="rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-gray-100 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors">
                        <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="on_review" {{ $report->status === 'on_review' ? 'selected' : '' }}>Sedang Ditinjau</option>
                        <option value="done" {{ $report->status === 'done' ? 'selected' : '' }}>Selesai</option>
                    </select>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-gray-50 hover:bg-indigo-500 transition-all duration-200 hover:shadow-lg hover:scale-105">
                        Update Status
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

