@extends('layouts.app', ['title' => 'Edit Job'])

@section('content')
    <div class="rounded-lg bg-white p-8 shadow">
        <h1 class="mb-6 text-2xl font-semibold text-gray-800">Edit Job</h1>

        <form action="{{ route('jobs.update', $job) }}" method="POST">
            @method('PUT')
            @include('jobs.partials.form-fields', ['submitLabel' => 'Perbarui'])
        </form>
    </div>
@endsection

