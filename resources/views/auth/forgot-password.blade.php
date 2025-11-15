@extends('layouts.app', ['title' => 'Lupa Password'])

@section('content')
    <div class="mx-auto max-w-md rounded-lg bg-white p-8 shadow">
        <h1 class="mb-6 text-2xl font-semibold text-gray-800">Reset Password</h1>

        <form action="{{ route('password.update') }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-gray-700">Email</label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full rounded border border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:outline-none"
                    >
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="recovery_code" class="mb-2 block text-sm font-medium text-gray-700">Recovery Code</label>
                    <input
                        type="text"
                        id="recovery_code"
                        name="recovery_code"
                        value="{{ old('recovery_code') }}"
                        required
                        maxlength="8"
                        placeholder="Masukkan 8 digit recovery code"
                        class="w-full rounded border border-gray-300 px-3 py-2 text-sm uppercase focus:border-indigo-500 focus:outline-none"
                        style="letter-spacing: 0.5em;"
                    >
                    <p class="mt-1 text-xs text-gray-500">Recovery code diberikan saat registrasi akun</p>
                    @error('recovery_code')
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

                <div class="flex items-center justify-between">
                    <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-500">
                        Kembali ke Login
                    </a>
                    <button type="submit" class="rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        Reset Password
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

