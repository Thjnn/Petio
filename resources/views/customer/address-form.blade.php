@extends('customer.layout')
@section('page-title','Địa chỉ nhận hàng')
@section('account-content')
<div class="pc-heading"><div><span class="pc-eyebrow">TÀI KHOẢN PETIO</span><h1>Địa chỉ nhận hàng</h1><p>Thông tin này được dùng để điền nhanh khi thanh toán.</p></div></div>

<form class="pc-panel pc-form" method="post" action="{{ $address->exists ? route('customer.addresses.update',$address->id) : route('customer.addresses.store') }}">@csrf @if($address->exists) @method('PUT') @endif
<div class="pc-two-col">
@foreach(['label'=>'Tên gợi nhớ (Nhà riêng / Công ty)','receiver_name'=>'Họ tên người nhận','receiver_phone'=>'Số điện thoại','ship_province'=>'Tỉnh / Thành phố','ship_district'=>'Quận / Huyện / Khu vực','ship_ward'=>'Phường / Xã','ship_address'=>'Số nhà, tên đường'] as $key=>$label)
<label>{{ $label }}<input name="{{ $key }}" value="{{ old($key,$address->$key) }}" required maxlength="{{ $key==='ship_address' ? 255 : ($key==='label' ? 50 : 100) }}" @if($key==='receiver_phone') pattern="0[0-9]{9}" @endif></label>
@endforeach
</div><label class="pc-checkbox"><input type="checkbox" name="is_default" value="1" @checked(old('is_default',$address->is_default))>Đặt làm địa chỉ mặc định</label><div class="pc-actions"><button class="pc-btn">Lưu địa chỉ</button><a class="pc-text-link" href="{{ route('customer.addresses') }}">Quay lại</a></div></form>
@endsection
