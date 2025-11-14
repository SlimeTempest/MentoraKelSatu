@extends('layouts.app', ['title' => 'Dashboard'])

@section('content')
    <div class="rounded-lg bg-white p-8 shadow">
        <h1 class="text-2xl font-semibold text-gray-800">Halo, {{ auth()->user()->name }}!</h1>
        <p class="mt-4 text-gray-600">
            Anda masuk sebagai <span class="font-medium text-indigo-600">{{ auth()->user()->role }}</span>.
            Fitur dashboard lengkap akan segera hadir.
        </p>
    </div>
@endsection

