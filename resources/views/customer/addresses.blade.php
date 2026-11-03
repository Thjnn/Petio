@extends('customer.layout')
@section('page-title', 'Địa chỉ nhận hàng')
@section('account-content')
<div class="pc-heading"><h1>Địa chỉ nhận hàng</h1></div>
<a class="pc-btn" href="{{ route('customer.addresses.create') }}">Thêm địa chỉ</a>@forelse($addresses as $address)<article class="pc-panel"><h2>{{ $address->label }} @if($address->is_default)<small>· Mặc định</small>@endif</h2><p>{{ $address->receiver_name }} · {{ $address->receiver_phone }}</p><p>{{ $address->ship_address }}, {{ $address->ship_ward }}, {{ $address->ship_district }}, {{ $address->ship_province }}</p><a href="{{ route('customer.addresses.edit', $address) }}">Sửa địa chỉ</a><form method="post" action="{{ route('customer.addresses.destroy', $address) }}">@csrf @method('DELETE')<button class="pc-btn pc-btn-outline">Xóa địa chỉ</button></form></article>@empty<p class="pc-panel">Bạn chưa lưu địa chỉ nhận hàng.</p>@endforelse
@endsection
