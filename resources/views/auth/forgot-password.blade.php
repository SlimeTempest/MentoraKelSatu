@extends('layouts.app', ['title' => 'Lupa Password'])

@section('content')
    <div class="mx-auto max-w-md rounded-lg border border-gray-700 bg-gray-800 p-6 sm:p-8 shadow-lg">
        <h1 class="mb-6 text-2xl font-semibold text-gray-100">Reset Password</h1>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-gray-300">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-gray-100 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors"
                        placeholder="nama@email.com"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="recovery_code" class="mb-2 block text-sm font-medium text-gray-300">Recovery Code</label>
                    <input
                        type="text"
                        id="recovery_code"
                        name="recovery_code"
                        value="{{ old('recovery_code') }}"
                        required
                        maxlength="8"
                        placeholder="Masukkan 8 digit recovery code"
                        class="w-full rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-gray-100 placeholder-gray-400 uppercase focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors"
                        style="letter-spacing: 0.5em;"
                    >
                    <p class="mt-1 text-xs text-gray-400">Recovery code diberikan saat registrasi akun</p>
                    @error('recovery_code')
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
                        class="w-full rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-gray-100 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors"
                        placeholder="Minimum 8 karakter"
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
                        class="w-full rounded-lg border border-gray-600 bg-gray-700 px-3 py-2 text-sm text-gray-100 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors"
                        placeholder="Ulangi password baru"
                    >
                </div>

                <div class="flex items-center justify-between pt-2">
                    <a href="{{ route('login') }}" class="text-sm text-indigo-400 hover:text-indigo-300 transition-colors">
                        Kembali ke Login
                    </a>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-gray-50 hover:bg-indigo-500 transition-all duration-200 hover:shadow-lg hover:scale-105">
                        Reset Password
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

