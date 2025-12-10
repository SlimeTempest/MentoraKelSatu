@extends('layouts.app', ['title' => 'Kelola User'])

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">Kelola User</h1>
        </div>

        {{-- Search and Filter --}}
        <div class="rounded-lg border border-gray-700 bg-gray-800 p-4 shadow-lg">
            <form action="{{ route('admin.users.index') }}" method="GET" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-400">Cari Nama</label>
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari nama user..."
                            class="w-full rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
                        >
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-400">Role</label>
                        <select name="role" class="w-full rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="all" {{ $roleFilter === 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="mahasiswa" {{ $roleFilter === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="dosen" {{ $roleFilter === 'dosen' ? 'selected' : '' }}>Dosen</option>
                            <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-400">Status</label>
                        <select name="suspended" class="w-full rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                            <option value="all" {{ $suspendedFilter === 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="0" {{ $suspendedFilter === '0' ? 'selected' : '' }}>Aktif</option>
                            <option value="1" {{ $suspendedFilter === '1' ? 'selected' : '' }}>Ditangguhkan</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-gray-50 hover:bg-blue-500 transition-all duration-200 hover:shadow-lg">
                            Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Users Table --}}
        <div class="rounded-lg border border-gray-700 bg-gray-800 shadow-lg overflow-hidden">
            @if ($users->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm text-gray-400">Tidak ada user yang ditemukan.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-700 text-sm">
                        <thead class="bg-gray-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">User</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Role</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Rating</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Bergabung</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-gray-300">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-700 bg-gray-800">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            @if ($user->photo && Storage::exists($user->photo))
                                                <img src="{{ Storage::url($user->photo) }}" alt="{{ $user->name }}" class="h-12 w-12 rounded-full object-cover border-2 border-gray-600">
                                            @elseif ($user->photo)
                                                <img src="{{ asset('storage/' . $user->photo) }}" alt="{{ $user->name }}" class="h-12 w-12 rounded-full object-cover border-2 border-gray-600" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="hidden h-12 w-12 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white border-2 border-gray-600">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            @else
                                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-600 text-sm font-semibold text-white border-2 border-gray-600">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('users.profile.show', $user) }}" class="font-semibold text-white hover:text-blue-400 transition-colors">
                                                    {{ $user->name }}
                                                </a>
                                                <p class="text-xs text-gray-400 mt-0.5">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium capitalize
                                            {{ $user->role === 'admin' ? 'bg-purple-500/20 text-purple-300 border-purple-500/30' : ($user->role === 'dosen' ? 'bg-blue-500/20 text-blue-300 border-blue-500/30' : 'bg-green-500/20 text-green-300 border-green-500/30') }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($user->is_suspended)
                                            <span class="inline-flex items-center rounded-full border border-red-500/30 bg-red-500/20 px-2.5 py-0.5 text-xs font-medium text-red-300">
                                                Ditangguhkan
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full border border-green-500/30 bg-green-500/20 px-2.5 py-0.5 text-xs font-medium text-green-300">
                                                Aktif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($user->role !== 'admin')
                                            <div class="flex items-center gap-1.5">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor" class="text-yellow-400">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                                <span class="text-sm font-medium text-white">{{ number_format($user->avg_rating, 1) }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-500">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('users.profile.show', $user) }}" class="rounded-lg border border-gray-600 bg-gray-700 px-3 py-1.5 text-xs font-medium text-gray-300 hover:bg-gray-600 hover:text-white transition-colors">
                                                Lihat
                                            </a>
                                            @if ($user->user_id !== auth()->id() && $user->role !== 'admin')
                                                @if ($user->is_suspended)
                                                    <form id="unsuspend-form-{{ $user->user_id }}" action="{{ route('admin.users.unsuspend', $user) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="button" class="rounded-lg bg-green-600 px-3 py-1.5 text-xs font-medium text-gray-50 hover:bg-green-500 transition-all duration-200 hover:shadow-lg hover:scale-105" onclick="customConfirm('Aktifkan kembali akun <strong>{{ $user->name }}</strong>?', function(confirmed) { if(confirmed) document.getElementById('unsuspend-form-{{ $user->user_id }}').submit(); })">
                                                            Aktifkan
                                                        </button>
                                                    </form>
                                                @else
                                                    <form id="suspend-form-{{ $user->user_id }}" action="{{ route('admin.users.suspend', $user) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="button" class="rounded-lg bg-red-600 px-3 py-1.5 text-xs font-medium text-gray-50 hover:bg-red-500 transition-all duration-200 hover:shadow-lg hover:scale-105" onclick="customConfirm('Tangguhkan akun <strong>{{ $user->name }}</strong>? User tidak akan bisa login hingga akun diaktifkan kembali.', function(confirmed) { if(confirmed) document.getElementById('suspend-form-{{ $user->user_id }}').submit(); })">
                                                            Tangguhkan
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-gray-700 bg-gray-700/30 px-4 sm:px-6 py-3 sm:py-4">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-xs sm:text-sm text-gray-400">
                            Menampilkan {{ $users->firstItem() ?? 0 }} sampai {{ $users->lastItem() ?? 0 }} dari {{ $users->total() }} hasil
                        </div>
                        <div>
                            {{ $users->appends(request()->query())->onEachSide(2)->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
