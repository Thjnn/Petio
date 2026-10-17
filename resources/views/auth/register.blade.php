@extends('layouts.app')

@section('title', 'Đăng ký – Petio')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}" />
@endpush

@section('content')
    <div class="hero">
      <div class="main-content">
        <div class="body">
          <div class="container">
            <div class="formdangnhap">
              <h1>Đăng Ký</h1>
              <form action="{{ route('register') }}" method="post" onsubmit="return kiemtraDK()">
                @csrf
                <input type="text" id="username" name="login" value="{{ old('login') }}" placeholder="Email/Số điện thoại/Tên đăng nhập" autocomplete="username" />
                <input type="password" id="password" name="password" placeholder="Mật khẩu" autocomplete="new-password" />
                <input type="password" id="re_password" name="password_confirmation" placeholder="Nhập lại mật khẩu" autocomplete="new-password" />
                <br />
                <button type="submit" class="btndangnhap">ĐĂNG KÝ</button>
                <br />

                <div class="separator">
                  <span>HOẶC</span>
                </div>

                <div class="applienket">
                  <div class="applienket-items">
                    <a href="https://www.facebook.com/">
                      <img src="{{ asset('assets/file_anh/fb-removebg-preview.png') }}" alt="" class="app-logo" />
                      <p>Facebook</p>
                    </a>
                  </div>
                  <div class="applienket-items">
                    <a href="https://www.google.com/">
                      <img src="{{ asset('assets/file_anh/Google__G__logo.svg.png') }}" alt="" class="app-logo" />
                      <p>Google</p>
                    </a>
                  </div>
                </div>
                <div class="quadangky">
                  <p>Bạn đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></p>
                </div>
              </form>
            </div>
            <div class="banner">
              <img src="{{ asset('assets/file_anh/66c195336114a71ec84efba43747131d-removebg-preview.png') }}" alt="" />
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection
