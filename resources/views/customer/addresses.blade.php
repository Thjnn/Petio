@extends('customer.layout')
@section('page-title','Sổ địa chỉ')
@section('account-content')
<div class="pc-heading"><div><span class="pc-eyebrow">TÀI KHOẢN PETIO</span><h1>Sổ địa chỉ</h1><p>Lưu địa chỉ để lần mua tiếp theo nhanh hơn.</p></div></div>

<a class="pc-btn" href="{{ route('customer.addresses.create') }}">+ Thêm địa chỉ</a>
<div class="pc-two-col pc-bottom">@forelse($addresses as $a)<article class="pc-panel pc-address"><div class="pc-heading"><span class="material-symbols-outlined">home_pin</span><strong>{{ $a->label }}</strong>@if($a->is_default)<span class="pc-badge">Mặc định</span>@endif</div><h2>{{ $a->receiver_name }}</h2><p>{{ $a->receiver_phone }}</p><p>{{ $a->ship_address }}, {{ $a->ship_ward }}, {{ $a->ship_district }}, {{ $a->ship_province }}</p><div class="pc-actions"><a class="pc-text-link" href="{{ route('customer.addresses.edit',$a->id) }}">Chỉnh sửa</a><form method="post" action="{{ route('customer.addresses.destroy',$a->id) }}" data-confirm="Xóa địa chỉ này?">@csrf @method('DELETE')<button class="pc-text-link">Xóa</button></form></div></article>@empty<div class="pc-panel pc-empty"><span class="material-symbols-outlined">location_on</span><h2>Yêu thương sẽ được gửi đến đâu?</h2><p>Thêm địa chỉ đầu tiên để dùng khi thanh toán.</p></div>@endforelse</div>
@endsection
