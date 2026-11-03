@extends('layouts.app')
@section('title','Thanh toán – Petio')
@push('styles')<link rel="stylesheet" href="{{ asset('assets/css/checkout.css') }}"><link rel="stylesheet" href="{{ asset('assets/css/checkout-refresh.css') }}?v=2">@endpush
@push('scripts')<script src="{{ asset('assets/js/checkout-refresh.js') }}?v=2" defer></script>@endpush
@section('content')
@php($savedMode = old('address_mode', $selectedAddress ? 'saved' : 'new') === 'saved' && $addresses->isNotEmpty())
<main class="px-checkout"><div class="px-wrap">
 <div class="px-top"><a href="{{ route('shop') }}">← Tiếp tục mua sắm</a><span><span class="material-symbols-outlined">lock</span> Kiểm tra trước khi đặt hàng</span></div>
 <header class="px-heading"><span class="px-kicker">PETIO / CHECKOUT</span><h1>Một bước nữa,<br><em>quà sẽ đến với bé.</em></h1><p>Xác nhận địa chỉ, chọn cách thanh toán và kiểm tra đơn hàng của bạn.</p></header>
 <div class="px-steps"><span>✓ Giỏ hàng</span><b>02 · Thanh toán</b><span>03 · Hoàn tất</span></div>
 @if($errors->any())<div class="px-error" role="alert"><strong>Chưa thể đặt hàng:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
 <form class="px-layout" action="{{ route('checkout.store') }}" method="post" id="petio-checkout">@csrf
  <div class="px-sections">
   <section class="px-card"><div class="px-section-title"><span>01</span><h2>Giao yêu thương đến</h2></div>
    @guest<p class="px-hint">Bạn có thể đặt hàng không cần tài khoản, hoặc <a href="{{ route('login') }}">đăng nhập</a> để dùng địa chỉ đã lưu.</p>@endguest
    @if($addresses->isNotEmpty())
    <div class="px-address-mode"><label><input type="radio" name="address_mode" value="saved" @checked($savedMode)> Dùng địa chỉ đã lưu</label><label><input type="radio" name="address_mode" value="new" @checked(!$savedMode)> Giao đến địa chỉ mới</label></div>
    <fieldset id="saved-address-fields" @disabled(!$savedMode) @if(!$savedMode) hidden @endif>
     @foreach($addresses as $address)<label class="px-address-card"><input type="radio" name="address_id" value="{{ $address->id }}" @checked($selectedAddress?->id === $address->id)><span><strong>{{ $address->receiver_name }} <small>{{ $address->receiver_phone }}</small></strong><span>{{ $address->ship_address }}, {{ $address->ship_ward }}, {{ $address->ship_district }}, {{ $address->ship_province }}</span><b class="px-address-label">{{ $address->label }} @if($address->is_default) · Mặc định @endif</b></span></label>@endforeach
     <a class="px-text-link" href="{{ route('customer.addresses') }}">Quản lý sổ địa chỉ ↗</a>
    </fieldset>
    @else<input type="hidden" name="address_mode" value="new">@auth<p class="px-hint">Bạn chưa lưu địa chỉ. Nhập thông tin nhận hàng bên dưới hoặc <a href="{{ route('customer.addresses.create') }}">thêm vào sổ địa chỉ</a>.</p>@endauth @endif
    <fieldset id="new-address-fields" class="px-fields" @disabled($savedMode) @if($savedMode) hidden @endif>
     <label>Họ tên người nhận *<input name="receiver_name" value="{{ old('receiver_name',$user?->full_name) }}" required maxlength="100" autocomplete="shipping name"></label>
     <label>Số điện thoại *<input type="tel" name="receiver_phone" value="{{ old('receiver_phone',$user?->phone) }}" required pattern="0[0-9]{9}" maxlength="10" autocomplete="shipping tel" placeholder="VD: 0912345678"></label>
     <label>Tỉnh / Thành phố *<input name="ship_province" value="{{ old('ship_province') }}" required maxlength="100" autocomplete="shipping address-level1"></label>
     <label>Quận / Huyện *<input name="ship_district" value="{{ old('ship_district') }}" required maxlength="100" autocomplete="shipping address-level2"></label>
     <label>Phường / Xã *<input name="ship_ward" value="{{ old('ship_ward') }}" required maxlength="100" autocomplete="shipping address-level3"></label>
     <label>Số nhà, tên đường *<input name="ship_address" value="{{ old('ship_address') }}" required maxlength="255" autocomplete="shipping street-address"></label>
    </fieldset>
    <details class="px-extra" @if(old('receiver_email') || old('note')) open @endif><summary>Email liên hệ & ghi chú (không bắt buộc)</summary><label>Email liên hệ<input name="receiver_email" type="email" value="{{ old('receiver_email',$user?->email) }}" maxlength="150" autocomplete="email"></label><label>Lưu ý cho đơn hàng<textarea name="note" rows="3" maxlength="500" placeholder="VD: Gọi trước khi giao hàng">{{ old('note') }}</textarea></label></details>
   </section>
   <section class="px-card"><div class="px-section-title"><span>02</span><h2>Cách bạn thanh toán</h2></div>
    @foreach(\App\Models\Order::PAYMENT_METHODS as $key=>$label)<label class="px-payment"><input type="radio" name="payment_method" value="{{ $key }}" @checked(old('payment_method','cod')===$key) required><span class="material-symbols-outlined">{{ $key==='cod' ? 'payments' : 'account_balance' }}</span><span><strong>{{ $label }}</strong><small>{{ $key==='cod' ? 'Thanh toán cho người giao hàng khi nhận sản phẩm.' : 'Liên hệ Petio để nhận thông tin chuyển khoản sau khi đặt đơn.' }}</small></span></label>@endforeach
   </section>
   <p class="px-help"><span class="material-symbols-outlined">support_agent</span> Cần giúp đỡ? <a href="{{ auth()->check() ? route('customer.support') : route('contact') }}">Liên hệ Petio</a></p>
  </div>
  <aside class="px-card px-summary"><div class="px-section-title"><span>03</span><h2>Túi quà của bé <small>{{ $lines->sum('qty') }} sản phẩm</small></h2></div>
   <ul class="px-lines">@foreach($lines as $line)<li><div class="px-line-img"><img src="{{ asset($line['variant']->product->thumbnail_url) }}" alt="{{ $line['variant']->product->name }}"><b>{{ $line['qty'] }}</b></div><div><strong>{{ $line['variant']->product->name }}</strong><small>{{ $line['variant']->name }} · {{ vnd($line['variant']->price) }}</small></div><span>{{ vnd($line['line_total']) }}</span></li>@endforeach</ul>
   <label class="px-label" for="coupon-code">Ưu đãi dành cho bạn</label><div class="px-coupon"><input id="coupon-code" name="coupon_code" value="{{ old('coupon_code',$selectedCoupon) }}" placeholder="Nhập mã giảm giá" maxlength="30"><button type="button" id="apply-coupon" data-url="{{ route('checkout.coupon') }}">Áp dụng</button></div>
   @if($coupons->isNotEmpty())<div class="px-coupon-chips">@foreach($coupons as $coupon)<button type="button" data-coupon="{{ $coupon->code }}" title="{{ $coupon->description }}">{{ $coupon->code }}</button>@endforeach</div>@endif
   <p id="coupon-feedback" class="px-hint" role="status" aria-live="polite">Mã giảm giá được kiểm tra lại khi đặt hàng.</p>
   <dl class="px-totals"><dt>Tạm tính</dt><dd>{{ vnd($subtotal) }}</dd><dt>Phí vận chuyển</dt><dd>{{ $shippingFee ? vnd($shippingFee) : 'Miễn phí' }}</dd><dt>Giảm giá</dt><dd id="coupon-discount">—</dd><dt class="px-grand">Tổng thanh toán</dt><dd class="px-grand" id="checkout-total" data-base="{{ $subtotal+$shippingFee }}">{{ vnd($subtotal+$shippingFee) }}</dd></dl>
   <button class="px-place-order" type="submit">Đặt hàng <span>→</span></button><p class="px-order-note">Hãy kiểm tra thông tin trước khi đặt. Bạn có thể hủy đơn trong tài khoản khi đơn chưa chuyển sang giao hàng.</p>
  </aside>
 </form>
</div></main>
@endsection
