@extends('layouts.app', ['title' => 'Riwayat Topup'])

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-bold text-gray-100">Riwayat Topup</h1>
        <a href="{{ route('topups.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-gray-50 hover:bg-indigo-500 transition-all duration-200 hover:shadow-lg hover:scale-105">
            + Topup Baru
        </a>
    </div>

    @if ($topups->isEmpty())
        <div class="rounded-lg border border-gray-700 bg-gray-800 p-8 text-center shadow-lg">
            <p class="text-gray-400">Belum ada riwayat topup.</p>
            <a href="{{ route('topups.create') }}" class="mt-4 inline-block rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-gray-50 hover:bg-indigo-500 transition-all duration-200 hover:shadow-lg">
                Topup Sekarang
            </a>
        </div>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-700 bg-gray-800 shadow-lg">
            <table class="min-w-full divide-y divide-gray-700 text-sm">
                <thead class="bg-gray-700/50 text-left text-gray-300">
                    <tr>
                        <th class="px-4 py-3 font-semibold">Tanggal</th>
                        <th class="px-4 py-3 font-semibold">Jumlah</th>
                        <th class="px-4 py-3 font-semibold">Rekening Tujuan</th>
                        <th class="px-4 py-3 font-semibold">Status</th>
                        <th class="px-4 py-3 font-semibold">Bukti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700 bg-gray-800">
                    @foreach ($topups as $topup)
                        <tr class="hover:bg-gray-700/50 transition-colors">
                            <td class="px-4 py-3 text-gray-300">
                                {{ $topup->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-4 py-3 font-semibold text-green-400">
                                Rp {{ number_format($topup->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-gray-300">
                                {{ $topup->rekening_tujuan }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-500/20 text-amber-300 border-amber-500/30',
                                        'approved' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                        'rejected' => 'bg-red-500/20 text-red-300 border-red-500/30',
                                    ];
                                    $color = $statusColors[$topup->status] ?? 'bg-gray-500/20 text-gray-300 border-gray-500/30';
                                    $statusLabels = [
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                    ];
                                @endphp
                                <span class="rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $color }}">
                                    {{ $statusLabels[$topup->status] ?? $topup->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($topup->bukti_pembayaran)
                                    <a href="{{ route('topups.proof', $topup) }}" target="_blank" class="text-indigo-400 hover:text-indigo-300 hover:underline text-xs transition-colors">
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-xs text-gray-500">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination & Info -->
        <div class="mt-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <!-- Info Jumlah Data -->
            <div class="text-sm text-gray-400">
                Menampilkan 
                <span class="font-semibold text-gray-300">{{ $topups->firstItem() ?? 0 }}</span>
                sampai 
                <span class="font-semibold text-gray-300">{{ $topups->lastItem() ?? 0 }}</span>
                dari 
                <span class="font-semibold text-gray-300">{{ $topups->total() }}</span>
                data
            </div>

            <!-- Pagination -->
            <div class="flex items-center gap-2">
                {{ $topups->links('pagination::default') }}
            </div>
        </div>
    @endif
@endsection

