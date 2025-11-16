@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    <div class="space-y-6">
        {{-- Welcome Card --}}
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h1 class="text-2xl font-semibold text-gray-800">Halo, {{ auth()->user()->name }}!</h1>
            <p class="mt-2 text-gray-600">
                Anda masuk sebagai <span class="font-medium text-indigo-600 capitalize">{{ auth()->user()->role }}</span>.
            </p>
        </div>

        @if (auth()->user()->role === 'admin')
            {{-- Admin Dashboard --}}
            <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-4">
                {{-- Job Card --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-800">Job</h2>
                    <p class="mt-2 text-sm text-gray-600">Kelola dan monitor semua job di sistem.</p>
                    <div class="mt-4">
                        <a href="{{ route('jobs.index') }}" class="inline-flex w-full items-center justify-center rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                            Lihat Daftar Job
                        </a>
                    </div>
                </div>

                {{-- Topup Card --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-800">Kelola Topup</h2>
                    <p class="mt-2 text-sm text-gray-600">Approve atau reject permintaan topup dari user.</p>
                    <div class="mt-4 space-y-2">
                        <a href="{{ route('admin.topups.index') }}" class="inline-flex w-full items-center justify-center rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                            Kelola Topup
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="inline-flex w-full items-center justify-center rounded border border-indigo-200 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">
                            Pengaturan Rekening
                        </a>
                    </div>
                </div>

                {{-- Reports Card --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-800">Kelola Laporan</h2>
                    <p class="mt-2 text-sm text-gray-600">Tinjau dan tangani laporan dari user.</p>
                    <div class="mt-4">
                        <a href="{{ route('admin.reports.index') }}" class="inline-flex w-full items-center justify-center rounded bg-orange-600 px-4 py-2 text-sm font-semibold text-white hover:bg-orange-500">
                            Kelola Laporan
                        </a>
                    </div>
                </div>

                {{-- Users Card --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-800">Kelola User</h2>
                    <p class="mt-2 text-sm text-gray-600">Lihat, cari, dan kelola semua user.</p>
                    <div class="mt-4">
                        <a href="{{ route('admin.users.index') }}" class="inline-flex w-full items-center justify-center rounded bg-purple-600 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-500">
                            Kelola User
                        </a>
                    </div>
                </div>
            </div>
        @else
            {{-- User Dashboard --}}
            <div class="grid gap-6 md:grid-cols-2">
                {{-- Job Card --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-800">Job</h2>
                    <p class="mt-2 text-sm text-gray-600">Buat job baru atau kelola job yang sudah kamu buat/ambil.</p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('jobs.index') }}" class="inline-flex items-center rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                            Lihat Daftar Job
                        </a>
                        <a href="{{ route('jobs.create') }}" class="inline-flex items-center rounded border border-indigo-200 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">
                            + Job Baru
                        </a>
                    </div>
                </div>

                {{-- Saldo & Topup Card --}}
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-800">Saldo & Topup</h2>
                    <p class="mt-2 text-sm text-gray-600">
                        Saldo Anda: <strong class="text-indigo-600">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</strong>
                    </p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('topups.create') }}" class="inline-flex items-center rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                            Topup Saldo
                        </a>
                        <a href="{{ route('topups.index') }}" class="inline-flex items-center rounded border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">
                            Lihat Riwayat
                        </a>
                    </div>
                </div>
            </div>

            {{-- Info Card untuk User --}}
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-800">Batas Job Aktif</h2>
                <p class="mt-2 text-sm text-gray-600">
                    Kamu bisa mengambil maksimal <strong>2 job</strong> sekaligus. Lepaskan job atau tandai selesai sebelum mengambil yang baru.
                </p>
            </div>
        @endif
    </div>
@endsection

