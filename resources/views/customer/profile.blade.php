@extends('customer.layout')
@section('page-title','Hồ sơ của bạn')
@section('account-content')
@php($u=auth()->user())
<div class="pc-heading"><div><span class="pc-kicker">01 / THÔNG TIN CÁ NHÂN</span><h1>Hồ sơ của sen.</h1><p>Một góc nhỏ mang dấu ấn của bạn. Cập nhật thông tin để mỗi lần mua sắm thuận tiện hơn.</p></div></div>
<div class="pc-profile-grid">
 <section class="pc-panel pc-avatar-panel"><span class="pc-kicker">CHÂN DUNG CỦA SEN</span><div id="avatar-current">@include('customer.avatar')</div><img id="avatar-preview" class="pc-avatar" alt="Ảnh mới đã chọn" hidden><h2>{{ $u->displayName() }}</h2><p class="pc-muted">Một gương mặt thân quen,<br>một kết nối gần gũi hơn.</p>
 <form action="{{ route('customer.avatar.update') }}" method="post" enctype="multipart/form-data" class="pc-form">@csrf<label class="pc-file-label" for="avatar-file"><span class="material-symbols-outlined">add_a_photo</span> Chọn ảnh từ máy</label><input id="avatar-file" name="avatar" type="file" accept="image/jpeg,image/png,image/webp" required><small>JPG, PNG, WebP · Tối đa 2 MB</small><p id="avatar-feedback" role="status"></p><button class="pc-btn" type="submit">Lưu ảnh đại diện</button></form>
 </section>
 <form class="pc-panel pc-form" method="post" action="{{ route('customer.profile.update') }}">@csrf @method('PUT')
 <div class="pc-section-number">01</div><h2>Thông tin hồ sơ</h2><p class="pc-muted">Dùng thông tin này để Petio liên hệ khi cần.</p>
 <label>Họ và tên<input name="full_name" value="{{ old('full_name',$u->full_name) }}" required maxlength="100" autocomplete="name"></label>
 <label>Email<input type="email" name="email" value="{{ old('email',$u->email) }}" maxlength="150" autocomplete="email"></label>
 <label>Số điện thoại<input name="phone" value="{{ old('phone',$u->phone) }}" pattern="0[0-9]{9}" maxlength="10" autocomplete="tel"></label>
 <label>Tên đăng nhập<input value="{{ $u->username }}" disabled></label><button class="pc-btn" type="submit">Lưu thông tin</button></form>
<aside class="pc-panel pc-profile-note"><span class="material-symbols-outlined">verified_user</span><h2>Góc riêng, an tâm hơn</h2><p>Thông tin đầy đủ giúp Petio liên hệ đúng người khi xác nhận đơn hàng.</p><a class="pc-text-link" href="{{ route('customer.privacy') }}">Đổi mật khẩu & bảo mật →</a><hr><a class="pc-text-link" href="{{ route('customer.addresses') }}">Quản lý địa chỉ nhận hàng →</a></aside>
</div>
@endsection
