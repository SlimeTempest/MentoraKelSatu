@extends('layouts.app', ['title' => 'Edit Job'])

@section('content')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-white">Edit Job</h1>
        <p class="mt-1 text-sm text-gray-400">Ubah informasi job yang sudah dibuat</p>
    </div>

    <div class="rounded-lg border border-gray-700 bg-gray-800 p-8 shadow-lg">
        <form action="{{ route('jobs.update', $job) }}" method="POST">
            @method('PUT')
            @include('jobs.partials.form-fields', ['submitLabel' => 'Perbarui'])
        </form>
    </div>
@endsection

