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
            <div class="rounded-lg border border-amber-500/30 bg-amber-500/10 p-6">
                <h2 class="mb-4 text-lg font-semibold text-amber-300">Laporan Pending ({{ $pendingReports->count() }})</h2>
                <div class="space-y-3">
                    @foreach ($pendingReports as $report)
                        <div class="rounded-lg border border-amber-500/30 bg-gray-800 p-4">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <p class="font-medium text-gray-100">
                                        Dilaporkan oleh: <span class="text-indigo-400">{{ $report->reporter->name }}</span>
                                        @if ($report->reportedUser)
                                            → <span class="text-red-400">{{ $report->reportedUser->name }}</span>
                                        @endif
                                    </p>
                                    <p class="mt-2 text-sm text-gray-300">{{ Str::limit($report->description, 150) }}</p>
                                    <p class="mt-2 text-xs text-gray-400">{{ $report->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="ml-4">
                                    <a href="{{ route('admin.reports.show', $report) }}" class="rounded-lg bg-indigo-600 px-3 py-1.5 text-sm font-semibold text-gray-50 hover:bg-indigo-500 transition-all duration-200 hover:shadow-lg">
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
                                                'pending' => 'bg-yellow-500/20 text-yellow-300 border-yellow-500/30',
                                                'on_review' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                                'done' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                            ];
                                            $color = $statusColors[$report->status] ?? 'bg-gray-500/20 text-gray-300 border-gray-500/30';
                                        @endphp
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium capitalize {{ $color }}">
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

                <div class="border-t border-gray-700 bg-gray-700/30 px-4 sm:px-6 py-3 sm:py-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-xs sm:text-sm text-gray-400">
                            Menampilkan {{ $allReports->firstItem() ?? 0 }} sampai {{ $allReports->lastItem() ?? 0 }} dari {{ $allReports->total() }} hasil
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

