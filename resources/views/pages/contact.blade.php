@extends('layouts.app')

@section('title', 'Liên hệ – Petio')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/lienhe.css') }}" />
@endpush

@section('content')
<!-- main -->
<div class="main">
  <!-- hero -->
  <div class="hero">
    <div class="main-content">
      <div class="body">
        <div class="banner">
          <div class="banner-img map-container">
            <iframe
              src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d735.0803886506095!2d106.36738726626626!3d10.353142991898316!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x310aafea51460e61%3A0x790a07c73d60253e!2zxJDhuqFpIFZp4buHdCBCb29rc3RvcmUgJiBDb2ZmZWU!5e0!3m2!1svi!2s!4v1793635362189!5m2!1svi!2s"
              width="100%"
              height="400"
              style="border:0;"
              allowfullscreen
              loading="lazy"
              referrerpolicy="no-referrer-when-downgrade">
            </iframe>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- content -->
  <div class="content-contact">
    <div class="main-content">
      <div class="body">
        <!-- Thong tin lien he -->
        <div class="thongtinlienhe">
          <h1>Thông tin liên hệ</h1>
          <div class="box-thongtin reveal reveal-left">
            <span class="material-symbols-outlined"> location_on </span>
            <div class="desc">
              <h3>Địa chỉ</h3>
              <p>Đồng Tháp, Việt Nam</p>
            </div>
          </div>
          <div class="box-thongtin reveal reveal-left">
            <span class="material-symbols-outlined"> mail </span>
            <div class="desc">
              <h3>Email</h3>
              <p>suport@gmail.com</p>
            </div>
          </div>
          <div class="box-thongtin reveal reveal-left">
            <span class="material-symbols-outlined"> phone_enabled </span>
            <div class="desc">
              <h3>Số điện thoại</h3>
              <p>123-456-789</p>
            </div>
          </div>
          <div class="box-thongtin reveal reveal-left">
            <span class="material-symbols-outlined"> shoppingmode </span>
            <div class="desc">
              <h3>Thời gian làm việc</h3>
              <p>Từ 9h-22h tất cả các ngày trong tuần</p>
            </div>
          </div>
        </div>
        <!-- form -->
        <div class="formthacmac reveal reveal-right">
          <h1>Gửi thắc mắc cho chúng tôi</h1>
          <p>
            Nếu bạn có thắc mắc gì, có thể gửi yêu cầu cho chúng tôi, và
            chúng tôi sẽ liên lạc lại với bạn sớm nhất có thể.
          </p>

          <form class="contact-form" action="{{ route('contact.store') }}" method="post" onsubmit="return kiemtralienhe()">
            @csrf
            <input type="text" id="TenKhachHang" name="full_name" value="{{ old('full_name', auth()->user()?->full_name) }}" placeholder="Tên của bạn" />

            <div class="row">
              <input type="text" id="Email" name="email" value="{{ old('email', auth()->user()?->email) }}" placeholder="Email của bạn" />
              <input type="text" id="SDT" name="phone" value="{{ old('phone', auth()->user()?->phone) }}" placeholder="Số điện thoại của bạn" />
            </div>

            <textarea id="NoiDung" name="content" placeholder="Nội dung">{{ old('content') }}</textarea>

            <button type="submit">GỬI CHO CHÚNG TÔI</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- uu dai -->
  <div class="bennefit">
    <div class="main-content">
      <div class="body">
        @include('partials.newsletter')
      </div>
    </div>
  </div>
</div>
@endsection