@extends('layouts.app')

@section('title', 'Tin tức – Petio')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/blog.css') }}" />
@endpush

@section('content')
    <div class="blog-container">
      <div class="main-content">
        <div class="body">
          <!-- content -->
          <div class="blog-content">
            @forelse ($posts->chunk(2) as $row)
              <div class="blog-item">
                @foreach ($row as $post)
                  <div class="item-{{ $loop->iteration }} reveal">
                    <a href="{{ route('blog.show', $post) }}" class="blog-img">
                      <img src="{{ asset($post->thumbnail_url) }}" alt="{{ $post->title }}" />
                    </a>
                    <br />
                    <a href="{{ route('blog.show', $post) }}" class="desc">{{ $post->title }}</a>
                  </div>
                @endforeach
              </div>
            @empty
              <p>Chưa có bài viết nào trong mục này.</p>
            @endforelse
          </div>

          @include('blog._sidebar')
        </div>
      </div>
    </div>

    <!-- phân trang -->
    @if ($posts->hasPages())
      <div class="pagination">
        <a href="{{ $posts->previousPageUrl() ?? '#' }}" class="page-link control-btn" aria-label="Trang trước">
          <span class="fas fa-chevron-left"></span>
        </a>
        @foreach ($posts->getUrlRange(1, $posts->lastPage()) as $page => $url)
          <a href="{{ $url }}" class="page-link {{ $page === $posts->currentPage() ? 'active' : '' }}">{{ $page }}</a>
        @endforeach
        <a href="{{ $posts->nextPageUrl() ?? '#' }}" class="page-link control-btn" aria-label="Trang sau">
          <span class="fas fa-chevron-right"></span>
        </a>
      </div>
    @endif

    <!-- uu dai -->
    <div class="bennefit">
      <div class="body">
        @include('partials.newsletter')
      </div>
    </div>
@endsection
