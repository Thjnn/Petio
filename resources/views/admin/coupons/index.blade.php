@extends('admin.layout')

@section('title', 'Mã giảm giá')

@section('actions')
  <a class="btn btn-primary" href="{{ route('admin.coupons.create') }}">+ Thêm mã</a>
@endsection

@section('content')
  <section class="panel panel-flush">
    @if ($coupons->isEmpty())
      <p class="empty">Chưa có mã giảm giá.</p>
    @else
      <table class="table">
        <tr><th>Mã</th><th>Giảm</th><th class="num">Đơn tối thiểu</th><th class="num">Đã dùng</th><th>Hiệu lực</th><th>Trạng thái</th><th></th></tr>
        @foreach ($coupons as $c)
          <tr>
            <td><strong>{{ $c->code }}</strong><br /><span class="muted">{{ $c->description }}</span></td>
            <td>
              {{ $c->discount_type === 'percent' ? $c->discount_value.'%' : vnd($c->discount_value) }}
              @if ($c->max_discount)
                <span class="muted">(tối đa {{ vnd($c->max_discount) }})</span>
              @endif
              @if ($c->first_order_only)
                <br /><span class="muted">Chỉ đơn đầu</span>
              @endif
            </td>
            <td class="num">{{ vnd($c->min_order_value) }}</td>
            <td class="num">{{ $c->used_count }}{{ $c->usage_limit ? ' / '.$c->usage_limit : '' }}</td>
            <td>{{ $c->starts_at?->format('d/m/Y') ?? '…' }} – {{ $c->expires_at?->format('d/m/Y') ?? '…' }}</td>
            <td><span class="badge badge-{{ $c->is_active ? 'active' : 'locked' }}">{{ $c->is_active ? 'Bật' : 'Tắt' }}</span></td>
            <td class="actions">
              <a class="btn btn-sm btn-light" href="{{ route('admin.coupons.edit', $c) }}">Sửa</a>
              @include('admin._delete', ['action' => route('admin.coupons.destroy', $c)])
            </td>
          </tr>
        @endforeach
      </table>
    @endif
  </section>
@endsection
