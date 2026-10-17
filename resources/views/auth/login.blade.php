@extends('layouts.app')

@section('title', 'Đăng nhập – Petio')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/auth.css') }}" />
@endpush

@section('content')
    <div class="hero">
      <div class="main-content">
        <div class="body">
          <div class="container">
            <div class="formdangnhap">
              <h1>Đăng nhập</h1>
              <form action="{{ route('login') }}" method="post" onsubmit="return kiemtraDN()">
                @csrf
                <input type="text" id="username" name="login" value="{{ old('login') }}" placeholder="Email/Số điện thoại/Tên đăng nhập" autocomplete="username" />
                <br />
                <input type="password" id="password" name="password" placeholder="Mật khẩu" autocomplete="current-password" />
                <br />
                <label class="remember">
                  <input type="checkbox" name="remember" value="1" /> Ghi nhớ đăng nhập
                </label>
                <button type="submit" class="btndangnhap">ĐĂNG NHẬP</button>
                <br />
                <a href="{{ route('contact') }}">Quên mật khẩu? Liên hệ hỗ trợ</a> <br />
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
                  <p>Bạn mới biết đến Petio? <a href="{{ route('register') }}">Đăng ký</a></p>
                </div>
              </form>
            </div>
            <div class="banner">
              <img src="{{ asset('assets/file_anh/snapedit_1764834735361.jpg') }}" alt="" />
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection
