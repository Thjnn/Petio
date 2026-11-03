@extends('customer.layout')
@section('page-title','Chi tiết đơn hàng')
@section('account-content')
<div class="pc-heading"><div><span class="pc-eyebrow">TÀI KHOẢN PETIO</span><h1>Chi tiết đơn hàng</h1><p>Thông tin nhận hàng, sản phẩm và lịch sử xử lý.</p></div></div>

<a class="pc-text-link" href="{{ route('customer.orders') }}">← Trở về đơn hàng</a>
<div class="pc-panel pc-bottom"><div class="pc-heading"><h2>#{{ $order->order_code }}</h2><span class="pc-badge">{{ \App\Models\Order::STATUSES[$order->status] }}</span></div>
@if(!in_array($order->status, ['cancelled','returned']))
<div class="pc-order-progress">@foreach(['pending'=>'Chờ xác nhận','confirmed'=>'Chờ giao hàng','shipping'=>'Đang giao','completed'=>'Hoàn thành'] as $key=>$label)<div class="{{ array_search($key, ['pending','confirmed','shipping','completed']) <= array_search($order->status, ['pending','confirmed','shipping','completed']) ? 'done' : '' }}">{{ $label }}</div>@endforeach</div>
@endif
@foreach($order->items as $item) @include('customer.order-item') @endforeach
<dl class="pc-totals"><dt>Tạm tính</dt><dd>{{ vnd($order->subtotal) }}</dd><dt>Vận chuyển</dt><dd>{{ vnd($order->shipping_fee) }}</dd><dt>Ưu đãi vận chuyển</dt><dd>−{{ vnd($order->shipping_discount) }}</dd><dt>Giảm giá sản phẩm</dt><dd>−{{ vnd($order->discount_amount) }}</dd><dt><strong>Tổng thanh toán</strong></dt><dd><strong>{{ vnd($order->total_amount) }}</strong></dd></dl></div>
<div class="pc-two-col pc-bottom"><section class="pc-panel"><h2>Giao đến</h2><p><strong>{{ $order->receiver_name }}</strong> · {{ $order->receiver_phone }}</p><p>{{ $order->ship_address }}, {{ $order->ship_ward }}, {{ $order->ship_district }}, {{ $order->ship_province }}</p><hr><p>{{ \App\Models\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</p><span class="pc-badge">{{ \App\Models\Order::PAYMENT_STATUSES[$order->payment_status] }}</span>@if($order->cancel_reason)<p>Lý do hủy: {{ $order->cancel_reason }}</p>@endif<a class="pc-text-link" href="{{ route('customer.support') }}">Cần hỗ trợ về đơn này? →</a></section>
<section class="pc-panel"><h2>Hành trình đơn hàng</h2><ol class="pc-timeline">@forelse($order->statusHistory->sortByDesc('id') as $history)<li><strong>{{ \App\Models\Order::STATUSES[$history->to_status] ?? $history->to_status }}</strong><small>{{ $history->created_at->format('d/m/Y H:i') }}</small>@if($history->note)<p>{{ $history->note }}</p>@endif</li>@empty<li>Đơn hàng đã được ghi nhận.</li>@endforelse</ol></section></div>
<div class="pc-actions pc-bottom">@include('customer.order-actions')</div>@include('customer.cancel-order')
@if($order->status==='completed' && $order->received_at)<section id="reviews" class="pc-bottom"><header class="pc-page-head"><div><span class="pc-kicker">CHIA SẺ SAU MỖI LẦN MUA</span><h2>Bé có thích món quà này?</h2><p>Đánh giá của bạn giúp những người nuôi thú cưng lựa chọn dễ hơn.</p></div></header>
@foreach($order->items as $item)<article class="pc-panel pc-review-card">@include('customer.order-item')
@if($item->review)<p class="pc-stars" aria-label="{{ $item->review->rating }} trên 5 sao">{{ str_repeat('★',$item->review->rating) }}{{ str_repeat('☆',5-$item->review->rating) }}</p><p>{{ $item->review->content }}</p><small>Đã đánh giá · {{ $item->review->created_at->format('d/m/Y') }}</small>
@elseif($item->variant?->product)<form class="pc-form" method="post" action="{{ route('customer.orders.review',[$order,$item->id]) }}">@csrf<fieldset class="pc-rating"><legend>Mức độ hài lòng</legend>@foreach([5,4,3,2,1] as $star)<label><input type="radio" name="rating" value="{{ $star }}" required @checked((int)old('rating',5)===$star)><span>{{ $star }} ★</span></label>@endforeach</fieldset><label>Trải nghiệm của bạn<textarea name="content" rows="3" minlength="10" maxlength="2000" required placeholder="Chất lượng, kích thước và trải nghiệm của bé…">{{ old('content') }}</textarea></label><button class="pc-btn">Gửi đánh giá</button></form>
@else<p>Sản phẩm đã ngừng hiển thị. <a href="{{ route('customer.support') }}">Liên hệ hỗ trợ</a> nếu bạn cần giúp đỡ.</p>@endif</article>@endforeach</section>@endif
@endsection
