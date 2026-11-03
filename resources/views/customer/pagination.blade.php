@if($paginator->hasPages())
<nav class="pc-pagination" aria-label="Phân trang">
  @if($paginator->previousPageUrl())<a class="pc-btn pc-btn-outline" href="{{ $paginator->previousPageUrl() }}" rel="prev">Trang trước</a>@endif
  <span>Trang {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
  @if($paginator->nextPageUrl())<a class="pc-btn pc-btn-outline" href="{{ $paginator->nextPageUrl() }}" rel="next">Trang sau</a>@endif
</nav>
@endif
