@extends('layouts.app', ['title' => 'Profile'])

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <div class="space-y-6">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-white">Profile</h1>
                <p class="mt-1 text-sm text-gray-400">Lihat dan kelola informasi profil Anda</p>
            </div>
            @if ($isOwnProfile)
                <a href="{{ route('profile.edit') }}" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition-all duration-200 hover:bg-blue-500 hover:shadow-lg hover:scale-105">
                    Edit Profile
                </a>
            @endif
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            {{-- Profile Card --}}
            <div class="rounded-lg border border-gray-700 bg-gray-800 p-6 shadow-lg">
                <div class="text-center">
                    <div class="mx-auto mb-4 h-24 w-24 rounded-full border-2 border-gray-600 overflow-hidden bg-blue-500/20 flex items-center justify-center">
                    @if ($user->photo)
                            <img src="{{ $user->photo_url }}?v={{ time() }}" alt="{{ $user->name }}" class="h-full w-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div class="hidden text-3xl font-semibold text-blue-400">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                    @else
                            <div class="text-3xl font-semibold text-blue-400">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                        @endif
                        </div>
                    <h2 class="text-xl font-semibold text-white">{{ $user->name }}</h2>
                    <span class="mt-2 inline-block rounded-full bg-blue-500/20 px-3 py-1 text-xs font-medium text-blue-300 capitalize">
                        {{ $user->role }}
                    </span>
                </div>
            </div>

            <div class="md:col-span-2 space-y-4">
                {{-- Informasi Pribadi --}}
                <div class="rounded-lg border border-gray-700 bg-gray-800 p-6 shadow-lg">
                    <h3 class="mb-4 text-lg font-semibold text-white">Informasi Pribadi</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-700 pb-3">
                            <span class="text-sm font-medium text-gray-400">Nama</span>
                            <span class="text-sm font-medium text-white">{{ $user->name }}</span>
                        </div>
                        @if ($isOwnProfile)
                        <div class="flex items-center justify-between border-b border-gray-700 pb-3">
                            <span class="text-sm font-medium text-gray-400">Email</span>
                            <span class="text-sm font-medium text-white">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-700 pb-3">
                            <span class="text-sm font-medium text-gray-400">No. Telepon</span>
                            <span class="text-sm font-medium text-white">{{ $user->phone ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-400">Recovery Code</span>
                            <div class="flex items-center gap-2">
                                <span id="recovery-code-text" class="font-mono text-sm text-white">{{ $user->recovery_code }}</span>
                                <button 
                                    type="button"
                                    onclick="copyRecoveryCode()"
                                    class="inline-flex items-center justify-center gap-1.5 rounded-lg border border-gray-600 bg-gray-700 px-3 py-1.5 text-xs font-medium text-gray-300 transition-all duration-200 hover:bg-gray-600 hover:text-white hover:scale-105"
                                    id="copy-btn"
                                    title="Copy"
                                >
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                    </svg>
                                    <span id="copy-btn-text">Copy</span>
                                </button>
                                <form id="generate-recovery-code-form" action="{{ route('profile.recovery-code.generate') }}" method="POST" class="inline">
                                    @csrf
                                    <button 
                                        type="button"
                                        onclick="customConfirm('Generate recovery code baru? Recovery code lama akan diganti.', function(confirmed) { if(confirmed) document.getElementById('generate-recovery-code-form').submit(); })"
                                        class="inline-flex items-center justify-center rounded-lg border border-blue-600 bg-blue-600 px-3 py-1.5 text-xs font-medium text-white transition-all duration-200 hover:bg-blue-500 hover:scale-105"
                                        title="Generate Recovery Code"
                                    >
                                        <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                        </svg>
                                        Generate
                                    </button>
                                </form>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                @if ($user->role !== 'admin')
                    {{-- Rating --}}
                    <div class="rounded-lg border border-gray-700 bg-gray-800 p-6 shadow-lg">
                        <h3 class="mb-4 text-lg font-semibold text-white">Rating</h3>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($user->avg_rating))
                                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5 text-gray-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-lg font-semibold text-white">
                                {{ number_format($user->avg_rating, 1) }} / 5.0
                            </span>
                            @if ($user->avg_rating == 0)
                                <span class="text-sm text-gray-400">(Belum ada rating)</span>
                            @endif
                        </div>
                    </div>

                    {{-- Statistik Job --}}
                    <div class="rounded-lg border border-gray-700 bg-gray-800 p-6 shadow-lg">
                        <h3 class="mb-4 text-lg font-semibold text-white">Statistik</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-400">Job Dibuat</p>
                                <p class="text-2xl font-semibold text-blue-400">{{ $stats['jobs_created'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Job Selesai</p>
                                <p class="text-2xl font-semibold text-green-400">{{ $stats['jobs_completed'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Sedang Dikerjakan</p>
                                <p class="text-2xl font-semibold text-blue-400">{{ $stats['jobs_in_progress'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-400">Total Pendapatan</p>
                                <p class="text-2xl font-semibold text-green-400">Rp {{ number_format($stats['total_earned'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    @if ($isOwnProfile)
                    {{-- Saldo --}}
                    <div class="rounded-lg border border-gray-700 bg-gray-800 p-6 shadow-lg">
                        <h3 class="mb-4 text-lg font-semibold text-white">Saldo</h3>
                        <p class="text-3xl font-semibold text-green-400">Rp {{ number_format($user->balance, 0, ',', '.') }}</p>
                    </div>
                    @endif
                @else
                    {{-- Statistik untuk Admin --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Statistik Laporan --}}
                        <div class="rounded-lg border border-gray-700 bg-gray-800 p-6 shadow-lg">
                            <h3 class="mb-4 text-lg font-semibold text-white">Statistik Laporan</h3>
                            <div>
                                <p class="text-sm text-gray-400">Laporan yang Ditangani</p>
                                <p class="text-3xl font-semibold text-blue-400">{{ $stats['handled_reports'] }}</p>
                                <p class="mt-1 text-xs text-gray-400">Laporan yang sudah direview atau diselesaikan</p>
                            </div>
                        </div>

                        {{-- Statistik Topup --}}
                        <div class="rounded-lg border border-gray-700 bg-gray-800 p-6 shadow-lg">
                            <h3 class="mb-4 text-lg font-semibold text-white">Statistik Topup</h3>
                            <div>
                                <p class="text-sm text-gray-400">Topup yang Ditangani</p>
                                <p class="text-3xl font-semibold text-green-400">{{ $stats['handled_topups'] }}</p>
                                <p class="mt-1 text-xs text-gray-400">Topup yang sudah di-approve atau reject</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        function copyRecoveryCode() {
            const code = document.getElementById('recovery-code-text').textContent;
            const btn = document.getElementById('copy-btn');
            const btnText = document.getElementById('copy-btn-text');
            
            navigator.clipboard.writeText(code).then(function() {
                // Update button dengan icon checkmark dan text "Copied!"
                const originalHTML = btn.innerHTML;
                btn.innerHTML = `
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                    </svg>
                    <span id="copy-btn-text">Copied!</span>
                `;
                btn.classList.remove('border-gray-600', 'bg-gray-700', 'text-gray-300');
                btn.classList.add('bg-green-500/20', 'border-green-500', 'text-green-300');
                
                // Kembalikan setelah 2 detik
                setTimeout(function() {
                    btn.innerHTML = `
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <span id="copy-btn-text">Copy</span>
                    `;
                    btn.classList.remove('bg-green-500/20', 'border-green-500', 'text-green-300');
                    btn.classList.add('border-gray-600', 'bg-gray-700', 'text-gray-300');
                }, 2000);
            }).catch(function(err) {
                showToast('Gagal menyalin recovery code', 'error');
            });
        }

    </script>
@endsection
