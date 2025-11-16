@extends('layouts.app', ['title' => 'Kelola User'])

@section('content')
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-800">Kelola User</h1>
        </div>

        {{-- Search and Filter --}}
        <div class="rounded-lg border border-gray-200 bg-white p-4 shadow-sm">
            <form action="{{ route('admin.users.index') }}" method="GET" class="space-y-4">
                <div class="grid gap-4 md:grid-cols-4">
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Cari Nama</label>
                        <input
                            type="text"
                            name="search"
                            value="{{ $search }}"
                            placeholder="Cari nama user..."
                            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                        >
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Role</label>
                        <select name="role" class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                            <option value="all" {{ $roleFilter === 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="mahasiswa" {{ $roleFilter === 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="dosen" {{ $roleFilter === 'dosen' ? 'selected' : '' }}>Dosen</option>
                            <option value="admin" {{ $roleFilter === 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-medium text-gray-600">Status</label>
                        <select name="suspended" class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none">
                            <option value="all" {{ $suspendedFilter === 'all' ? 'selected' : '' }}>Semua</option>
                            <option value="0" {{ $suspendedFilter === '0' ? 'selected' : '' }}>Aktif</option>
                            <option value="1" {{ $suspendedFilter === '1' ? 'selected' : '' }}>Ditangguhkan</option>
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                            Cari
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Users Table --}}
        <div class="rounded-lg border border-gray-200 bg-white shadow-sm">
            @if ($users->isEmpty())
                <div class="p-8 text-center">
                    <p class="text-sm text-gray-500">Tidak ada user yang ditemukan.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-600">User</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-600">Role</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-600">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-600">Rating</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-600">Bergabung</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase text-gray-600">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($users as $user)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            @if ($user->photo)
                                                <img src="{{ Storage::url($user->photo) }}" alt="{{ $user->name }}" class="h-10 w-10 rounded-full object-cover">
                                            @else
                                                <div class="flex h-10 w-10 items-center justify-center rounded-full bg-indigo-100 text-sm font-semibold text-indigo-600">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <a href="{{ route('users.profile.show', $user) }}" class="font-medium text-gray-900 hover:text-indigo-600">
                                                    {{ $user->name }}
                                                </a>
                                                <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <span class="rounded bg-indigo-100 px-2 py-0.5 text-xs font-medium text-indigo-700 capitalize">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3">
                                        @if ($user->is_suspended)
                                            <span class="rounded bg-red-100 px-2 py-0.5 text-xs font-medium text-red-700">Ditangguhkan</span>
                                        @else
                                            <span class="rounded bg-green-100 px-2 py-0.5 text-xs font-medium text-green-700">Aktif</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-gray-700">
                                        @if ($user->role !== 'admin')
                                            <div class="flex items-center gap-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="text-yellow-400">
                                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                                </svg>
                                                <span class="text-sm">{{ number_format($user->avg_rating, 1) }}</span>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-xs text-gray-500">
                                        {{ $user->created_at->format('d M Y') }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('users.profile.show', $user) }}" class="rounded border border-gray-300 px-2 py-1 text-xs text-gray-700 hover:bg-gray-50">
                                                Lihat
                                            </a>
                                            @if ($user->user_id !== auth()->id() && $user->role !== 'admin')
                                                @if ($user->is_suspended)
                                                    <form id="unsuspend-form-{{ $user->user_id }}" action="{{ route('admin.users.unsuspend', $user) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="button" class="rounded bg-green-600 px-2 py-1 text-xs text-white hover:bg-green-500" onclick="customConfirm('Aktifkan kembali akun <strong>{{ $user->name }}</strong>?', function(confirmed) { if(confirmed) document.getElementById('unsuspend-form-{{ $user->user_id }}').submit(); })">
                                                            Aktifkan
                                                        </button>
                                                    </form>
                                                @else
                                                    <form id="suspend-form-{{ $user->user_id }}" action="{{ route('admin.users.suspend', $user) }}" method="POST" class="inline">
                                                        @csrf
                                                        <button type="button" class="rounded bg-red-600 px-2 py-1 text-xs text-white hover:bg-red-500" onclick="customConfirm('Tangguhkan akun <strong>{{ $user->name }}</strong>? User tidak akan bisa login hingga akun diaktifkan kembali.', function(confirmed) { if(confirmed) document.getElementById('suspend-form-{{ $user->user_id }}').submit(); })">
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

                <div class="border-t border-gray-200 px-4 py-3">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    @php
        use Illuminate\Support\Facades\Storage;
    @endphp
@endsection

