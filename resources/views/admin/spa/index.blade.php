@extends('admin.layout')

@section('title', 'Dịch vụ spa')

@section('actions')
  <a class="btn btn-primary" href="{{ route('admin.spa.create') }}">+ Thêm dịch vụ</a>
@endsection

@section('content')
  <section class="panel panel-flush">
    @if ($services->isEmpty())
      <p class="empty">Chưa có dịch vụ.</p>
    @else
      <table class="table">
        <tr><th class="num">#</th><th>Dịch vụ</th><th>Giá</th><th class="num">Thời gian</th><th>Trạng thái</th><th></th></tr>
        @foreach ($services as $s)
          <tr>
            <td class="num">{{ $s->sort_order }}</td>
            <td><a href="{{ route('admin.spa.edit', $s) }}"><strong>{{ $s->name }}</strong></a><br /><span class="muted">{{ $s->description }}</span></td>
            <td>
              @if ($s->pricing_type === 'range')
                {{ vnd($s->price_from) }}{{ $s->price_to ? ' – '.vnd($s->price_to) : '' }}
              @else
                @foreach ($s->prices as $p)
                  <span class="muted">{{ $p->label() }}:</span> {{ vnd($p->price) }}<br />
                @endforeach
              @endif
            </td>
            <td class="num">{{ $s->duration_min ? $s->duration_min.' phút' : '—' }}</td>
            <td><span class="badge badge-{{ $s->is_active ? 'active' : 'locked' }}">{{ $s->is_active ? 'Hiện' : 'Ẩn' }}</span></td>
            <td class="actions">
              <a class="btn btn-sm btn-light" href="{{ route('admin.spa.edit', $s) }}">Sửa</a>
              @include('admin._delete', ['action' => route('admin.spa.destroy', $s)])
            </td>
          </tr>
        @endforeach
      </table>
    @endif
  </section>
@endsection
