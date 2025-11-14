@extends('layouts.app', ['title' => 'Riwayat Topup'])

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <h1 class="text-2xl font-semibold text-gray-800">Riwayat Topup</h1>
        <a href="{{ route('topups.create') }}" class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
            + Topup Baru
        </a>
    </div>

    @if ($topups->isEmpty())
        <div class="rounded-lg border border-gray-200 bg-white p-8 text-center">
            <p class="text-gray-500">Belum ada riwayat topup.</p>
            <a href="{{ route('topups.create') }}" class="mt-4 inline-block rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                Topup Sekarang
            </a>
        </div>
    @else
        <div class="overflow-x-auto rounded border border-gray-200 bg-white">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-gray-600">
                    <tr>
                        <th class="px-4 py-3 font-medium">Tanggal</th>
                        <th class="px-4 py-3 font-medium">Jumlah</th>
                        <th class="px-4 py-3 font-medium">Rekening Tujuan</th>
                        <th class="px-4 py-3 font-medium">Status</th>
                        <th class="px-4 py-3 font-medium">Bukti</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($topups as $topup)
                        <tr>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $topup->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900">
                                Rp {{ number_format($topup->amount, 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $topup->rekening_tujuan }}
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'approved' => 'bg-green-100 text-green-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                    ];
                                    $color = $statusColors[$topup->status] ?? 'bg-gray-100 text-gray-700';
                                    $statusLabels = [
                                        'pending' => 'Menunggu',
                                        'approved' => 'Disetujui',
                                        'rejected' => 'Ditolak',
                                    ];
                                @endphp
                                <span class="rounded px-2 py-0.5 text-xs font-medium {{ $color }}">
                                    {{ $statusLabels[$topup->status] ?? $topup->status }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                @if ($topup->bukti_pembayaran)
                                    <a href="{{ asset('storage/' . $topup->bukti_pembayaran) }}" target="_blank" class="text-indigo-600 hover:underline text-xs">
                                        Lihat
                                    </a>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection

