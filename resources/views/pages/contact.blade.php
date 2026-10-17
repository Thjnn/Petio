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
              <div class="banner-img">
                <img
                  src="{{ asset('assets') }}/file_anh/Screenshot 2025-12-21 202548.png"
                  alt=""
                />
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
