<div class="mx-auto max-w-5xl px-4 py-6 text-sm text-gray-400 sm:px-6 lg:px-8">
    <div class="flex flex-col items-center gap-3">
        @if(file_exists(public_path('images/logo.png')))
            <img src="{{ asset('images/logo.png') }}" alt="MENTORA" class="h-6 w-auto opacity-60" style="background: transparent; mix-blend-mode: normal;">
        @elseif(file_exists(public_path('images/logo.svg')))
            <img src="{{ asset('images/logo.svg') }}" alt="MENTORA" class="h-6 w-auto opacity-60" style="background: transparent;">
        @endif
        <p class="text-center">&copy; {{ now()->year }} MENTORA. Semua hak dilindungi.</p>
    </div>
</div>

