@extends('layouts.app', ['title' => 'Redeem Codes'])

@section('content')
    <div class="mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-100">Redeem Codes</h1>
            <p class="mt-1 text-sm text-gray-400">Kelola redeem code yang telah Anda buat</p>
        </div>
        <a href="{{ route('redeem-codes.create') }}" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-gray-50 hover:bg-indigo-500 transition-all duration-200 hover:shadow-lg hover:scale-105 flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Buat Redeem Code
        </a>
    </div>

    @if ($redeemCodes->isEmpty())
        <div class="rounded-lg border border-gray-700 bg-gray-800 p-12 text-center shadow-lg">
            <div class="mx-auto mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-indigo-500/20">
                <svg class="h-8 w-8 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <p class="mb-2 text-lg font-semibold text-gray-300">Belum ada redeem code</p>
            <p class="mb-6 text-sm text-gray-400">Buat redeem code pertama Anda untuk dibagikan ke mahasiswa</p>
            <a href="{{ route('redeem-codes.create') }}" class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-gray-50 hover:bg-indigo-500 transition-all duration-200 hover:shadow-lg hover:scale-105">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Buat Redeem Code
            </a>
        </div>
    @else
        <div class="rounded-lg border border-gray-700 bg-gray-800 shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-700 text-sm">
                    <thead class="bg-gray-700/50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Code</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Jumlah</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Klaim Oleh</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Expires</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Tanggal Dibuat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-700 bg-gray-800">
                        @foreach ($redeemCodes as $redeemCode)
                            <tr class="hover:bg-gray-700/50 transition-colors">
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="font-mono font-semibold text-gray-100">{{ $redeemCode->code }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-bold text-green-400">Rp {{ number_format($redeemCode->amount, 0, ',', '.') }}</div>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @php
                                        $statusColors = [
                                            'claimed' => 'bg-green-500/20 text-green-300 border-green-500/30',
                                            'expired' => 'bg-red-500/20 text-red-300 border-red-500/30',
                                            'available' => 'bg-blue-500/20 text-blue-300 border-blue-500/30',
                                        ];
                                        if ($redeemCode->is_claimed) {
                                            $status = 'claimed';
                                            $label = 'Telah Diklaim';
                                        } elseif ($redeemCode->isExpired()) {
                                            $status = 'expired';
                                            $label = 'Kadaluarsa';
                                        } else {
                                            $status = 'available';
                                            $label = 'Tersedia';
                                        }
                                        $color = $statusColors[$status];
                                    @endphp
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $color }}">
                                        {{ $label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-300">
                                    {{ $redeemCode->claimer ? $redeemCode->claimer->name : '-' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-400">
                                    {{ $redeemCode->expires_at ? $redeemCode->expires_at->format('d M Y') : 'Tidak ada' }}
                                </td>
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-400">
                                    {{ $redeemCode->created_at->format('d M Y, H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection

