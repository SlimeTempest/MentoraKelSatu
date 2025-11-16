@extends('layouts.app', ['title' => 'Profile'])

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-800">Profile</h1>
            @if ($isOwnProfile)
                <a href="{{ route('profile.edit') }}" class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                    Edit Profile
                </a>
            @endif
        </div>

        <div class="grid gap-6 md:grid-cols-3">
            {{-- Profile Card --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <div class="text-center">
                    @if ($user->photo)
                        <img src="{{ Storage::url($user->photo) }}" alt="{{ $user->name }}" class="mx-auto mb-4 h-24 w-24 rounded-full object-cover">
                    @else
                        <div class="mx-auto mb-4 flex h-24 w-24 items-center justify-center rounded-full bg-indigo-100 text-3xl font-semibold text-indigo-600">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif
                    <h2 class="text-xl font-semibold text-gray-800">{{ $user->name }}</h2>
                    <span class="mt-2 inline-block rounded bg-indigo-100 px-3 py-1 text-xs font-medium text-indigo-700 capitalize">
                        {{ $user->role }}
                    </span>
                </div>
            </div>

            <div class="md:col-span-2 space-y-4">
                {{-- Informasi Pribadi --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="mb-4 text-lg font-semibold text-gray-800">Informasi Pribadi</h3>
                    <div class="space-y-3">
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-sm font-medium text-gray-600">Nama</span>
                            <span class="text-sm text-gray-900">{{ $user->name }}</span>
                        </div>
                        @if ($isOwnProfile)
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-sm font-medium text-gray-600">Email</span>
                            <span class="text-sm text-gray-900">{{ $user->email }}</span>
                        </div>
                        <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                            <span class="text-sm font-medium text-gray-600">No. Telepon</span>
                            <span class="text-sm text-gray-900">{{ $user->phone ?? '-' }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-medium text-gray-600">Recovery Code</span>
                            <div class="flex items-center gap-2">
                                <span id="recovery-code-text" class="font-mono text-sm text-gray-900">{{ $user->recovery_code }}</span>
                                <button 
                                    type="button"
                                    onclick="copyRecoveryCode()"
                                    class="rounded border border-gray-300 bg-white px-3 py-1 text-xs font-medium text-gray-700 hover:bg-gray-50"
                                    id="copy-btn"
                                >
                                    Copy
                                </button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                @if ($user->role !== 'admin')
                    {{-- Rating --}}
                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">Rating</h3>
                        <div class="flex items-center gap-2">
                            <div class="flex items-center">
                                @for ($i = 1; $i <= 5; $i++)
                                    @if ($i <= floor($user->avg_rating))
                                        <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @else
                                        <svg class="h-5 w-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                    @endif
                                @endfor
                            </div>
                            <span class="text-lg font-semibold text-gray-800">
                                {{ number_format($user->avg_rating, 1) }} / 5.0
                            </span>
                            @if ($user->avg_rating == 0)
                                <span class="text-sm text-gray-500">(Belum ada rating)</span>
                            @endif
                        </div>
                    </div>

                    {{-- Statistik Job --}}
                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">Statistik</h3>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-600">Job Dibuat</p>
                                <p class="text-2xl font-semibold text-indigo-600">{{ $stats['jobs_created'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Job Selesai</p>
                                <p class="text-2xl font-semibold text-green-600">{{ $stats['jobs_completed'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Sedang Dikerjakan</p>
                                <p class="text-2xl font-semibold text-blue-600">{{ $stats['jobs_in_progress'] }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-600">Total Pendapatan</p>
                                <p class="text-2xl font-semibold text-green-600">Rp {{ number_format($stats['total_earned'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>

                    @if ($isOwnProfile)
                    {{-- Saldo --}}
                    <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                        <h3 class="mb-4 text-lg font-semibold text-gray-800">Saldo</h3>
                        <p class="text-3xl font-semibold text-indigo-600">Rp {{ number_format($user->balance, 0, ',', '.') }}</p>
                    </div>
                    @endif
                @else
                    {{-- Statistik untuk Admin --}}
                    <div class="grid grid-cols-2 gap-4">
                        {{-- Statistik Laporan --}}
                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                            <h3 class="mb-4 text-lg font-semibold text-gray-800">Statistik Laporan</h3>
                            <div>
                                <p class="text-sm text-gray-600">Laporan yang Ditangani</p>
                                <p class="text-3xl font-semibold text-indigo-600">{{ $stats['handled_reports'] }}</p>
                                <p class="mt-1 text-xs text-gray-500">Laporan yang sudah direview atau diselesaikan</p>
                            </div>
                        </div>

                        {{-- Statistik Topup --}}
                        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                            <h3 class="mb-4 text-lg font-semibold text-gray-800">Statistik Topup</h3>
                            <div>
                                <p class="text-sm text-gray-600">Topup yang Ditangani</p>
                                <p class="text-3xl font-semibold text-green-600">{{ $stats['handled_topups'] }}</p>
                                <p class="mt-1 text-xs text-gray-500">Topup yang sudah di-approve atau reject</p>
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
            
            navigator.clipboard.writeText(code).then(function() {
                // Ubah text button jadi "Copied!"
                const originalText = btn.textContent;
                btn.textContent = 'Copied!';
                btn.classList.remove('border-gray-300', 'text-gray-700');
                btn.classList.add('bg-green-100', 'border-green-300', 'text-green-700');
                
                // Kembalikan setelah 2 detik
                setTimeout(function() {
                    btn.textContent = originalText;
                    btn.classList.remove('bg-green-100', 'border-green-300', 'text-green-700');
                    btn.classList.add('border-gray-300', 'text-gray-700');
                }, 2000);
            }).catch(function(err) {
                showToast('Gagal menyalin recovery code', 'error');
            });
        }

    </script>
@endsection
