@extends('admin.layout')

@section('title', 'Đơn '.$order->order_code)

@section('actions')
  <a class="btn btn-light" href="{{ route('admin.orders.index') }}">← Danh sách đơn</a>
@endsection

@section('content')
  <div class="grid-2">
    <section class="panel">
      <h2>Thông tin nhận hàng</h2>
      <dl class="info">
        <dt>Người nhận</dt><dd>{{ $order->receiver_name }}</dd>
        <dt>Điện thoại</dt><dd><a href="tel:{{ $order->receiver_phone }}">{{ $order->receiver_phone }}</a></dd>
        <dt>Email</dt><dd>{{ $order->receiver_email ?: '—' }}</dd>
        <dt>Địa chỉ</dt><dd>{{ $order->ship_address }}, {{ $order->ship_ward }}, {{ $order->ship_district }}, {{ $order->ship_province }}</dd>
        <dt>Tài khoản</dt><dd>{{ $order->user?->displayName() ?? 'Khách vãng lai' }}</dd>
        <dt>Ghi chú</dt><dd>{{ $order->note ?: '—' }}</dd>
        <dt>Thanh toán</dt><dd>{{ \App\Models\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</dd>
        <dt>Ngày đặt</dt><dd>{{ $order->created_at->format('d/m/Y H:i') }}</dd>
        @if ($order->cancel_reason)
          <dt>Lý do hủy</dt><dd>{{ $order->cancel_reason }}</dd>
        @endif
      </dl>
    </section>

    <section class="panel">
      <h2>Xử lý đơn</h2>
      <form action="{{ route('admin.orders.update', $order) }}" method="post" class="form-grid">
        @csrf
        @method('PUT')
        @php($locked = in_array($order->status, \App\Models\Order::FINAL_STATUSES, true))
        <label class="field">
          <span>Trạng thái đơn</span>
          <select name="status" @disabled($locked)>
            @foreach (\App\Models\Order::STATUSES as $key => $label)
              <option value="{{ $key }}" @selected($order->status === $key)>{{ $label }}</option>
            @endforeach
          </select>
          @if ($locked)
            <input type="hidden" name="status" value="{{ $order->status }}" />
            <small>Đơn đã hủy/trả, kho đã được hoàn lại.</small>
          @else
            <small>Chuyển sang Hủy / Trả hàng sẽ cộng lại tồn kho.</small>
          @endif
        </label>
        <label class="field">
          <span>Thanh toán</span>
          <select name="payment_status">
            @foreach (\App\Models\Order::PAYMENT_STATUSES as $key => $label)
              <option value="{{ $key }}" @selected($order->payment_status === $key)>{{ $label }}</option>
            @endforeach
          </select>
          @if ($order->paid_at)
            <small>Thanh toán lúc {{ $order->paid_at->format('d/m/Y H:i') }}</small>
          @endif
        </label>
        <label class="field field-full">
          <span>Ghi chú (lý do hủy, mã vận đơn...)</span>
          <input type="text" name="note" maxlength="255" />
        </label>
        <div class="form-actions field-full">
          <button class="btn btn-primary" type="submit">Cập nhật</button>
        </div>
      </form>
    </section>
  </div>

  <section class="panel panel-flush">
    <table class="table">
      <tr><th>Sản phẩm</th><th>SKU</th><th class="num">Đơn giá</th><th class="num">SL</th><th class="num">Thành tiền</th></tr>
      @foreach ($order->items as $item)
        <tr>
          <td>{{ $item->product_name }} <span class="muted">— {{ $item->variant_name }}</span></td>
          <td class="muted">{{ $item->sku }}</td>
          <td class="num">{{ vnd($item->unit_price) }}</td>
          <td class="num">{{ $item->quantity }}</td>
          <td class="num">{{ vnd($item->line_total) }}</td>
        </tr>
      @endforeach
      <tr><td colspan="4" class="num">Tạm tính</td><td class="num">{{ vnd($order->subtotal) }}</td></tr>
      @if ($order->discount_amount)
        <tr><td colspan="4" class="num">Giảm giá</td><td class="num">−{{ vnd($order->discount_amount) }}</td></tr>
      @endif
      <tr><td colspan="4" class="num">Phí ship</td><td class="num">{{ vnd($order->shipping_fee) }}</td></tr>
      <tr><td colspan="4" class="num"><strong>Tổng cộng</strong></td><td class="num"><strong>{{ vnd($order->total_amount) }}</strong></td></tr>
    </table>
  </section>

  <section class="panel">
    <h2>Lịch sử</h2>
    <ul class="timeline">
      @foreach ($order->statusHistory->sortByDesc('id') as $h)
        <li>
          <strong>{{ $h->created_at->format('d/m/Y H:i') }}</strong> —
          @if ($h->from_status && $h->from_status !== $h->to_status)
            {{ \App\Models\Order::STATUSES[$h->from_status] ?? $h->from_status }} →
          @endif
          <span class="badge badge-{{ $h->to_status }}">{{ \App\Models\Order::STATUSES[$h->to_status] ?? $h->to_status }}</span>
          <span class="muted">bởi {{ $h->changer?->displayName() ?? 'khách' }}</span>
          @if ($h->note)
            <br />{{ $h->note }}
          @endif
        </li>
      @endforeach
    </ul>
  </section>
@endsection
