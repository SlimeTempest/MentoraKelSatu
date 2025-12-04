@extends('layouts.app', ['title' => 'Kelola Topup'])

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Daftar Top Up</h1>
        <p class="mt-1 text-sm text-gray-400">Kelola dan monitor semua Top up di sistem</p>
    </div>

    @if ($errors->has('topup'))
        <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('topup') }}
        </div>
    @endif

    <section>
        @if ($allTopups->isEmpty())
            <div class="rounded-lg border border-gray-700 bg-gray-800 shadow-lg">
                <div class="p-8 text-center">
                    <p class="text-sm text-gray-400">Belum ada topup di sistem.</p>
                </div>
            </div>
        @else
            <div class="rounded-lg border border-gray-700 bg-gray-800 shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700 text-sm">
                        <thead class="bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">User</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Jumlah</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Rekening</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Tanggal</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Bukti</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700 bg-gray-800">
                            @foreach ($allTopups as $topup)
                                <tr class="hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-white">{{ $topup->user->name }}</div>
                                        <div class="text-xs text-gray-400">{{ $topup->user->email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-green-400">Rp {{ number_format($topup->amount, 0, ',', '.') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                                        {{ $topup->rekening_tujuan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusColors = [
                                                'pending' => 'bg-amber-500/20 text-amber-300',
                                                'approved' => 'bg-green-500/20 text-green-300',
                                                'rejected' => 'bg-red-500/20 text-red-300',
                                            ];
                                            $color = $statusColors[$topup->status] ?? 'bg-gray-500/20 text-gray-300';
                                            $statusLabels = [
                                                'pending' => 'Menunggu',
                                                'approved' => 'Disetujui',
                                                'rejected' => 'Ditolak',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $color }}">
                                            {{ $statusLabels[$topup->status] ?? $topup->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $topup->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($topup->bukti_pembayaran)
                                            <a href="{{ asset('storage/' . $topup->bukti_pembayaran) }}" target="_blank" class="text-blue-400 hover:text-blue-300 text-xs font-medium transition-colors">
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

                <div class="border-t border-gray-700 bg-gray-700/30 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div class="text-sm text-gray-400">
                            Showing {{ $allTopups->firstItem() ?? 0 }} to {{ $allTopups->lastItem() ?? 0 }} of {{ $allTopups->total() }} results
                        </div>
                        <div>
                            {{ $allTopups->onEachSide(2)->links() }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
@endsection

