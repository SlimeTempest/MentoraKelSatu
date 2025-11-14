@extends('layouts.app', ['title' => 'Kelola Topup'])

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">Kelola Topup</h1>
        <p class="mt-1 text-sm text-gray-500">Approve atau reject permintaan topup dari user.</p>
    </div>

    @if ($errors->has('topup'))
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('topup') }}
        </div>
    @endif

    @if ($pendingTopups->isNotEmpty())
        <section class="mb-8">
            <header class="mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Topup Pending</h2>
                <p class="text-sm text-gray-500">Permintaan topup yang menunggu persetujuan.</p>
            </header>

            <div class="space-y-4">
                @foreach ($pendingTopups as $topup)
                    <div class="rounded-lg border border-amber-200 bg-amber-50 p-6">
                        <div class="flex flex-wrap items-start justify-between gap-4">
                            <div class="flex-1">
                                <div class="mb-2 flex items-center gap-3">
                                    <p class="font-medium text-gray-900">{{ $topup->user->name }}</p>
                                    <span class="rounded bg-amber-100 px-2 py-0.5 text-xs text-amber-700">Pending</span>
                                </div>
                                <p class="text-sm text-gray-600">
                                    <strong>Jumlah:</strong> Rp {{ number_format($topup->amount, 0, ',', '.') }}
                                </p>
                                <p class="text-sm text-gray-600">
                                    <strong>Rekening:</strong> {{ $topup->rekening_tujuan }}
                                </p>
                                <p class="mt-2 text-xs text-gray-500">
                                    Dikirim: {{ $topup->created_at->format('d M Y, H:i') }}
                                </p>
                                @if ($topup->bukti_pembayaran)
                                    <a href="{{ asset('storage/' . $topup->bukti_pembayaran) }}" target="_blank" class="mt-2 inline-block text-sm text-indigo-600 hover:underline">
                                        Lihat Bukti Pembayaran →
                                    </a>
                                @endif
                            </div>
                            <div class="flex gap-2">
                                <form id="approve-form-{{ $topup->topup_id }}" action="{{ route('admin.topups.approve', $topup) }}" method="POST">
                                    @csrf
                                    <button type="button" onclick="customConfirm('Setujui topup ini? Saldo akan ditambahkan ke akun user.', function(confirmed) { if(confirmed) document.getElementById('approve-form-{{ $topup->topup_id }}').submit(); })" class="rounded bg-green-600 px-4 py-2 text-sm font-semibold text-white hover:bg-green-500">
                                        Setujui
                                    </button>
                                </form>
                                <form id="reject-form-{{ $topup->topup_id }}" action="{{ route('admin.topups.reject', $topup) }}" method="POST">
                                    @csrf
                                    <button type="button" onclick="customConfirm('Tolak topup ini?', function(confirmed) { if(confirmed) document.getElementById('reject-form-{{ $topup->topup_id }}').submit(); })" class="rounded bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-500">
                                        Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    <section>
        <header class="mb-4">
            <h2 class="text-lg font-semibold text-gray-800">Semua Topup</h2>
            <p class="text-sm text-gray-500">Riwayat semua permintaan topup.</p>
        </header>

        @if ($allTopups->isEmpty())
            <p class="text-sm text-gray-500">Belum ada topup di sistem.</p>
        @else
            <div class="overflow-x-auto rounded border border-gray-200 bg-white">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead class="bg-gray-50 text-left text-gray-600">
                        <tr>
                            <th class="px-4 py-3 font-medium">User</th>
                            <th class="px-4 py-3 font-medium">Jumlah</th>
                            <th class="px-4 py-3 font-medium">Rekening</th>
                            <th class="px-4 py-3 font-medium">Status</th>
                            <th class="px-4 py-3 font-medium">Tanggal</th>
                            <th class="px-4 py-3 font-medium">Bukti</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($allTopups as $topup)
                            <tr>
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $topup->user->name }}
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
                                <td class="px-4 py-3 text-gray-700">
                                    {{ $topup->created_at->format('d M Y, H:i') }}
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

            <div class="mt-4">
                {{ $allTopups->links() }}
            </div>
        @endif
    </section>
@endsection

