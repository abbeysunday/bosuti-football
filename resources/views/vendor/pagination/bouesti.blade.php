{{--
    Public-site paginator. Usage: {{ $posts->links('vendor.pagination.bouesti') }}
    Phones show Previous / current page / Next; wider screens show the page numbers.
--}}
@if ($paginator->hasPages())
    <nav class="pagination" role="navigation" aria-label="Pagination">
        @if ($paginator->onFirstPage())
            <span class="page-btn is-disabled" aria-disabled="true"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i><span class="page-label">Previous</span></span>
        @else
            <a class="page-btn" href="{{ $paginator->previousPageUrl() }}" rel="prev"><i class="fa-solid fa-arrow-left" aria-hidden="true"></i><span class="page-label">Previous</span></a>
        @endif

        <span class="page-status">Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}</span>

        <ul class="page-numbers">
            @foreach ($elements as $element)
                @if (is_string($element))
                    <li><span class="page-gap" aria-hidden="true">…</span></li>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <li>
                            @if ($page == $paginator->currentPage())
                                <span class="page-num is-current" aria-current="page">{{ $page }}</span>
                            @else
                                <a class="page-num" href="{{ $url }}" aria-label="Go to page {{ $page }}">{{ $page }}</a>
                            @endif
                        </li>
                    @endforeach
                @endif
            @endforeach
        </ul>

        @if ($paginator->hasMorePages())
            <a class="page-btn" href="{{ $paginator->nextPageUrl() }}" rel="next"><span class="page-label">Next</span><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></a>
        @else
            <span class="page-btn is-disabled" aria-disabled="true"><span class="page-label">Next</span><i class="fa-solid fa-arrow-right" aria-hidden="true"></i></span>
        @endif
    </nav>
@endif
