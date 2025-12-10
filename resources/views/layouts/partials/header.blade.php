<div class="flex w-full items-center justify-between py-4 pl-8 sm:pl-12 lg:pl-16">
    <a href="/" class="text-xl font-semibold text-blue-400 hover:text-blue-300 transition-colors">MentoraKelSatu</a>
    <nav class="flex items-center gap-4 pr-8 text-sm font-medium text-gray-300 sm:pr-12 lg:pr-16">
        @auth
            @if (auth()->check() && auth()->user()->role !== 'admin')
                <div class="flex items-center gap-2 rounded-lg bg-gray-700 px-3 py-1.5">
                    <span class="text-xs text-gray-400">Saldo:</span>
                    <span class="font-semibold text-green-400">Rp
                        {{ number_format(auth()->user()->balance ?? 0, 0, ',', '.') }}</span>
                </div>
            @endif
            <a href="{{ route('dashboard') }}" class="hover:text-white transition-colors">Dashboard</a>
            <a href="{{ route('jobs.index') }}" class="hover:text-white transition-colors">Job</a>
            <a href="{{ route('profile.show') }}" class="hover:text-white transition-colors">Profile</a>
            @if (auth()->check() && auth()->user()->role !== 'admin')
                <a href="{{ route('topups.index') }}" class="hover:text-white transition-colors">Topup</a>
            @elseif (auth()->check() && auth()->user()->role === 'admin')
                <a href="{{ route('admin.topups.index') }}" class="hover:text-white transition-colors">Kelola Topup</a>
                <a href="{{ route('admin.settings.index') }}" class="hover:text-white transition-colors">Pengaturan</a>
            @endif
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="rounded-lg bg-blue-600 px-3 py-1.5 text-white hover:bg-blue-500 transition-colors">
                    Keluar
                </button>
            </form>
        @endauth

        @guest
            <a href="{{ route('login') }}" class="hover:text-white transition-colors">Masuk</a>
            <a href="{{ route('register') }}" class="rounded-lg bg-blue-600 px-3 py-1.5 text-white hover:bg-blue-500 transition-colors">
                Daftar
            </a>
        @endguest
    </nav>
</div>
