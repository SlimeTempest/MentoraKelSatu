@extends('layouts.app', ['title' => 'Buat Job'])

@section('content')
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Buat Job Baru</h1>
            <p class="mt-1 text-sm text-gray-400">Buat job baru untuk dikerjakan oleh freelancer</p>
        </div>
        <div class="rounded-lg border border-blue-500/30 bg-blue-500/20 px-4 py-2.5">
            <p class="text-xs text-gray-400">Saldo Anda</p>
            <p class="text-lg font-semibold text-green-400">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <div class="rounded-lg border border-gray-700 bg-gray-800 p-8 shadow-lg">
        <form action="{{ route('jobs.store') }}" method="POST">
            @include('jobs.partials.form-fields', ['submitLabel' => 'Simpan'])
        </form>
    </div>
@endsection
