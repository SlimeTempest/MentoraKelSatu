<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'MentoraKelSatu' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-gray-100 text-gray-900">
    @include('layouts.partials.header')

    <main class="flex-1">
        <div class="mx-auto w-full max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
            @if (session('status'))
                <div id="status-alert" class="mb-6 flex items-center gap-3 rounded-lg border border-green-200 bg-green-50 px-4 py-3 shadow-sm animate-fade-in">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 text-green-600">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                    <p class="flex-1 text-sm font-medium text-green-800">{!! session('status') !!}</p>
                    <button onclick="document.getElementById('status-alert').remove()" class="flex-shrink-0 text-green-600 hover:text-green-800">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
                </div>
            @endif

            @if (session('error'))
                <div id="error-alert" class="mb-6 flex items-center gap-3 rounded-lg border border-red-200 bg-red-50 px-4 py-3 shadow-sm animate-fade-in">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 text-red-600">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <p class="flex-1 text-sm font-medium text-red-800">{{ session('error') }}</p>
                    <button onclick="document.getElementById('error-alert').remove()" class="flex-shrink-0 text-red-600 hover:text-red-800">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="6" x2="6" y2="18"/>
                            <line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>
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
            modal.style.cssText = 'display: flex; background-color: rgba(0, 0, 0, 0.5); backdrop-filter: blur(2px);';
            modal.innerHTML = `
                <div class="mx-4 w-full max-w-md rounded-lg bg-white p-6 shadow-2xl animate-fade-in" style="z-index: 51;">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-100">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-600">
                                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                                <path d="M12 9v4"/>
                                <path d="M12 17h.01"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Konfirmasi</h3>
                    </div>
                    <div class="mb-6 text-sm text-gray-700" id="confirm-message">${message}</div>
                    <div class="flex justify-end gap-3">
                        <button type="button" class="cancel-btn rounded border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                            Batal
                        </button>
                        <button type="button" class="confirm-btn rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-500 transition-colors">
                            Ya, Lanjutkan
                        </button>
                    </div>
                </div>
            `;
            
            // Render HTML di message
            const messageEl = modal.querySelector('#confirm-message');
            messageEl.innerHTML = message;
            
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

    <style>
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
    </style>

    <script>
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const statusAlert = document.getElementById('status-alert');
            const errorAlert = document.getElementById('error-alert');
            
            if (statusAlert) {
                setTimeout(function() {
                    statusAlert.style.transition = 'opacity 0.3s ease-out';
                    statusAlert.style.opacity = '0';
                    setTimeout(function() {
                        statusAlert.remove();
                    }, 300);
                }, 5000);
            }
            
            if (errorAlert) {
                setTimeout(function() {
                    errorAlert.style.transition = 'opacity 0.3s ease-out';
                    errorAlert.style.opacity = '0';
                    setTimeout(function() {
                        errorAlert.remove();
                    }, 300);
                }, 5000);
            }
        });

        // Toast notification function
        function showToast(message, type = 'info') {
            const colors = {
                success: { bg: 'bg-green-50', border: 'border-green-200', text: 'text-green-800', icon: 'text-green-600' },
                error: { bg: 'bg-red-50', border: 'border-red-200', text: 'text-red-800', icon: 'text-red-600' },
                info: { bg: 'bg-blue-50', border: 'border-blue-200', text: 'text-blue-800', icon: 'text-blue-600' }
            };
            const color = colors[type] || colors.info;
            
            const toast = document.createElement('div');
            toast.className = `fixed top-4 right-4 z-50 flex items-center gap-3 rounded-lg border ${color.border} ${color.bg} px-4 py-3 shadow-lg animate-fade-in max-w-md`;
            toast.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 ${color.icon}">
                    ${type === 'success' ? '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>' : 
                      type === 'error' ? '<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>' :
                      '<circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/>'}
                </svg>
                <p class="flex-1 text-sm font-medium ${color.text}">${message}</p>
                <button onclick="this.parentElement.remove()" class="flex-shrink-0 ${color.icon} hover:opacity-70">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="18" y1="6" x2="6" y2="18"/>
                        <line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(function() {
                toast.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(function() {
                    toast.remove();
                }, 300);
            }, 4000);
        }
    </script>
</body>
</html>

