@extends('customer.layout')
@section('page-title','Ví ưu đãi')
@section('account-content')
<div class="pc-heading"><div><span class="pc-eyebrow">TÀI KHOẢN PETIO</span><h1>Ví ưu đãi</h1><p>Thêm một chút tiết kiệm, thêm một món quà cho bé.</p></div></div>

<div class="pc-coupon-banner"><span class="material-symbols-outlined">redeem</span><div><h2>Ưu đãi nhỏ, niềm vui to</h2><p>Sao chép mã hoặc chọn dùng khi thanh toán. Điều kiện được kiểm tra theo giỏ hàng thực tế.</p></div></div>
<div class="pc-two-col">@forelse($coupons as $c)
@php($reason=$c->rejectReason($c->min_order_value,auth()->user()))
<article class="pc-panel pc-coupon"><div class="pc-coupon-value">{{ $c->discount_type === 'percent' ? $c->discount_value.'%' : vnd($c->discount_value) }}<small>ƯU ĐÃI PETIO</small></div><div><h2>{{ $c->code }}</h2><p>{{ $c->description }}</p><small>Đơn từ {{ vnd($c->min_order_value) }}@if($c->max_discount) · Giảm tối đa {{ vnd($c->max_discount) }}@endif</small><small>{{ $c->expires_at ? 'Hết hạn: '.$c->expires_at->format('d/m/Y') : 'Không giới hạn ngày kết thúc' }}</small>@if($c->first_order_only)<small>Dành cho đơn hàng đầu tiên</small>@endif
@if($reason)<p class="pc-muted">{{ $reason }}</p>@else<div class="pc-actions"><button class="pc-btn pc-btn-outline" type="button" data-copy="{{ $c->code }}">Sao chép</button><a class="pc-text-link" href="{{ route('checkout.index',['coupon'=>$c->code]) }}">Dùng mã →</a></div>@endif</div></article>
@empty<div class="pc-panel pc-empty"><span class="material-symbols-outlined">confirmation_number</span><h2>Chưa có ưu đãi mới</h2><p>Ghé lại sau để khám phá các mã giảm giá của Petio.</p></div>@endforelse</div><p id="pc-copy-status" role="status" aria-live="polite"></p>
@endsection
