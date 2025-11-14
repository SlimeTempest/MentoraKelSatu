<header class="bg-white shadow">
    <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="/" class="text-xl font-semibold text-indigo-600">MentoraKelSatu</a>
        <nav class="flex items-center gap-4 text-sm font-medium text-gray-700">
            @auth
                @if (auth()->user()->role !== 'admin')
                    <div class="flex items-center gap-2 rounded bg-gray-100 px-3 py-1.5">
                        <span class="text-xs text-gray-600">Saldo:</span>
                        <span class="font-semibold text-gray-900">Rp {{ number_format(auth()->user()->balance, 0, ',', '.') }}</span>
                    </div>
                @endif
                <a href="{{ route('dashboard') }}" class="hover:text-indigo-600">Dashboard</a>
                <a href="{{ route('jobs.index') }}" class="hover:text-indigo-600">Job</a>
                @if (auth()->user()->role !== 'admin')
                    <a href="{{ route('topups.index') }}" class="hover:text-indigo-600">Topup</a>
                @else
                    <a href="{{ route('admin.topups.index') }}" class="hover:text-indigo-600">Kelola Topup</a>
                    <a href="{{ route('admin.settings.index') }}" class="hover:text-indigo-600">Pengaturan</a>
                @endif
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="rounded bg-indigo-600 px-3 py-1.5 text-white hover:bg-indigo-500">
                        Keluar
                    </button>
                </form>
            @endauth

            @guest
                <a href="{{ route('login') }}" class="hover:text-indigo-600">Masuk</a>
                <a href="{{ route('register') }}" class="rounded bg-indigo-600 px-3 py-1.5 text-white hover:bg-indigo-500">
                    Daftar
                </a>
            @endguest
        </nav>
    </div>
</header>

