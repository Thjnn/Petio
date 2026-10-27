@extends('customer.layout')
@section('page-title','Đơn hàng của bạn')
@section('account-content')
<div class="pc-heading"><div><span class="pc-eyebrow">TÀI KHOẢN PETIO</span><h1>Đơn hàng của bạn</h1><p>Theo dõi những món quà đang trên đường đến với bé.</p></div></div>

<form method="get" class="pc-panel pc-filter"><label class="pc-sr-only" for="order-search">Tìm mã đơn hàng</label><input id="order-search" name="q" value="{{ request('q') }}" placeholder="Tìm theo mã đơn hàng…"><label class="pc-sr-only" for="order-status">Trạng thái</label><select id="order-status" name="status"><option value="">Tất cả trạng thái</option>@foreach(\App\Models\Order::STATUSES as $key=>$label)<option value="{{ $key }}" @selected(request('status')===$key)>{{ $label }}</option>@endforeach</select><button class="pc-btn">Tìm đơn</button></form>
@forelse($orders as $o)
<article class="pc-panel pc-order"><div class="pc-heading"><div><strong>#{{ $o->order_code }}</strong><small>{{ $o->created_at->format('d/m/Y • H:i') }}</small></div><span class="pc-badge status-{{ $o->status }}">{{ \App\Models\Order::STATUSES[$o->status] }}</span></div>
@foreach($o->items->take(2) as $item) @include('customer.order-item') @endforeach
@if($o->items->count()>2)<p class="pc-muted">Và {{ $o->items->count()-2 }} sản phẩm khác</p>@endif
<div class="pc-order-foot"><span>Tổng thanh toán <strong>{{ vnd($o->total_amount) }}</strong></span><a class="pc-btn pc-btn-outline" href="{{ route('customer.orders.show',$o) }}">Xem chi tiết →</a></div>@include('customer.cancel-order', ['order'=>$o])</article>
@empty<div class="pc-panel pc-empty"><span class="material-symbols-outlined">inventory_2</span><h2>Chưa tìm thấy đơn hàng</h2><p>Đơn hàng đặt bằng tài khoản này sẽ xuất hiện ở đây.</p><a class="pc-btn" href="{{ route('shop') }}">Dạo một vòng cửa hàng</a></div>@endforelse
<div class="pc-pagination">{{ $orders->links('customer.pagination') }}</div>
@endsection
