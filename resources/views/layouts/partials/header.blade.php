<header class="bg-white shadow">
    <div class="mx-auto flex max-w-5xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
        <a href="/" class="text-xl font-semibold text-indigo-600">MentoraKelSatu</a>
        <nav class="flex items-center gap-4 text-sm font-medium text-gray-700">
            @auth
                <a href="/dashboard" class="hover:text-indigo-600">Dashboard</a>
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

