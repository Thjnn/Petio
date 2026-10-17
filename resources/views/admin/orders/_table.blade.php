@if ($orders->isEmpty())
  <p class="empty">Chưa có đơn hàng nào.</p>
@else
  <table class="table">
    <tr>
      <th>Mã đơn</th><th>Khách</th><th>Ngày đặt</th><th class="num">Tổng tiền</th><th>Thanh toán</th><th>Trạng thái</th>
    </tr>
    @foreach ($orders as $order)
      <tr>
        <td><a href="{{ route('admin.orders.show', $order) }}"><strong>{{ $order->order_code }}</strong></a></td>
        <td>{{ $order->receiver_name }}<br /><span class="muted">{{ $order->receiver_phone }}</span></td>
        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
        <td class="num">{{ vnd($order->total_amount) }}</td>
        <td><span class="badge badge-{{ $order->payment_status }}">{{ \App\Models\Order::PAYMENT_STATUSES[$order->payment_status] }}</span></td>
        <td><span class="badge badge-{{ $order->status }}">{{ \App\Models\Order::STATUSES[$order->status] }}</span></td>
      </tr>
    @endforeach
  </table>
@endif
