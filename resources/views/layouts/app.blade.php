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

    <script>
        // Custom confirm function untuk mengganti browser alert
        function customConfirm(message, callback) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center';
            modal.style.cssText = 'display: flex; background-color: rgba(0, 0, 0, 0.3);';
            modal.innerHTML = `
                <div class="mx-4 w-full max-w-md rounded-lg bg-white p-6 shadow-xl" style="z-index: 51;">
                    <h3 class="mb-4 text-lg font-semibold text-gray-900">Konfirmasi</h3>
                    <p class="mb-6 text-sm text-gray-600">${message}</p>
                    <div class="flex justify-end gap-3">
                        <button type="button" class="cancel-btn rounded border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">
                            Batal
                        </button>
                        <button type="button" class="confirm-btn rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500">
                            Ya
                        </button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(modal);
            
            modal.querySelector('.confirm-btn').onclick = function() {
                document.body.removeChild(modal);
                if (callback) callback(true);
            };
            
            modal.querySelector('.cancel-btn').onclick = function() {
                document.body.removeChild(modal);
                if (callback) callback(false);
            };
            
            modal.onclick = function(e) {
                if (e.target === modal) {
                    document.body.removeChild(modal);
                    if (callback) callback(false);
                }
            };
        }
    </script>
</body>
</html>

