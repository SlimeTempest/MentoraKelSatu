@extends('layouts.app', ['title' => 'Edit Profile'])

@php
    use Illuminate\Support\Facades\Storage;
@endphp

@section('content')
    <div class="rounded-lg bg-white p-8 shadow">
        <h1 class="mb-6 text-2xl font-semibold text-gray-800">Edit Profile</h1>

        <div class="space-y-6">
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="space-y-4">
                    {{-- Photo Upload --}}
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">Foto Profil</label>
                        <div class="flex items-center gap-4">
                            <div id="photo-preview" class="relative">
                                @if ($user->photo)
                                    <img id="preview-img" src="{{ Storage::url($user->photo) }}" alt="{{ $user->name }}" class="h-24 w-24 rounded-full object-cover border-2 border-gray-200">
                                @else
                                    <div id="preview-initial" class="flex h-24 w-24 items-center justify-center rounded-full bg-indigo-100 text-3xl font-semibold text-indigo-600 border-2 border-gray-200">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex-1">
                                <input
                                    type="file"
                                    id="photo"
                                    name="photo"
                                    accept="image/jpeg,image/png,image/jpg"
                                    class="block w-full text-sm text-gray-500 file:mr-4 file:rounded file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100"
                                >
                                <p class="mt-1 text-xs text-gray-500">Format: JPG, PNG. Maksimal 2MB</p>
                            </div>
                        </div>
                        @error('photo')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Name --}}
                    <div>
                        <label for="name" class="mb-2 block text-sm font-medium text-gray-700">Nama</label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                        >
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="email" class="mb-2 block text-sm font-medium text-gray-700">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="{{ old('email', $user->email) }}"
                            required
                            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                        >
                        @error('email')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Phone --}}
                    <div>
                        <label for="phone" class="mb-2 block text-sm font-medium text-gray-700">No. Telepon</label>
                        <input
                            type="text"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $user->phone) }}"
                            placeholder="08xxxxxxxxxx"
                            class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                        >
                        @error('phone')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('profile.show') }}" class="rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Batal
                        </a>
                        <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>

            {{-- Change Password --}}
            <div class="border-t border-gray-200 pt-6">
                <h2 class="mb-4 text-lg font-semibold text-gray-800">Ubah Password</h2>
                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="space-y-4">
                        <div>
                            <label for="current_password" class="mb-2 block text-sm font-medium text-gray-700">Password Saat Ini</label>
                            <input
                                type="password"
                                id="current_password"
                                name="current_password"
                                required
                                class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                            >
                            @error('current_password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password" class="mb-2 block text-sm font-medium text-gray-700">Password Baru</label>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                minlength="8"
                                class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                            >
                            @error('password')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700">Konfirmasi Password Baru</label>
                            <input
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                minlength="8"
                                class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                            >
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
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
                    img.className = 'h-24 w-24 rounded-full object-cover border-2 border-indigo-300';
                    previewContainer.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
@endsection
