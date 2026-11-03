@extends('customer.layout')
@section('page-title','Hồ sơ của bạn')
@section('account-content')
@php($u=auth()->user())
<div class="pc-heading"><div><span class="pc-kicker">01 / THÔNG TIN CÁ NHÂN</span><h1>Một chút về bạn.</h1><p>Cá nhân hóa góc nhỏ của mình tại Petio.</p></div></div>
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
 <form class="pc-panel pc-form" method="post" action="{{ route('customer.password') }}">@csrf @method('PUT')
 <div class="pc-section-number">02</div><h2>Bảo vệ tài khoản</h2><p class="pc-muted">Một mật khẩu riêng giúp bạn yên tâm hơn.</p>
 <label>Mật khẩu hiện tại<input type="password" name="current_password" required autocomplete="current-password"></label><label>Mật khẩu mới<input type="password" name="password" required minlength="8" autocomplete="new-password"><small>Tối thiểu 8 ký tự.</small></label><label>Nhập lại mật khẩu mới<input type="password" name="password_confirmation" required minlength="8" autocomplete="new-password"></label><button class="pc-btn pc-btn-outline" type="submit">Đổi mật khẩu</button></form>
</div>
@endsection
