@extends('layouts.app')

@section('title', 'Đặt hàng thành công – Petio')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/checkout.css') }}" />
@endpush

@section('content')
<main class="main section">
  <div class="container">
    <div class="checkout-box checkout-done">
      <span class="material-symbols-outlined icon">check_circle</span>
      <h1>Cảm ơn bạn đã đặt hàng!</h1>
      <p>Mã đơn hàng: <strong>{{ $order->order_code }}</strong></p>
      <p>Petio sẽ gọi số <strong>{{ $order->receiver_phone }}</strong> để xác nhận trong thời gian sớm nhất.</p>

      <ul class="checkout-lines">
        @foreach ($order->items as $item)
          <li>
            <div>
              <p>{{ $item->product_name }}</p>
              <small>
                @if ($item->variant_name !== 'Mặc định') {{ $item->variant_name }} · @endif
                {{ vnd($item->unit_price) }} × {{ $item->quantity }}
              </small>
            </div>
            <strong>{{ vnd($item->line_total) }}</strong>
          </li>
        @endforeach
      </ul>

      <dl class="checkout-totals">
        <dt>Tạm tính</dt><dd>{{ vnd($order->subtotal) }}</dd>
        @if ($order->discount_amount)
          <dt>Giảm giá</dt><dd>−{{ vnd($order->discount_amount) }}</dd>
        @endif
        <dt>Phí vận chuyển</dt><dd>{{ $order->shipping_fee ? vnd($order->shipping_fee) : 'Miễn phí' }}</dd>
        <dt class="grand">Tổng thanh toán</dt><dd class="grand">{{ vnd($order->total_amount) }}</dd>
      </dl>

      <p>
        Giao tới: {{ $order->ship_address }}, {{ $order->ship_ward }}, {{ $order->ship_district }}, {{ $order->ship_province }}<br />
        Thanh toán: {{ \App\Models\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}
      </p>

      @if($order->user_id && $order->user_id === auth()->id())<p><a class="btn btn-primary" href="{{ route('customer.orders.show',$order) }}">Theo dõi / quản lý đơn hàng</a></p>@endif
      @if($order->payment_method === 'bank_transfer')<p>Đơn chưa được xác nhận thanh toán tự động. <a href="{{ route('contact') }}">Liên hệ Petio</a> để nhận thông tin chuyển khoản, kèm mã đơn {{ $order->order_code }}.</p>@endif
      <a href="{{ route('shop') }}" class="btn btn-primary">Tiếp tục mua sắm</a>
    </div>
  </div>
</main>
@endsection
