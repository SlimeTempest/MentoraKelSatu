@extends('layouts.app', ['title' => 'Buat Job'])

@section('content')
    <div class="rounded-lg bg-white p-8 shadow">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-semibold text-gray-800">Buat Job Baru</h1>
            <div class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-2">
                <p class="text-xs text-gray-600">Saldo Anda</p>
                <p class="text-lg font-semibold text-indigo-600">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</p>
            </div>
        </div>

        <form action="{{ route('jobs.store') }}" method="POST">
            @include('jobs.partials.form-fields', ['submitLabel' => 'Simpan'])
        </form>
    </div>
@endsection

