{{-- Admin/account pagination. Phones: Previous · page x of y · Next. Larger screens add page numbers. --}}
@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="mt-6 flex items-center justify-between gap-3">
        @php $btn = 'inline-flex min-h-[44px] min-w-[44px] items-center justify-center rounded-full border px-4 text-sm font-semibold transition'; @endphp

        @if ($paginator->onFirstPage())
            <span class="{{ $btn }} border-subtle text-ink-faint" aria-disabled="true">{!! __('pagination.previous') !!}</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $btn }} border-line bg-pitch-850 text-ink hover:border-line-strong hover:text-gold-light">{!! __('pagination.previous') !!}</a>
        @endif

        <p class="text-sm text-ink-muted sm:hidden">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</p>

        <ul class="hidden items-center gap-1.5 sm:flex">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="px-2 text-ink-faint">…</span></li>
                @endif
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span aria-current="page" class="{{ $btn }} border-gold-light bg-gold-light px-0 text-pitch-950">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="{{ $btn }} border-line bg-pitch-850 px-0 text-ink-2 hover:border-line-strong hover:text-white" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ul>

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $btn }} border-line bg-pitch-850 text-ink hover:border-line-strong hover:text-gold-light">{!! __('pagination.next') !!}</a>
        @else
            <span class="{{ $btn }} border-subtle text-ink-faint" aria-disabled="true">{!! __('pagination.next') !!}</span>
        @endif
    </nav>
@endif