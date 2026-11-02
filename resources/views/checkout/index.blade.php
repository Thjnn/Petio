@extends('layouts.app')

@section('title', 'Thanh toán – Petio')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/checkout.css') }}" />
@endpush

@section('content')
<main class="main section">
  <div class="container">
    <h1 class="section-title"><span class="hl">Thanh toán</span> đơn hàng</h1>

    <form class="checkout" action="{{ route('checkout.store') }}" method="post">
      @csrf

      <!-- ---------- thông tin nhận hàng ---------- -->
      <section class="checkout-box">
        <h2>Thông tin nhận hàng</h2>

        @guest
          <p class="checkout-hint">
            Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a> để theo dõi đơn dễ hơn
            (không bắt buộc).
          </p>
        @endguest

        <div class="checkout-grid">
          <label>Họ tên người nhận *
            <input type="text" name="receiver_name" value="{{ old('receiver_name', $user?->full_name) }}" required maxlength="100" />
          </label>
          <label>Số điện thoại *
            <input type="tel" name="receiver_phone" value="{{ old('receiver_phone', $user?->phone) }}" required pattern="0[0-9]{9}" title="10 chữ số, bắt đầu bằng 0" />
          </label>
          <label class="span-2">Email (nhận thông báo đơn hàng)
            <input type="email" name="receiver_email" value="{{ old('receiver_email', $user?->email) }}" maxlength="150" />
          </label>
          <label>Tỉnh / Thành phố *
            <input type="text" name="ship_province" value="{{ old('ship_province') }}" required maxlength="100" />
          </label>
          <label>Quận / Huyện *
            <input type="text" name="ship_district" value="{{ old('ship_district') }}" required maxlength="100" />
          </label>
          <label>Phường / Xã *
            <input type="text" name="ship_ward" value="{{ old('ship_ward') }}" required maxlength="100" />
          </label>
          <label>Số nhà, tên đường *
            <input type="text" name="ship_address" value="{{ old('ship_address') }}" required maxlength="255" />
          </label>
          <label class="span-2">Ghi chú
            <textarea name="note" rows="3" maxlength="500">{{ old('note') }}</textarea>
          </label>
        </div>

        <h2>Phương thức thanh toán</h2>
        <div class="checkout-payments">
          @foreach (\App\Models\Order::PAYMENT_METHODS as $value => $label)
            <label class="checkout-radio">
              <input type="radio" name="payment_method" value="{{ $value }}" @checked(old('payment_method', 'cod') === $value) />
              <span>{{ $label }}</span>
            </label>
          @endforeach
        </div>
      </section>

      <!-- ---------- tóm tắt đơn ---------- -->
      <aside class="checkout-box checkout-summary">
        <h2>Đơn hàng ({{ $lines->sum('qty') }} sản phẩm)</h2>

        <ul class="checkout-lines">
          @foreach ($lines as $line)
            <li>
              <img src="{{ asset($line['variant']->product->thumbnail_url) }}" alt="" />
              <div>
                <p>{{ $line['variant']->product->name }}</p>
                <small>
                  @if ($line['variant']->name !== 'Mặc định') {{ $line['variant']->name }} · @endif
                  {{ vnd($line['variant']->price) }} × {{ $line['qty'] }}
                </small>
              </div>
              <strong>{{ vnd($line['line_total']) }}</strong>
            </li>
          @endforeach
        </ul>

        <label class="checkout-coupon">Mã giảm giá
          <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="VD: FIRST35" maxlength="30" />
        </label>

        <dl class="checkout-totals">
          <dt>Tạm tính</dt><dd>{{ vnd($subtotal) }}</dd>
          <dt>Phí vận chuyển</dt><dd>{{ $shippingFee ? vnd($shippingFee) : 'Miễn phí' }}</dd>
          <dt class="grand">Tổng (chưa trừ mã giảm giá)</dt><dd class="grand">{{ vnd($subtotal + $shippingFee) }}</dd>
        </dl>

        <button type="submit" class="btn btn-primary btn-block">Đặt hàng</button>
      </aside>
    </form>
  </div>
</main>
@endsection
