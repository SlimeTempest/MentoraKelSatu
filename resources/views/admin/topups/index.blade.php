@extends('layouts.app', ['title' => 'Kelola Topup'])

@section('content')
    <script>
        // Define functions globally before Alpine.js initializes
        window.showApproveModal = function(url, userName, amount) {
            console.log('showApproveModal called', url, userName, amount);
            const modal = document.getElementById('approve-modal');
            const form = document.getElementById('approve-form');
            const userNameEl = document.getElementById('approve-user-name');
            const amountEl = document.getElementById('approve-amount');
            
            if (!modal || !form || !userNameEl || !amountEl) {
                console.error('Modal elements not found', {modal, form, userNameEl, amountEl});
                return;
            }
            
            form.action = url;
            userNameEl.textContent = userName;
            amountEl.textContent = 'Rp ' + amount;
            
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        };

        window.closeApproveModal = function() {
            const modal = document.getElementById('approve-modal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        };

        window.showRejectModal = function(url, userName, amount) {
            console.log('showRejectModal called', url, userName, amount);
            const modal = document.getElementById('reject-modal');
            const form = document.getElementById('reject-form');
            const userNameEl = document.getElementById('reject-user-name');
            const amountEl = document.getElementById('reject-amount');
            
            if (!modal || !form || !userNameEl || !amountEl) {
                console.error('Modal elements not found', {modal, form, userNameEl, amountEl});
                return;
            }
            
            form.action = url;
            userNameEl.textContent = userName;
            amountEl.textContent = 'Rp ' + amount;
            
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        };

        window.closeRejectModal = function() {
            const modal = document.getElementById('reject-modal');
            if (modal) {
                modal.style.display = 'none';
                document.body.style.overflow = '';
            }
        };
    </script>
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Daftar Top Up</h1>
        <p class="mt-1 text-sm text-gray-400">Kelola dan monitor semua Top up di sistem</p>
    </div>

    @if ($errors->has('topup'))
        <div class="mb-4 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 text-sm text-red-300">
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
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Aksi</th>
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
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium {{ $color }}">
                                            {{ $statusLabels[$topup->status] ?? $topup->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $topup->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($topup->bukti_pembayaran)
                                            <a href="{{ route('topups.proof', $topup) }}" target="_blank" class="text-blue-400 hover:text-blue-300 text-xs font-medium transition-colors hover:underline">
                                                Lihat
                                            </a>
                                        @else
                                            <span class="text-xs text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        @if ($topup->status === 'pending')
                                            <div x-data="{ open: false }" class="relative inline-block text-left">
                                                <div>
                                                    <button @click="open = !open" type="button" class="flex items-center justify-center rounded-lg border border-gray-600 bg-gray-700 p-2 text-gray-300 hover:bg-gray-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-all duration-200" id="menu-button-{{ $topup->topup_id }}" aria-expanded="true" aria-haspopup="true">
                                                        <span class="sr-only">Open options</span>
                                                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                                            <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 14a1.5 1.5 0 110 3 1.5 1.5 0 010-3z" />
                                                        </svg>
                                                    </button>
                                                </div>
                                                <div x-show="open" @click.away="open = false"
                                                    x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="transform opacity-0 scale-95"
                                                    x-transition:enter-end="transform opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="transform opacity-100 scale-100"
                                                    x-transition:leave-end="transform opacity-0 scale-95"
                                                    class="origin-top-right absolute right-0 mt-2 w-48 rounded-lg shadow-lg bg-gray-800 border border-gray-700 ring-1 ring-black ring-opacity-5 focus:outline-none z-50"
                                                    role="menu" aria-orientation="vertical" aria-labelledby="menu-button-{{ $topup->topup_id }}" tabindex="-1"
                                                    style="display: none;">
                                                    <div class="py-1" role="none">
                                                        <button type="button" onclick="event.stopPropagation(); const dropdown = event.target.closest('[x-data]'); if (dropdown && dropdown.__x) { dropdown.__x.$data.open = false; } setTimeout(() => { window.showApproveModal('{{ route('admin.topups.approve', $topup) }}', '{{ $topup->user->name }}', '{{ number_format($topup->amount, 0, ',', '.') }}'); }, 100);" class="text-green-300 hover:bg-gray-700 hover:text-green-200 block w-full text-left px-4 py-2 text-sm transition-colors duration-200 flex items-center gap-2" role="menuitem" tabindex="-1">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                                            </svg>
                                                            Setujui
                                                        </button>
                                                        <button type="button" onclick="event.stopPropagation(); const dropdown = event.target.closest('[x-data]'); if (dropdown && dropdown.__x) { dropdown.__x.$data.open = false; } setTimeout(() => { window.showRejectModal('{{ route('admin.topups.reject', $topup) }}', '{{ $topup->user->name }}', '{{ number_format($topup->amount, 0, ',', '.') }}'); }, 100);" class="text-red-300 hover:bg-gray-700 hover:text-red-200 block w-full text-left px-4 py-2 text-sm transition-colors duration-200 flex items-center gap-2" role="menuitem" tabindex="-1">
                                                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                                            </svg>
                                                            Tolak
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-500">Sudah diproses</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-700 bg-gray-700/30 px-4 sm:px-6 py-3 sm:py-4">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <!-- Info Jumlah Data -->
                        <div class="text-sm text-gray-400">
                            Menampilkan 
                            <span class="font-semibold text-gray-300">{{ $allTopups->firstItem() ?? 0 }}</span>
                            sampai 
                            <span class="font-semibold text-gray-300">{{ $allTopups->lastItem() ?? 0 }}</span>
                            dari 
                            <span class="font-semibold text-gray-300">{{ $allTopups->total() }}</span>
                            data
                        </div>

                        <!-- Pagination -->
                        <div class="flex items-center gap-2">
                            {{ $allTopups->links('pagination::default') }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>

    <!-- Approve Modal -->
    <div id="approve-modal" class="fixed inset-0 z-50 items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;">
        <div class="mx-4 w-full max-w-md transform overflow-hidden rounded-lg border border-gray-700 bg-gray-800 shadow-2xl transition-all">
            <div class="p-6">
                <div class="mb-4 flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-500/20 border border-green-500/30">
                        <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white">Setujui Topup</h3>
                        <p class="text-sm text-gray-400">Konfirmasi persetujuan topup</p>
                    </div>
                </div>
                
                <div class="mb-6 rounded-lg border border-gray-700 bg-gray-700/30 p-4">
                    <p class="text-sm text-gray-300 mb-2">
                        Apakah Anda yakin ingin menyetujui topup ini?
                    </p>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">User:</span>
                            <span class="font-medium text-white" id="approve-user-name"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Jumlah:</span>
                            <span class="font-semibold text-green-400" id="approve-amount">Rp 0</span>
                        </div>
                    </div>
                    <div class="mt-3 rounded-lg border border-amber-500/30 bg-amber-500/10 p-2">
                        <p class="text-xs text-amber-300">
                            <strong>Perhatian:</strong> Saldo user akan ditambahkan setelah persetujuan.
                        </p>
                    </div>
                </div>

                <form id="approve-form" method="POST" class="flex gap-3">
                    @csrf
                    <button type="button" onclick="window.closeApproveModal()" class="flex-1 rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm font-medium text-gray-300 transition-all hover:bg-gray-600 hover:text-white">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 rounded-lg bg-green-600 px-4 py-2.5 text-sm font-semibold text-white transition-all hover:bg-green-500 hover:shadow-lg">
                        Setujui
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="reject-modal" class="fixed inset-0 z-50 items-center justify-center bg-black/60 backdrop-blur-sm" style="display: none;">
        <div class="mx-4 w-full max-w-md transform overflow-hidden rounded-lg border border-gray-700 bg-gray-800 shadow-2xl transition-all">
            <div class="p-6">
                <div class="mb-4 flex items-center gap-4">
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-red-500/20 border border-red-500/30">
                        <svg class="h-6 w-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white">Tolak Topup</h3>
                        <p class="text-sm text-gray-400">Konfirmasi penolakan topup</p>
                    </div>
                </div>
                
                <div class="mb-6 rounded-lg border border-gray-700 bg-gray-700/30 p-4">
                    <p class="text-sm text-gray-300 mb-2">
                        Apakah Anda yakin ingin menolak topup ini?
                    </p>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-400">User:</span>
                            <span class="font-medium text-white" id="reject-user-name"></span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-400">Jumlah:</span>
                            <span class="font-semibold text-red-400" id="reject-amount">Rp 0</span>
                        </div>
                    </div>
                    <div class="mt-3 rounded-lg border border-red-500/30 bg-red-500/10 p-2">
                        <p class="text-xs text-red-300">
                            <strong>Perhatian:</strong> Topup akan ditolak dan saldo user tidak akan ditambahkan.
                        </p>
                    </div>
                </div>

                <form id="reject-form" method="POST" class="flex gap-3">
                    @csrf
                    <button type="button" onclick="window.closeRejectModal()" class="flex-1 rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm font-medium text-gray-300 transition-all hover:bg-gray-600 hover:text-white">
                        Batal
                    </button>
                    <button type="submit" class="flex-1 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition-all hover:bg-red-500 hover:shadow-lg">
                        Tolak
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Close modal when clicking outside
            const approveModal = document.getElementById('approve-modal');
            if (approveModal) {
                approveModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        window.closeApproveModal();
                    }
                });
            }

            const rejectModal = document.getElementById('reject-modal');
            if (rejectModal) {
                rejectModal.addEventListener('click', function(e) {
                    if (e.target === this) {
                        window.closeRejectModal();
                    }
                });
            }

            // Close modal with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    window.closeApproveModal();
                    window.closeRejectModal();
                }
            });
        });
    </script>
@endsection

