@extends('layouts.app', ['title' => 'Edit Profile'])

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Edit Profile</h1>
        <p class="mt-1 text-sm text-gray-400">Ubah informasi profil Anda</p>
    </div>

    <div class="rounded-lg border border-gray-700 bg-gray-800 p-8 shadow-lg">

        <div class="space-y-6">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    {{-- Photo Upload --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-300">Foto Profil</label>
                        <div class="flex items-center gap-4">
                            <div id="photo-preview" class="relative h-24 w-24 rounded-full border-2 border-gray-600 overflow-hidden bg-blue-500/20 flex items-center justify-center">
                                @if ($user->photo)
                                    <img id="preview-img" src="{{ $user->photo_url }}?v={{ time() }}" alt="{{ $user->name }}" class="h-full w-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div id="preview-initial" class="hidden text-3xl font-semibold text-blue-400">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                @else
                                    <div id="preview-initial" class="text-3xl font-semibold text-blue-400">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input
                                    type="file"
                                    id="photo"
                                    name="photo"
                                    accept="image/jpeg,image/png,image/jpg"
                                    class="block w-full text-sm text-gray-400 file:mr-4 file:rounded-lg file:border-0 file:bg-blue-600 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-blue-500 transition-colors"
                                >
                                <p class="mt-1 text-xs text-gray-400">Format: JPG, PNG. Maksimal 2MB</p>
                            </div>
                        </div>
                        @error('photo')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Name --}}
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-gray-300">Nama</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors"
                            placeholder="Masukkan nama lengkap"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-gray-300">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors"
                            placeholder="Masukkan alamat email"
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="mb-2 block text-sm font-medium text-gray-300">No. Telepon</label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $user->phone) }}"
                            placeholder="08xxxxxxxxxx"
                            class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors"
                        >
                        @error('phone')
                            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('profile.show') }}" class="rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 text-sm font-semibold text-gray-300 transition-all duration-200 hover:bg-gray-600 hover:text-white">
                            Batal
                        </a>
                        <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition-all duration-200 hover:bg-blue-500 hover:shadow-lg">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>

            {{-- Change Password --}}
            <div class="border-t border-gray-700 pt-6">
                <h2 class="mb-4 text-lg font-semibold text-white">Ubah Password</h2>
                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="mb-2 block text-sm font-medium text-gray-300">Password Saat Ini</label>
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                required
                                class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors"
                                placeholder="Masukkan password saat ini"
                            >
                            @error('current_password')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-medium text-gray-300">Password Baru</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                minlength="8"
                                class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors"
                                placeholder="Masukkan password baru (min. 8 karakter)"
                            >
                            @error('password')
                                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-300">Konfirmasi Password Baru</label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                minlength="8"
                                class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-white placeholder-gray-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/50 transition-colors"
                                placeholder="Konfirmasi password baru"
                            >
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition-all duration-200 hover:bg-blue-500 hover:shadow-lg">
                                Ubah Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Preview foto saat user memilih file
        document.getElementById('photo').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewContainer = document.getElementById('photo-preview');
                    const previewInitial = document.getElementById('preview-initial');
                    
                    // Hapus preview initial jika ada
                    if (previewInitial) {
                        previewInitial.remove();
                    }
                    
                    // Hapus preview img lama jika ada
                    const oldImg = document.getElementById('preview-img');
                    if (oldImg) {
                        oldImg.remove();
                    }
                    
                    // Buat img baru untuk preview
                    const img = document.createElement('img');
                    img.id = 'preview-img';
                    img.src = e.target.result;
                    img.className = 'h-24 w-24 rounded-full object-cover border-2 border-blue-500';
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
