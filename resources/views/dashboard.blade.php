@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    <div class="space-y-6">
        <div class="rounded-lg bg-white p-8 shadow">
            <h1 class="text-2xl font-semibold text-gray-800">Halo, {{ auth()->user()->name }}!</h1>
            <p class="mt-4 text-gray-600">
                Anda masuk sebagai <span class="font-medium text-indigo-600">{{ auth()->user()->role }}</span>.
            </p>
        </div>

        <div class="grid gap-6 md:grid-cols-2">
            <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-800">Job</h2>
                @if (auth()->user()->role === 'admin')
                    <p class="mt-2 text-sm text-gray-600">Kelola dan monitor semua job di sistem.</p>
                @else
                    <p class="mt-2 text-sm text-gray-600">Buat job baru atau kelola job yang sudah kamu buat/ambil.</p>
                @endif
                <div class="mt-4 flex flex-wrap gap-3">
                    <a href="{{ route('jobs.index') }}" class="inline-flex items-center rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                        Lihat Daftar Job
                    </a>
                    @if (auth()->user()->role !== 'admin')
                        <a href="{{ route('jobs.create') }}" class="inline-flex items-center rounded border border-indigo-200 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">
                            + Job Baru
                        </a>
                    @endif
                </div>
            </div>

            @if (auth()->user()->role === 'admin')
                <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-semibold text-gray-800">Kelola Topup</h2>
                    <p class="mt-2 text-sm text-gray-600">Approve atau reject permintaan topup dari user.</p>
                    <div class="mt-4 flex flex-wrap gap-3">
                        <a href="{{ route('admin.topups.index') }}" class="inline-flex items-center rounded bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-500">
                            Kelola Topup
                        </a>
                        <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center rounded border border-indigo-200 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">
                            Pengaturan Rekening
                        </a>
                    </div>
                </div>
            @else
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
            @endif
        </div>

        @if (auth()->user()->role !== 'admin')
        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-gray-800">Batas Job Aktif</h2>
            <p class="mt-2 text-sm text-gray-600">
                Kamu bisa mengambil maksimal <strong>2 job</strong> sekaligus. Lepaskan job atau tandai selesai sebelum mengambil yang baru.
            </p>
        </div>
        @endif
    </div>
@endsection

