@extends('layouts.app', ['title' => 'Kelola Laporan'])

@php
    use Illuminate\Support\Str;
@endphp

@section('content')
    <div class="space-y-6">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-white">Kelola Laporan</h1>
            <p class="mt-1 text-sm text-gray-400">Kelola dan monitor semua laporan di sistem</p>
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

        <div class="rounded-lg border border-gray-700 bg-gray-800 shadow-lg overflow-hidden">
            @if ($allReports->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm text-gray-400">Belum ada laporan.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700 text-sm">
                        <thead class="bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Dilaporkan Oleh</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">User yang Dilaporkan</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Deskripsi</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700 bg-gray-800">
                            @foreach ($allReports as $report)
                                <tr class="hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-white">{{ $report->reporter->name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-300">{{ $report->reportedUser->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-300 max-w-xs truncate">{{ Str::limit($report->description, 60) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-yellow-500/20 text-yellow-300',
                                                'on_review' => 'bg-blue-500/20 text-blue-300',
                                                'done' => 'bg-green-500/20 text-green-300',
                                            ];
                                            $color = $statusColors[$report->status] ?? 'bg-gray-500/20 text-gray-300';
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium capitalize {{ $color }}">
                                            {{ str_replace('_', ' ', $report->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $report->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <a href="{{ route('admin.reports.show', $report) }}" class="rounded-lg border border-gray-600 bg-gray-700 px-3 py-1.5 text-xs font-medium text-gray-300 hover:bg-gray-600 hover:text-white transition-colors">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-700 bg-gray-700/30 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-400">
                            Showing {{ $allReports->firstItem() ?? 0 }} to {{ $allReports->lastItem() ?? 0 }} of {{ $allReports->total() }} results
                        </div>
                        <div>
                            {{ $allReports->onEachSide(2)->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

