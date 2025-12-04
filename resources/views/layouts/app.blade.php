<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'MentoraKelSatu' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
</head>
<body class="bg-gray-900 text-gray-100">
    @auth
        <!-- Sidebar -->
        @include('layouts.partials.sidebar')

        <!-- Topbar -->
        @include('layouts.partials.topbar')

        <!-- Main Content -->
        <main class="ml-0 mt-16 min-h-screen transition-all duration-300 lg:ml-64">
            <div class="p-6">
                @if (session('status'))
                    <div id="status-alert" class="mb-6 flex items-center gap-3 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 shadow-sm animate-fade-in">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 text-green-400">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                        <p class="flex-1 text-sm font-medium text-green-300">{!! session('status') !!}</p>
                        <button onclick="document.getElementById('status-alert').remove()" class="flex-shrink-0 text-green-400 hover:text-green-300">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <line x1="18" y1="6" x2="6" y2="18"/>
                                <line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </button>
                    </div>
                @endif

                @if (session('error'))
                    <div id="error-alert" class="mb-6 flex items-center gap-3 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 shadow-sm animate-fade-in">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 text-red-400">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="12" y1="8" x2="12" y2="12"/>
                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                        <p class="flex-1 text-sm font-medium text-red-300">{{ session('error') }}</p>
                        <button onclick="document.getElementById('error-alert').remove()" class="flex-shrink-0 text-red-400 hover:text-red-300">
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
    @else
        <!-- Guest Layout (Login/Register) -->
        <div class="flex min-h-screen flex-col bg-gray-900">
            <!-- Header -->
            <header class="sticky top-0 z-10 border-b border-gray-700 bg-gray-800 shadow-sm">
                @include('layouts.partials.header')
            </header>

            <!-- Main Content -->
            <main class="flex-1 flex items-center justify-center py-12">
                <div class="w-full max-w-5xl px-4 sm:px-6 lg:px-8">
                    @if (session('status'))
                        <div id="status-alert" class="mb-6 flex items-center gap-3 rounded-lg border border-green-500/30 bg-green-500/10 px-4 py-3 shadow-sm animate-fade-in">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 text-green-400">
                                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                <polyline points="22 4 12 14.01 9 11.01"/>
                            </svg>
                            <p class="flex-1 text-sm font-medium text-green-300">{!! session('status') !!}</p>
                            <button onclick="document.getElementById('status-alert').remove()" class="flex-shrink-0 text-green-400 hover:text-green-300">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="18" y1="6" x2="6" y2="18"/>
                                    <line x1="6" y1="6" x2="18" y2="18"/>
                                </svg>
                            </button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div id="error-alert" class="mb-6 flex items-center gap-3 rounded-lg border border-red-500/30 bg-red-500/10 px-4 py-3 shadow-sm animate-fade-in">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="flex-shrink-0 text-red-400">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            <p class="flex-1 text-sm font-medium text-red-300">{{ session('error') }}</p>
                            <button onclick="document.getElementById('error-alert').remove()" class="flex-shrink-0 text-red-400 hover:text-red-300">
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

            <!-- Footer -->
            <footer class="mt-auto border-t border-gray-700 bg-gray-800">
                @include('layouts.partials.footer')
            </footer>
        </div>
    @endauth

    <script>
        // Custom confirm function untuk mengganti browser alert
        function customConfirm(message, callback) {
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 z-50 flex items-center justify-center';
            modal.style.cssText = 'display: flex; background-color: rgba(0, 0, 0, 0.75); backdrop-filter: blur(4px);';
            modal.innerHTML = `
                <div class="mx-4 w-full max-w-md rounded-lg border border-gray-700 bg-gray-800 p-6 shadow-2xl animate-fade-in" style="z-index: 51;">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-amber-500/20 border border-amber-500/30">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-amber-400">
                                <path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/>
                                <path d="M12 9v4"/>
                                <path d="M12 17h.01"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-white">Konfirmasi</h3>
                    </div>
                    <div class="mb-6 text-sm text-gray-300" id="confirm-message">${message}</div>
                    <div class="flex justify-end gap-3">
                        <button type="button" class="cancel-btn rounded-lg border border-gray-600 bg-gray-700 px-4 py-2 text-sm font-medium text-gray-300 transition-all duration-200 hover:bg-gray-600 hover:text-white">
                            Batal
                        </button>
                        <button type="button" class="confirm-btn rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white transition-all duration-200 hover:bg-blue-500 hover:shadow-lg">
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
        // Sidebar Toggle (Mobile)
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarOverlay = document.getElementById('sidebar-overlay');
            const sidebarToggle = document.getElementById('sidebar-toggle');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                sidebarOverlay.classList.remove('hidden');
                setTimeout(() => {
                    sidebarOverlay.classList.remove('opacity-0');
                    sidebarOverlay.classList.add('opacity-100');
                }, 10);
            }

            function closeSidebar() {
                sidebarOverlay.classList.remove('opacity-100');
                sidebarOverlay.classList.add('opacity-0');
                setTimeout(() => {
                    sidebar.classList.add('-translate-x-full');
                    sidebarOverlay.classList.add('hidden');
                }, 300);
            }

            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    if (sidebar.classList.contains('-translate-x-full')) {
                        openSidebar();
                    } else {
                        closeSidebar();
                    }
                });
            }

            if (sidebarOverlay) {
                sidebarOverlay.addEventListener('click', function() {
                    closeSidebar();
                });
            }

            // Auto-dismiss alerts after 5 seconds
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

