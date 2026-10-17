@extends('admin.layout')

@section('title', 'Banner')

@section('actions')
  <a class="btn btn-primary" href="{{ route('admin.banners.create') }}">+ Thêm banner</a>
@endsection

@section('content')
  @php($positions = \App\Http\Controllers\Admin\BannerController::POSITIONS)
  <section class="panel panel-flush">
    @if ($banners->isEmpty())
      <p class="empty">Chưa có banner.</p>
    @else
      <table class="table">
        <tr><th></th><th>Tiêu đề</th><th>Vị trí</th><th class="num">Thứ tự</th><th>Thời gian</th><th>Trạng thái</th><th></th></tr>
        @foreach ($banners as $b)
          <tr>
            <td><img class="thumb" src="{{ asset($b->image_url) }}" alt="" loading="lazy" style="width: 96px" /></td>
            <td><a href="{{ route('admin.banners.edit', $b) }}"><strong>{{ $b->title ?: '(không tiêu đề)' }}</strong></a><br /><span class="muted">{{ $b->subtitle }}</span></td>
            <td>{{ $positions[$b->position] }}</td>
            <td class="num">{{ $b->sort_order }}</td>
            <td>{{ $b->starts_at?->format('d/m/Y') ?? '…' }} – {{ $b->ends_at?->format('d/m/Y') ?? '…' }}</td>
            <td><span class="badge badge-{{ $b->is_active ? 'active' : 'locked' }}">{{ $b->is_active ? 'Bật' : 'Tắt' }}</span></td>
            <td class="actions">
              <a class="btn btn-sm btn-light" href="{{ route('admin.banners.edit', $b) }}">Sửa</a>
              @include('admin._delete', ['action' => route('admin.banners.destroy', $b)])
            </td>
          </tr>
        @endforeach
      </table>
    @endif
  </section>
@endsection
