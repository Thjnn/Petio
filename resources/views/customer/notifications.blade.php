@extends('customer.layout')
@section('page-title', 'Thông báo')
@section('account-content')
<div class="pc-heading"><h1>Thông báo</h1></div>
@forelse($notices as $notice)<article class="pc-panel"><h2>Đơn hàng #{{ $notice->order_code }}</h2><p>{{ \App\Models\Order::STATUSES[$notice->to_status] ?? $notice->to_status }} · {{ $notice->created_at }}</p><a href="{{ route('customer.orders.show', $notice->order_code) }}">Xem đơn hàng</a>@unless($notice->read_at)<form method="post" action="{{ route('customer.notifications.read', $notice->id) }}">@csrf @method('PATCH')<button class="pc-btn">Đánh dấu đã đọc</button></form>@endunless<form method="post" action="{{ route('customer.notifications.destroy', $notice->id) }}">@csrf @method('DELETE')<button class="pc-btn pc-btn-outline">Xóa thông báo</button></form></article>@empty<p class="pc-panel">Chưa có thông báo.</p>@endforelse{{ $notices->links('customer.pagination') }}
@endsection
