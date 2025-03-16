@if ($paginator->hasPages())
    <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center gap-2">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="btn btn-sm btn-outline-secondary disabled">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="btn btn-sm btn-outline-secondary">
                    Previous
                </a>
            @endif

            {{-- Page Numbers --}}
            <div class="d-flex align-items-center">
                @foreach ($elements as $element)
                    {{-- Array Of Links --}}
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="btn btn-sm btn-primary mx-1">{{ $page }}</span>
                            @else
                                <a href="{{ $url }}" class="btn btn-sm btn-outline-secondary mx-1">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-sm btn-outline-secondary">
                    Next
                </a>
            @else
                <span class="btn btn-sm btn-outline-secondary disabled">
                    Next
                </span>
            @endif
        </div>
        
        <small class="text-muted ms-2">
            {{ $paginator->firstItem() }}-{{ $paginator->lastItem() }} of {{ $paginator->total() }}
        </small>
    </div>
@endif
