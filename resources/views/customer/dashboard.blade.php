@extends('customer.layout')
@section('page-title','Góc của sen')
@section('account-content')
@php($u=auth()->user())
<section class="pc-welcome">
 <div class="pc-welcome-copy"><span class="pc-kicker">MY PETIO / GÓC CỦA SEN</span><div class="pc-welcome-person">@include('customer.avatar')<span>Rất vui được gặp lại bạn</span></div><h1>Chào {{ $u->displayName() }}!<br><em>Yêu thương bắt đầu<br>từ những điều nhỏ.</em></h1><p>Đơn hàng, ưu đãi và những món bé thích —<br>tất cả ở ngay trong góc nhỏ của bạn.</p><a class="pc-btn" href="{{ route('shop') }}">Chọn quà cho bé <span>↗</span></a></div>
 <div class="pc-welcome-visual"><span class="pc-circle"></span><img src="{{ asset('assets/file_anh/images-chotrang-removebg-preview.png') }}" alt="Người bạn bốn chân của Petio"><span class="pc-floating-tag">Một chiếc đuôi vui.<br><strong>Một ngày thật đẹp.</strong></span><span class="material-symbols-outlined pc-floating-paw">pets</span></div>
</section>
<div class="pc-stats">
 @foreach([['orders','inventory_2',$orderCount,'Đơn hàng'],['orders','local_shipping',$shippingCount,'Đang giao'],['favorites','favorite',$favoriteCount,'Món yêu thích'],['notifications','notifications',$unreadCount,'Thông báo mới']] as [$route,$icon,$value,$label])
 <a href="{{ route('customer.'.$route, $label === 'Đang giao' ? ['status'=>'shipping'] : []) }}" class="pc-stat"><span class="material-symbols-outlined">{{ $icon }}</span><div><strong>{{ $value }}</strong><small>{{ $label }}</small></div></a>
 @endforeach
</div>
<div class="pc-heading"><div><span class="pc-kicker">MỌI ĐIỀU BẠN CẦN</span><h2>Chăm bé dễ hơn, mỗi ngày.</h2></div><p>Chọn một mục để bắt đầu</p></div>
<div class="pc-hub-grid">
 @foreach([['profile','person','Hồ sơ','Thông tin cá nhân & ảnh đại diện'],['orders','inventory_2','Đơn hàng','Theo dõi, xem chi tiết & hủy đơn'],['favorites','favorite','Yêu thích','Những món quà bạn đã lưu'],['notifications','notifications','Thông báo','Cập nhật hành trình đơn hàng'],['coupons','confirmation_number','Mã giảm giá','Ưu đãi cho lần mua tiếp theo'],['addresses','location_on','Địa chỉ','Giao yêu thương đến đúng nơi'],['privacy','verified_user','Bảo mật','Tìm hiểu cách bảo vệ thông tin'],['support','support_agent','Hỗ trợ','Kết nối với đội ngũ Petio'],['about','pets','Về chúng tôi','Câu chuyện dành cho người yêu thú cưng']] as [$key,$icon,$title,$desc])
 <a class="pc-hub-card" href="{{ route('customer.'.$key) }}"><span class="material-symbols-outlined">{{ $icon }}</span><span class="pc-arrow">↗</span><h3>{{ $title }}</h3><p>{{ $desc }}</p></a>
 @endforeach
 <form class="pc-hub-card pc-hub-logout" method="post" action="{{ route('logout') }}">@csrf<button type="submit"><span class="material-symbols-outlined">logout</span><h3>Đăng xuất</h3><p>Hẹn gặp lại bạn và bé cưng!</p></button></form>
</div>
<div class="pc-bottom pc-bottom-banner"><div><span class="pc-kicker">MỘT CHÚT NUÔNG CHIỀU</span><h2>Đến giờ thư giãn của bé rồi!</h2><p>Khám phá dịch vụ tắm, vệ sinh và chăm sóc tại Petio.</p></div><a class="pc-btn pc-btn-outline" href="{{ route('spa') }}">Khám phá dịch vụ ↗</a></div>
@endsection
