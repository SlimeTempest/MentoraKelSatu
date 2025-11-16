@extends('layouts.app', ['title' => 'Kelola Laporan'])

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-800">Kelola Laporan</h1>
        </div>

        @if ($pendingReports->isNotEmpty())
            <div class="rounded-lg border border-yellow-200 bg-yellow-50 p-6">
                <h2 class="mb-4 text-lg font-semibold text-yellow-800">Laporan Pending ({{ $pendingReports->count() }})</h2>
                <div class="space-y-3">
                    @foreach ($pendingReports as $report)
                        <div class="rounded border border-yellow-200 bg-white p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-900">
                                        Dilaporkan oleh: <span class="text-indigo-600">{{ $report->reporter->name }}</span>
                                        @if ($report->reportedUser)
                                            → <span class="text-red-600">{{ $report->reportedUser->name }}</span>
                                        @endif
                                    </p>
                                    <p class="mt-2 text-sm text-gray-700">{{ Str::limit($report->description, 150) }}</p>
                                    <p class="mt-2 text-xs text-gray-500">{{ $report->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="ml-4">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="rounded bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-white hover:bg-indigo-500">
                                        Lihat Detail
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="mb-4 text-lg font-semibold text-gray-800">Semua Laporan</h2>
            
            @if ($allReports->isEmpty())
                <p class="text-sm text-gray-500">Belum ada laporan.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50 text-left text-gray-600">
                            <tr>
                                <th class="px-4 py-3 font-medium">Dilaporkan Oleh</th>
                                <th class="px-4 py-3 font-medium">User yang Dilaporkan</th>
                                <th class="px-4 py-3 font-medium">Deskripsi</th>
                                <th class="px-4 py-3 font-medium">Status</th>
                                <th class="px-4 py-3 font-medium">Tanggal</th>
                                <th class="px-4 py-3 font-medium">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($allReports as $report)
                                <tr>
                                    <td class="px-4 py-3 text-gray-700">{{ $report->reporter->name }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $report->reportedUser->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ Str::limit($report->description, 60) }}</td>
                                    <td class="px-4 py-3">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-100 text-yellow-700',
                                                'on_review' => 'bg-blue-100 text-blue-700',
                                                'done' => 'bg-green-100 text-green-700',
                                            ];
                                            $color = $statusColors[$report->status] ?? 'bg-gray-100 text-gray-700';
                                        @endphp
                                        <span class="rounded px-2 py-0.5 text-xs capitalize {{ $color }}">
                                            {{ str_replace('_', ' ', $report->status) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">{{ $report->created_at->format('d M Y') }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('admin.reports.show', $report) }}" class="rounded border border-gray-300 px-2 py-1 text-xs text-gray-700 hover:bg-gray-50">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $allReports->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

