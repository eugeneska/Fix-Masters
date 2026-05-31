@if ($paginator->hasPages())
  <nav class="admin-pagination__nav" role="navigation">
    @if ($paginator->onFirstPage())
      <span class="admin-pagination__link is-disabled">←</span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" class="admin-pagination__link">←</a>
    @endif

    <span class="admin-pagination__info">{{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} из {{ $paginator->total() }}</span>

    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" class="admin-pagination__link">→</a>
    @else
      <span class="admin-pagination__link is-disabled">→</span>
    @endif
  </nav>
@endif
