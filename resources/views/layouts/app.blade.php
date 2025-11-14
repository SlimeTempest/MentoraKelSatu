<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'MentoraKelSatu' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-gray-100 text-gray-900">
    @include('layouts.partials.header')

    <main class="flex-1">
        <div class="mx-auto w-full max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            @if (session('status'))
                <div class="mb-6 rounded border border-green-200 bg-green-50 px-4 py-3 text-green-700">
                    {{ session('status') }}
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    @include('layouts.partials.footer')
</body>
</html>

