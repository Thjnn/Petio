@extends('customer.layout')
@section('page-title','Chi tiết đơn hàng')
@section('account-content')
<div class="pc-heading"><div><span class="pc-eyebrow">TÀI KHOẢN PETIO</span><h1>Chi tiết đơn hàng</h1><p>Thông tin nhận hàng, sản phẩm và lịch sử xử lý.</p></div></div>

<a class="pc-text-link" href="{{ route('customer.orders') }}">← Trở về đơn hàng</a>
<div class="pc-panel pc-bottom"><div class="pc-heading"><h2>#{{ $order->order_code }}</h2><span class="pc-badge">{{ \App\Models\Order::STATUSES[$order->status] }}</span></div>
@if(!in_array($order->status, ['cancelled','returned']))
<div class="pc-order-progress">@foreach(['pending'=>'Chờ xác nhận','confirmed'=>'Đã xác nhận','shipping'=>'Đang giao','completed'=>'Hoàn thành'] as $key=>$label)<div class="{{ array_search($key, ['pending','confirmed','shipping','completed']) <= array_search($order->status, ['pending','confirmed','shipping','completed']) ? 'done' : '' }}">{{ $label }}</div>@endforeach</div>
@endif
@foreach($order->items as $item) @include('customer.order-item') @endforeach
<dl class="pc-totals"><dt>Tạm tính</dt><dd>{{ vnd($order->subtotal) }}</dd><dt>Vận chuyển</dt><dd>{{ vnd($order->shipping_fee) }}</dd><dt>Giảm giá</dt><dd>−{{ vnd($order->discount_amount) }}</dd><dt><strong>Tổng thanh toán</strong></dt><dd><strong>{{ vnd($order->total_amount) }}</strong></dd></dl></div>
<div class="pc-two-col pc-bottom"><section class="pc-panel"><h2>Giao đến</h2><p><strong>{{ $order->receiver_name }}</strong> · {{ $order->receiver_phone }}</p><p>{{ $order->ship_address }}, {{ $order->ship_ward }}, {{ $order->ship_district }}, {{ $order->ship_province }}</p><hr><p>{{ \App\Models\Order::PAYMENT_METHODS[$order->payment_method] ?? $order->payment_method }}</p><span class="pc-badge">{{ \App\Models\Order::PAYMENT_STATUSES[$order->payment_status] }}</span>@if($order->cancel_reason)<p>Lý do hủy: {{ $order->cancel_reason }}</p>@endif<a class="pc-text-link" href="{{ route('customer.support') }}">Cần hỗ trợ về đơn này? →</a></section>
<section class="pc-panel"><h2>Hành trình đơn hàng</h2><ol class="pc-timeline">@forelse($order->statusHistory->sortByDesc('id') as $history)<li><strong>{{ \App\Models\Order::STATUSES[$history->to_status] ?? $history->to_status }}</strong><small>{{ $history->created_at->format('d/m/Y H:i') }}</small></li>@empty<li>Đơn hàng đã được ghi nhận.</li>@endforelse</ol></section></div>
@include('customer.cancel-order')
@endsection
