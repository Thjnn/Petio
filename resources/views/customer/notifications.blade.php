@extends('customer.layout')
@section('page-title','Thông báo')
@section('account-content')
<div class="pc-heading"><div><span class="pc-eyebrow">TÀI KHOẢN PETIO</span><h1>Thông báo</h1><p>Cập nhật thật từ hành trình đơn hàng của bạn.</p></div></div>

@forelse($notices as $n)<article class="pc-panel pc-notice {{ $n->read_at ? '' : 'is-unread' }}"><span class="material-symbols-outlined">local_shipping</span><div><span class="pc-badge">{{ $n->read_at ? 'Đã đọc' : 'Mới' }}</span><h2>Đơn #{{ $n->order_code }}: {{ \App\Models\Order::STATUSES[$n->to_status] ?? $n->to_status }}</h2><small>{{ \Illuminate\Support\Carbon::parse($n->created_at)->format('d/m/Y H:i') }}</small><a class="pc-text-link" href="{{ route('customer.orders.show',$n->order_code) }}">Xem đơn hàng →</a><div class="pc-actions">@if(!$n->read_at)<form method="post" action="{{ route('customer.notifications.read',$n->id) }}">@csrf @method('PATCH')<button class="pc-text-link">Đánh dấu đã đọc</button></form>@endif<form method="post" action="{{ route('customer.notifications.destroy',$n->id) }}">@csrf @method('DELETE')<button class="pc-text-link">Xóa thông báo</button></form></div></div></article>
@empty<div class="pc-panel pc-empty"><span class="material-symbols-outlined">notifications_none</span><h2>Bạn đã xem hết thông báo</h2><p>Khi đơn hàng có thay đổi, Petio sẽ cập nhật tại đây.</p></div>@endforelse
<div class="pc-pagination">{{ $notices->links('customer.pagination') }}</div>
@endsection
