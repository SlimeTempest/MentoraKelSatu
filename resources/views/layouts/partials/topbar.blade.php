<!-- Topbar -->
<header class="fixed top-0 right-0 z-30 flex h-16 w-full items-center justify-between border-b border-gray-700 bg-gray-800 px-4 transition-all duration-300 lg:left-64">
    <div class="flex items-center gap-4">
        <!-- Mobile Menu Button -->
        <button id="sidebar-toggle" class="lg:hidden rounded-lg p-2 text-gray-300 transition-all duration-200 hover:bg-gray-700 hover:text-white hover:scale-110 active:scale-95">
            <svg class="h-6 w-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <div class="flex items-center gap-4">
        <!-- Balance (Non-Admin) -->
        @if (auth()->check() && auth()->user()->role !== 'admin')
            <div class="hidden sm:flex items-center gap-2 rounded-lg bg-gray-700 px-3 py-1.5 transition-all duration-200 hover:bg-gray-600 hover:shadow-lg hover:scale-105">
                <svg class="h-4 w-4 text-green-400 transition-transform duration-200 hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                </svg>
                <span class="text-sm font-semibold text-white">Rp {{ number_format(auth()->user()->balance ?? 0, 0, ',', '.') }}</span>
            </div>
        @endif

        <!-- User Profile Dropdown -->
        @auth
        <div class="relative" x-data="{ open: false }">
            <button @click="open = !open" class="flex items-center gap-3 rounded-lg px-3 py-2 text-gray-300 transition-all duration-200 hover:bg-gray-700 hover:text-white hover:scale-105 active:scale-95">
                <div class="h-8 w-8 rounded-full bg-blue-600 flex items-center justify-center transition-all duration-200 hover:bg-blue-500 hover:shadow-lg hover:shadow-blue-500/50">
                    <span class="text-sm font-semibold text-white">{{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 1)) }}</span>
                </div>
                <div class="hidden md:block text-left">
                    <p class="text-sm font-medium text-white">{{ auth()->user()->name ?? 'User' }}</p>
                    <p class="text-xs text-gray-400 capitalize">{{ auth()->user()->role ?? 'user' }}</p>
                </div>
                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>

            <!-- Dropdown Menu -->
            <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2 w-48 rounded-lg bg-gray-700 shadow-lg ring-1 ring-black ring-opacity-5" style="display: none;">
                <div class="py-1">
                    <a href="{{ route('profile.show') }}" class="group flex items-center gap-3 px-4 py-2 text-sm text-gray-300 transition-all duration-200 hover:bg-gray-600 hover:text-white hover:translate-x-1">
                        <svg class="h-4 w-4 transition-transform duration-200 group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Profile
                    </a>
                    <a href="{{ route('profile.edit') }}" class="group flex items-center gap-3 px-4 py-2 text-sm text-gray-300 transition-all duration-200 hover:bg-gray-600 hover:text-white hover:translate-x-1">
                        <svg class="h-4 w-4 transition-transform duration-200 group-hover:scale-110 group-hover:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Settings
                    </a>
                    <hr class="my-1 border-gray-600">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="group flex w-full items-center gap-3 px-4 py-2 text-sm text-gray-300 transition-all duration-200 hover:bg-red-600 hover:text-white hover:translate-x-1">
                            <svg class="h-4 w-4 transition-transform duration-200 group-hover:scale-110 group-hover:rotate-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                            </svg>
                            Log out
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endauth
    </div>
</header>

