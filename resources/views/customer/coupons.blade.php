@extends('customer.layout')
@section('page-title', 'Mã giảm giá')
@section('account-content')
<div class="pc-heading"><h1>Mã giảm giá</h1></div>
@forelse($coupons as $coupon)<article class="pc-panel"><h2>{{ $coupon->code }}</h2><p>Giảm {{ $coupon->discount_type === 'percent' ? $coupon->discount_value.'%' : vnd($coupon->discount_value) }} · Đơn tối thiểu {{ vnd($coupon->min_order_value) }}</p>@if($coupon->first_order_only)<p>Dành cho đơn hàng đầu tiên.</p>@endif @if($coupon->expires_at)<p>Hạn sử dụng: {{ $coupon->expires_at->format('d/m/Y') }}</p>@endif</article>@empty<p class="pc-panel">Chưa có mã giảm giá.</p>@endforelse
@endsection
