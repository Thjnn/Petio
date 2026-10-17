@extends('admin.layout')

@section('title', 'Bài viết')

@section('actions')
  <a class="btn btn-primary" href="{{ route('admin.posts.create') }}">+ Viết bài</a>
@endsection

@section('content')
  <form class="filters" method="get">
    <input type="search" name="q" value="{{ request('q') }}" placeholder="Tiêu đề..." aria-label="Tìm bài viết" />
    <button class="btn" type="submit">Tìm</button>
  </form>

  <section class="panel panel-flush">
    @if ($posts->isEmpty())
      <p class="empty">Chưa có bài viết.</p>
    @else
      <table class="table">
        <tr><th></th><th>Tiêu đề</th><th>Chuyên mục</th><th>Ngày đăng</th><th class="num">Lượt xem</th><th>Trạng thái</th><th></th></tr>
        @foreach ($posts as $post)
          <tr>
            <td><img class="thumb" src="{{ asset($post->thumbnail_url) }}" alt="" loading="lazy" /></td>
            <td><a href="{{ route('admin.posts.edit', $post) }}"><strong>{{ $post->title }}</strong></a></td>
            <td>{{ $post->category?->name ?? '—' }}</td>
            <td>{{ $post->published_at?->format('d/m/Y') ?? '—' }}</td>
            <td class="num">{{ $post->view_count }}</td>
            <td><span class="badge badge-{{ $post->status }}">{{ \App\Http\Controllers\Admin\BlogPostController::STATUSES[$post->status] }}</span></td>
            <td class="actions">
              <a class="btn btn-sm btn-light" href="{{ route('admin.posts.edit', $post) }}">Sửa</a>
              @include('admin._delete', ['action' => route('admin.posts.destroy', $post)])
            </td>
          </tr>
        @endforeach
      </table>
    @endif
  </section>
  {{ $posts->links('pagination::bootstrap-4') }}
@endsection
