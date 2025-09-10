@if ($paginator->hasPages())
    <nav>
        <ul class="pagination pagination-sm pagination-rounded pagination-boxed mb-0 pagination-mobile">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&laquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev" aria-label="@lang('pagination.previous')">&laquo;</a>
                </li>
            @endif

            {{-- Mobile-optimized Pagination Elements --}}
            @php
                // For mobile, show fewer page links based on screen size
                $isMobile = isset($_SERVER['HTTP_USER_AGENT']) && (
                    strpos($_SERVER['HTTP_USER_AGENT'], 'Mobile') !== false || 
                    strpos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false
                );
                
                // Number of pages to show on each side of current page
                $window = $isMobile ? 0 : 1; 
            @endphp

            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled d-none d-sm-block" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        {{-- Show current page always, nearby pages based on window size, and first/last pages --}}
                        @php
                            $isCurrentPage = $page == $paginator->currentPage();
                            $isFirstOrLastPage = $page == 1 || $page == $paginator->lastPage();
                            $isWithinWindow = abs($paginator->currentPage() - $page) <= $window;
                            
                            // Show on mobile only if it's the current page, within window, or first/last page
                            $showOnMobile = $isCurrentPage || $isFirstOrLastPage || $isWithinWindow;
                        @endphp
                        
                        @if ($isCurrentPage)
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @elseif ($showOnMobile)
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @else
                            <li class="page-item d-none d-sm-block"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next" aria-label="@lang('pagination.next')">&raquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&raquo;</span>
                </li>
            @endif
        </ul>
    </nav>
@endif