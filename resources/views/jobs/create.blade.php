@extends('layouts.app', ['title' => 'Buat Job'])

@section('content')
    <div class="rounded-lg bg-white p-8 shadow">
        <h1 class="mb-6 text-2xl font-semibold text-gray-800">Buat Job Baru</h1>

        <form action="{{ route('jobs.store') }}" method="POST">
            @include('jobs.partials.form-fields', ['submitLabel' => 'Simpan'])
        </form>
    </div>
@endsection

