<!-- Topbar -->
<header class="fixed top-0 left-0 right-0 lg:left-64 z-50 flex h-16 items-center justify-between border-b border-gray-700 bg-gray-800 pl-2 pr-1 sm:pl-4 sm:pr-2 transition-all duration-300" style="min-width: 0; overflow: visible !important; background-color: rgb(31 41 55) !important;">
    <div class="flex items-center gap-2 flex-shrink-0">
        <!-- Mobile Menu Button -->
        <button id="sidebar-toggle" class="lg:hidden rounded-lg p-2 text-gray-300 transition-all duration-200 hover:bg-gray-700 hover:text-white hover:scale-110 active:scale-95 flex-shrink-0">
            <svg class="h-6 w-6 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
    </div>

    <div class="flex items-center gap-2 sm:gap-3 flex-shrink-0 pr-2 sm:pr-4 lg:pr-6" style="min-width: 0; overflow: visible !important;">
        <!-- Balance (Non-Admin) -->
        @if (auth()->check() && auth()->user()->role !== 'admin')
            <div class="flex items-center gap-2 rounded-lg bg-gradient-to-r from-green-600/20 to-emerald-600/20 border border-green-500/30 px-3 py-1.5 sm:px-4 sm:py-2 transition-all duration-200 hover:from-green-600/30 hover:to-emerald-600/30 hover:border-green-500/50 hover:shadow-lg hover:scale-105 flex-shrink-0">
                <div class="flex flex-col">
                    <span class="text-xs text-gray-400 leading-tight">Saldo</span>
                    <span class="text-xs sm:text-sm font-bold text-green-400 whitespace-nowrap">Rp {{ number_format(auth()->user()->balance ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        @endif

        <!-- User Profile Dropdown -->
        @auth
        @php
            // Pastikan menggunakan data terbaru dari database
            $currentUser = auth()->user()->fresh();
        @endphp
        <div class="relative flex-shrink-0" x-data="{ open: false }" style="overflow: visible !important;">
            <button @click="open = !open" class="flex items-center gap-2 sm:gap-3 rounded-lg bg-gray-700/50 hover:bg-gray-700 px-2 py-1.5 sm:px-3 sm:py-2 text-gray-300 transition-all duration-200 hover:text-white flex-shrink-0 border border-gray-600/50 hover:border-gray-500" style="min-width: 0; overflow: visible !important;">
                <div class="h-8 w-8 sm:h-10 sm:w-10 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center transition-all duration-200 hover:from-blue-400 hover:to-blue-500 flex-shrink-0 shadow-md overflow-hidden" style="flex-shrink: 0 !important;">
                    @if ($currentUser->photo)
                        <img src="{{ $currentUser->photo_url }}?v={{ time() }}" alt="{{ $currentUser->name }}" class="h-full w-full object-cover" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <span class="hidden text-sm sm:text-base font-bold text-white">{{ strtoupper(substr($currentUser->name ?? 'U', 0, 1)) }}</span>
                    @else
                        <span class="text-sm sm:text-base font-bold text-white">{{ strtoupper(substr($currentUser->name ?? 'U', 0, 1)) }}</span>
                    @endif
                </div>
                <!-- Tampilkan nama dan role untuk semua role (mengikuti referensi dosen) -->
                <div class="block text-left flex-shrink-0 min-w-0" style="max-width: 150px; flex-shrink: 0;">
                    <p class="text-xs sm:text-sm font-semibold text-white truncate leading-tight">{{ $currentUser->name ?? 'User' }}</p>
                    <p class="text-xs text-gray-400 capitalize truncate leading-tight">{{ $currentUser->role ?? 'user' }}</p>
                </div>
                <svg class="h-3.5 w-3.5 sm:h-4 sm:w-4 text-gray-400 flex-shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

