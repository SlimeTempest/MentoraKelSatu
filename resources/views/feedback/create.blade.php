@extends('layouts.app', ['title' => 'Beri Rating'])

@section('content')
    <div class="mx-auto max-w-2xl rounded-lg border border-gray-700 bg-gray-800 p-6 sm:p-8 shadow-lg">
        <h1 class="mb-6 text-2xl font-bold text-gray-100">Beri Rating untuk Job</h1>

        <div class="mb-6 rounded-lg border border-gray-600 bg-gray-700/50 p-4">
            <h3 class="font-semibold text-gray-100">{{ $job->title }}</h3>
            @if ($job->assignee)
                <p class="mt-1 text-sm text-gray-300">Worker: <strong class="text-gray-100">{{ $job->assignee->name }}</strong></p>
            @endif
            <p class="mt-1 text-sm text-gray-300">Harga: <strong class="text-green-400">Rp {{ number_format($job->price, 0, ',', '.') }}</strong></p>
        </div>

        <form action="{{ route('jobs.feedback.store', $job) }}" method="POST">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-300">Rating</label>
                    <div class="flex gap-2" id="rating-container">
                        @for ($i = 1; $i <= 5; $i++)
                            <button
                                type="button"
                                onclick="setRating({{ $i }})"
                                class="rating-star h-10 w-10 rounded-lg border border-gray-600 bg-gray-700 hover:bg-yellow-500/20 hover:border-yellow-500 flex items-center justify-center transition-colors"
                                data-rating="{{ $i }}"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-gray-400">
                                    <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                </svg>
                            </button>
                        @endfor
                    </div>
                    <input type="hidden" name="rating" id="rating-input" value="{{ old('rating', 0) }}" required>
                    @error('rating')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                    <p class="mt-2 text-xs text-gray-400" id="rating-text">Pilih rating (1-5 bintang)</p>
                </div>

                <div>
                    <label for="comment" class="mb-2 block text-sm font-medium text-gray-300">Komentar (Opsional)</label>
                    <textarea
                        id="comment"
                        name="comment"
                        rows="4"
                        maxlength="1000"
                        class="w-full rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm text-gray-100 placeholder-gray-400 focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 transition-colors resize-none"
                        placeholder="Bagikan pengalaman Anda tentang pekerjaan ini..."
                    >{{ old('comment') }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">Maksimal 1000 karakter</p>
                    @error('comment')
                        <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('jobs.index') }}" class="rounded-lg border border-gray-600 bg-gray-700 px-4 py-2.5 text-sm font-semibold text-gray-300 hover:bg-gray-600 hover:text-gray-100 transition-all duration-200">
                        Batal
                    </a>
                    <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-gray-50 hover:bg-indigo-500 transition-all duration-200 hover:shadow-lg hover:scale-105">
                        Kirim Rating
                    </button>
                </div>
            </div>
        </form>
    </div>

    <script>
        let selectedRating = 0;

        function setRating(rating) {
            selectedRating = rating;
            document.getElementById('rating-input').value = rating;
            
            const stars = document.querySelectorAll('.rating-star');
            stars.forEach((star, index) => {
                const svg = star.querySelector('svg');
                if (index < rating) {
                    star.classList.add('bg-yellow-500/20', 'border-yellow-500');
                    star.classList.remove('border-gray-600', 'bg-gray-700');
                    if (svg) {
                        svg.setAttribute('fill', 'currentColor');
                        svg.classList.remove('text-gray-400');
                        svg.classList.add('text-yellow-400');
                    }
                } else {
                    star.classList.remove('bg-yellow-500/20', 'border-yellow-500');
                    star.classList.add('border-gray-600', 'bg-gray-700');
                    if (svg) {
                        svg.setAttribute('fill', 'none');
                        svg.classList.remove('text-yellow-400');
                        svg.classList.add('text-gray-400');
                    }
                }
            });

            const ratingTexts = ['', 'Sangat Buruk', 'Buruk', 'Cukup', 'Baik', 'Sangat Baik'];
            document.getElementById('rating-text').textContent = ratingTexts[rating] || 'Pilih rating (1-5 bintang)';
        }

        // Set initial rating from old input
        @if (old('rating'))
            setRating({{ old('rating') }});
        @endif
    </script>
@endsection

