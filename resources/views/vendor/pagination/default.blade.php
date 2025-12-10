@if ($paginator->hasPages())
    <nav class="flex items-center justify-center gap-1" aria-label="Pagination Navigation">
        <ul class="flex items-center gap-1">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li>
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-600 bg-gray-700 text-gray-500 cursor-not-allowed" aria-disabled="true" aria-label="@lang('pagination.previous')">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </span>
                </li>
            @else
                <li>
                    <a href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')" class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-600 bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white hover:border-indigo-500 transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li>
                        <span class="flex h-9 w-9 items-center justify-center text-gray-500">{{ $element }}</span>
                    </li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li>
                                <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-indigo-500 bg-indigo-600 text-white font-semibold" aria-current="page">{{ $page }}</span>
                            </li>
                        @else
                            <li>
                                <a href="{{ $url }}" class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-600 bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white hover:border-indigo-500 transition-all duration-200">{{ $page }}</a>
                            </li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li>
                    <a href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')" class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-600 bg-gray-700 text-gray-300 hover:bg-gray-600 hover:text-white hover:border-indigo-500 transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </li>
            @else
                <li>
                    <span class="flex h-9 w-9 items-center justify-center rounded-lg border border-gray-600 bg-gray-700 text-gray-500 cursor-not-allowed" aria-disabled="true" aria-label="@lang('pagination.next')">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </span>
                </li>
            @endif
        </ul>
    </nav>
@endif
