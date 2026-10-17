@extends('admin.layout')

@section('title', 'Tổng quan')

@section('content')
  <div class="stats">
    <div class="stat"><span>Doanh thu tháng này</span><strong>{{ vnd($stats['revenueMonth']) }}</strong></div>
    <div class="stat"><span>Đơn hôm nay</span><strong>{{ $stats['ordersToday'] }}</strong></div>
    <a class="stat" href="{{ route('admin.orders.index', ['status' => 'pending']) }}"><span>Đơn chờ xác nhận</span><strong>{{ $stats['pendingOrders'] }}</strong></a>
    <div class="stat"><span>Khách hàng</span><strong>{{ $stats['customers'] }}</strong></div>
    <a class="stat" href="{{ route('admin.contacts.index', ['status' => 'new']) }}"><span>Liên hệ mới</span><strong>{{ $stats['newContacts'] }}</strong></a>
  </div>

  <div class="grid-2">
    <section class="panel">
      <h2>Doanh thu 7 ngày (không tính đơn hủy/trả)</h2>
      @php($max = max($week->max(), 1))
      <div class="bars">
        @foreach ($week as $day => $amount)
          <div class="bar" title="{{ $day }}: {{ vnd($amount) }}">
            <i style="height: {{ round($amount / $max * 100) }}%"></i>
            <span>{{ $day }}</span>
          </div>
        @endforeach
      </div>
    </section>

    <section class="panel panel-flush">
      <h2 style="padding: 24px 24px 0">Sắp hết hàng (dưới 5)</h2>
      @if ($lowStock->isEmpty())
        <p class="empty">Kho ổn, không có biến thể nào sắp hết.</p>
      @else
        <table class="table">
          <tr><th>Sản phẩm</th><th>Biến thể</th><th class="num">Tồn</th></tr>
          @foreach ($lowStock as $v)
            <tr>
              <td><a href="{{ route('admin.products.edit', $v->product_id) }}">{{ $v->product->name }}</a></td>
              <td>{{ $v->name }}</td>
              <td class="num"><strong>{{ $v->stock_qty }}</strong></td>
            </tr>
          @endforeach
        </table>
      @endif
    </section>
  </div>

  <section class="panel panel-flush">
    <h2 style="padding: 24px 24px 0">Đơn hàng mới nhất</h2>
    @include('admin.orders._table', ['orders' => $recentOrders])
  </section>
@endsection
