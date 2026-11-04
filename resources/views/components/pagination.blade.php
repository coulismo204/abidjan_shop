@if ($paginator->hasPages())
    <nav class="flex items-center justify-center gap-2" aria-label="{{ __('Pagination') }}">
        @if ($paginator->onFirstPage())
            <span class="btn-outline pointer-events-none px-4 opacity-30" aria-hidden="true"><x-icon name="back" class="size-4" /></span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="btn-outline px-4" aria-label="{{ __('Page précédente') }}"><x-icon name="back" class="size-4" /></a>
        @endif
        <span class="px-3 text-sm font-semibold">{{ __('Page :c sur :t', ['c' => $paginator->currentPage(), 't' => $paginator->lastPage()]) }}</span>
        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="btn-outline px-4" aria-label="{{ __('Page suivante') }}"><x-icon name="arrow" class="size-4" /></a>
        @else
            <span class="btn-outline pointer-events-none px-4 opacity-30" aria-hidden="true"><x-icon name="arrow" class="size-4" /></span>
        @endif
    </nav>
@endif
