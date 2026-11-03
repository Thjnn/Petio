@extends('customer.layout')
@section('page-title', 'Bảo mật tài khoản')
@section('account-content')
<div class="pc-heading"><h1>Bảo mật tài khoản</h1></div>
<section class="pc-panel"><h2>Giữ tài khoản an toàn</h2><p>Sử dụng mật khẩu riêng cho Petio và không chia sẻ mật khẩu với người khác. Đăng xuất khi dùng thiết bị chung.</p><a class="pc-btn" href="{{ route('customer.profile') }}">Cập nhật hồ sơ và mật khẩu</a><p>Nếu cần hỗ trợ về thông tin tài khoản, vui lòng liên hệ Petio.</p><a href="{{ route('customer.support') }}">Liên hệ hỗ trợ</a></section>
@endsection
