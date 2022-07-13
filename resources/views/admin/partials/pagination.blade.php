@if ($paginator->hasPages())
<!-- Pagination -->
<div class="pull-right pagination">
    <ul class="pagination">
        @if (!$paginator->onFirstPage())
            <li>
                <a href="{{ $paginator->previousPageUrl() }}" >
                    <span class="btn btn-outline-secondary" style="margin-right: 5px;"> Previous </span>
                </a>
            </li>
        @endif

        @foreach ($elements as $element)
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li><span class="btn btn-outline-secondary active" style="margin-right: 5px;">{{ $page }}</span></li>
                    @elseif (($page == $paginator->currentPage() + 1 || $page == $paginator->currentPage() + 2) || $page == $paginator->lastPage())
                        <li><a href="{{ $url }}" class="btn btn-outline-secondary" style="margin-right: 5px;">{{ $page }}</a></li>
                    @elseif ($page == $paginator->lastPage() - 1)
                        <li class="disabled"><span class="btn btn-outline-secondary" style="margin-right: 5px;" > ... </span></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        @if ($paginator->hasMorePages())
            <li>
                <a href="{{ $paginator->nextPageUrl() }}" class="btn btn-outline-secondary">
                    <span> Next </span>
                </a>
            </li>
        @endif
    </ul>
</div>
<!-- Pagination -->
@endif

<!-- <div class="template-demo">
    <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" class="btn btn-outline-secondary">1</button>
        <button type="button" class="btn btn-outline-secondary">2</button>
        <button type="button" class="btn btn-outline-secondary">3</button>
    </div>
    <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" class="btn btn-outline-secondary">
            <i class="mdi mdi-heart-outline"></i>
        </button>
        <button type="button" class="btn btn-outline-secondary">
            <i class="mdi mdi-calendar"></i>
        </button>
        <button type="button" class="btn btn-outline-secondary">
            <i class="mdi mdi-clock"></i>
        </button>
    </div>
</div> -->