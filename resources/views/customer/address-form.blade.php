@extends('customer.layout')
@section('page-title', 'Lưu địa chỉ')
@section('account-content')
<div class="pc-heading"><h1>Lưu địa chỉ</h1></div>
<form class="pc-panel pc-form" method="post" action="{{ $address->exists ? route('customer.addresses.update', $address) : route('customer.addresses.store') }}">@csrf @if($address->exists) @method('PUT') @endif
@foreach(['label'=>'Tên địa chỉ','receiver_name'=>'Tên người nhận','receiver_phone'=>'Số điện thoại','ship_province'=>'Tỉnh / thành phố','ship_district'=>'Quận / huyện','ship_ward'=>'Phường / xã','ship_address'=>'Số nhà, đường'] as $field=>$label)
<label for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" name="{{ $field }}" value="{{ old($field, $address->$field) }}" required>
@endforeach
<label><input type="checkbox" name="is_default" value="1" @checked(old('is_default', $address->is_default))> Đặt làm địa chỉ mặc định</label><button class="pc-btn">Lưu địa chỉ</button></form>
@endsection
