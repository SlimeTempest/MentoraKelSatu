<!-- Sidebar -->
<aside class="fixed left-0 top-0 z-40 h-screen w-64 bg-gray-800 text-white shadow-xl transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0" id="sidebar">
    <div class="flex h-full flex-col">
        <!-- Logo -->
        <div class="flex h-16 items-center justify-between border-b border-gray-700 px-6">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 group transition-transform duration-200 hover:scale-105">
                @if(file_exists(public_path('images/logo.png')))
                    <img src="{{ asset('images/logo.png') }}" alt="MENTORA" class="h-10 w-auto transition-all duration-200 group-hover:opacity-80" style="background: transparent; mix-blend-mode: normal;">
                @elseif(file_exists(public_path('images/logo.svg')))
                    <img src="{{ asset('images/logo.svg') }}" alt="MENTORA" class="h-10 w-auto transition-all duration-200 group-hover:opacity-80" style="background: transparent;">
                @else
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-blue-600 transition-all duration-200 group-hover:bg-blue-500 group-hover:shadow-lg">
                        <svg class="h-5 w-5 transition-transform duration-200 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                @endif
            </a>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto px-4 py-6">
            <ul class="space-y-2">
                <!-- Dashboard -->
                <li>
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-lg px-4 py-3 transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-300 hover:bg-gray-700 hover:text-white hover:translate-x-1 hover:shadow-md' }}">
                        <svg class="h-5 w-5 transition-transform duration-200 {{ request()->routeIs('dashboard') ? 'scale-110' : 'group-hover:scale-110' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        <span class="font-medium">{{ request()->routeIs('dashboard') ? 'Dashboard' : 'Dashboard' }}</span>
                    </a>
                </li>

                <!-- Jobs -->
                <li>
                    <a href="{{ route('jobs.index') }}" class="group flex items-center gap-3 rounded-lg px-4 py-3 transition-all duration-200 {{ request()->routeIs('jobs.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-300 hover:bg-gray-700 hover:text-white hover:translate-x-1 hover:shadow-md' }}">
                        <svg class="h-5 w-5 transition-transform duration-200 {{ request()->routeIs('jobs.*') ? 'scale-110' : 'group-hover:scale-110' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <span class="font-medium">Jobs</span>
                    </a>
                </li>

                @if (auth()->user()->role !== 'admin')
                    <!-- Topup -->
                    <li>
                        <a href="{{ route('topups.index') }}" class="group flex items-center gap-3 rounded-lg px-4 py-3 transition-all duration-200 {{ request()->routeIs('topups.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-300 hover:bg-gray-700 hover:text-white hover:translate-x-1 hover:shadow-md' }}">
                            <svg class="h-5 w-5 transition-transform duration-200 {{ request()->routeIs('topups.*') ? 'scale-110' : 'group-hover:scale-110' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <span class="font-medium">Topup</span>
                        </a>
                    </li>

                    @if (auth()->user()->role === 'dosen')
                        <!-- Redeem Codes (Dosen) -->
                        <li>
                            <a href="{{ route('redeem-codes.index') }}" class="group flex items-center gap-3 rounded-lg px-4 py-3 transition-all duration-200 {{ request()->routeIs('redeem-codes.*') && !request()->routeIs('redeem-codes.claim*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-300 hover:bg-gray-700 hover:text-white hover:translate-x-1 hover:shadow-md' }}">
                                <svg class="h-5 w-5 transition-transform duration-200 {{ request()->routeIs('redeem-codes.*') && !request()->routeIs('redeem-codes.claim*') ? 'scale-110' : 'group-hover:scale-110' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v12m0 0l-4-4m4 4l4-4M3 12h18"></path>
                                </svg>
                                <span class="font-medium">Redeem Code</span>
                            </a>
                        </li>
                    @elseif (auth()->user()->role === 'mahasiswa')
                        <!-- Claim Redeem Code (Mahasiswa) -->
                        <li>
                            <a href="{{ route('redeem-codes.claim') }}" class="group flex items-center gap-3 rounded-lg px-4 py-3 transition-all duration-200 {{ request()->routeIs('redeem-codes.claim*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-300 hover:bg-gray-700 hover:text-white hover:translate-x-1 hover:shadow-md' }}">
                                <svg class="h-5 w-5 transition-transform duration-200 {{ request()->routeIs('redeem-codes.claim*') ? 'scale-110' : 'group-hover:scale-110' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <span class="font-medium">Klaim Redeem Code</span>
                            </a>
                        </li>
                    @endif
                @else
                    <!-- Admin Topups -->
                    <li>
                        <a href="{{ route('admin.topups.index') }}" class="group flex items-center gap-3 rounded-lg px-4 py-3 transition-all duration-200 {{ request()->routeIs('admin.topups.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-300 hover:bg-gray-700 hover:text-white hover:translate-x-1 hover:shadow-md' }}">
                            <svg class="h-5 w-5 transition-transform duration-200 {{ request()->routeIs('admin.topups.*') ? 'scale-110' : 'group-hover:scale-110' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                            </svg>
                            <span class="font-medium">Kelola Topup</span>
                        </a>
                    </li>

                    <!-- Admin Reports -->
                    <li>
                        <a href="{{ route('admin.reports.index') }}" class="group flex items-center gap-3 rounded-lg px-4 py-3 transition-all duration-200 {{ request()->routeIs('admin.reports.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-300 hover:bg-gray-700 hover:text-white hover:translate-x-1 hover:shadow-md' }}">
                            <svg class="h-5 w-5 transition-transform duration-200 {{ request()->routeIs('admin.reports.*') ? 'scale-110' : 'group-hover:scale-110' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <span class="font-medium">Reports</span>
                        </a>
                    </li>

                    <!-- Admin Users -->
                    <li>
                        <a href="{{ route('admin.users.index') }}" class="group flex items-center gap-3 rounded-lg px-4 py-3 transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-300 hover:bg-gray-700 hover:text-white hover:translate-x-1 hover:shadow-md' }}">
                            <svg class="h-5 w-5 transition-transform duration-200 {{ request()->routeIs('admin.users.*') ? 'scale-110' : 'group-hover:scale-110' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                            </svg>
                            <span class="font-medium">Users</span>
                        </a>
                    </li>

                    <!-- Admin Categories -->
                    <li>
                        <a href="{{ route('admin.categories.index') }}" class="group flex items-center gap-3 rounded-lg px-4 py-3 transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-300 hover:bg-gray-700 hover:text-white hover:translate-x-1 hover:shadow-md' }}">
                            <svg class="h-5 w-5 transition-transform duration-200 {{ request()->routeIs('admin.categories.*') ? 'scale-110' : 'group-hover:scale-110' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            <span class="font-medium">Kategori</span>
                        </a>
                    </li>

                    <!-- Admin Settings -->
                    <li>
                        <a href="{{ route('admin.settings.index') }}" class="group flex items-center gap-3 rounded-lg px-4 py-3 transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-300 hover:bg-gray-700 hover:text-white hover:translate-x-1 hover:shadow-md' }}">
                            <svg class="h-5 w-5 transition-transform duration-200 {{ request()->routeIs('admin.settings.*') ? 'scale-110 group-hover:rotate-90' : 'group-hover:scale-110 group-hover:rotate-90' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="font-medium">Pengaturan Rekening</span>
                        </a>
                    </li>
                @endif

                <!-- Profile -->
                <li>
                    <a href="{{ route('profile.show') }}" class="group flex items-center gap-3 rounded-lg px-4 py-3 transition-all duration-200 {{ request()->routeIs('profile.*') ? 'bg-blue-600 text-white shadow-lg shadow-blue-500/20' : 'text-gray-300 hover:bg-gray-700 hover:text-white hover:translate-x-1 hover:shadow-md' }}">
                        <svg class="h-5 w-5 transition-transform duration-200 {{ request()->routeIs('profile.*') ? 'scale-110' : 'group-hover:scale-110' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        <span class="font-medium">Profile</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Logout Button -->
        <div class="border-t border-gray-700 p-4">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="group flex w-full items-center gap-3 rounded-lg px-4 py-3 text-gray-300 transition-all duration-200 hover:bg-red-600 hover:text-white hover:shadow-lg hover:shadow-red-500/20 hover:translate-x-1">
                    <svg class="h-5 w-5 transition-transform duration-200 group-hover:scale-110 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span class="font-medium">Logout</span>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- Sidebar Overlay (Mobile) -->
<div class="fixed inset-0 z-30 bg-black bg-opacity-50 backdrop-blur-sm transition-opacity duration-300 hidden opacity-0" id="sidebar-overlay"></div>

