@extends('layouts.app')
@section('title', 'Tài khoản của tôi – Petio')
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/customer.css') }}?v=2">
<link rel="stylesheet" href="{{ asset('assets/css/customer-refresh.css') }}?v=2">
@endpush
@push('scripts')<script src="{{ asset('assets/js/customer.js') }}?v=2" defer></script>@endpush
@section('content')
@php
 $u=auth()->user();
 $menu=[['dashboard','space_dashboard','Góc của sen'],['profile','person','Hồ sơ'],['orders','inventory_2','Đơn hàng'],['favorites','favorite','Yêu thích'],['notifications','notifications','Thông báo'],['coupons','confirmation_number','Mã giảm giá'],['addresses','location_on','Địa chỉ'],['privacy','verified_user','Chính sách bảo mật'],['support','support_agent','Hỗ trợ'],['about','pets','Về chúng tôi']];
@endphp
<main class="pc pc-refresh">
 <div class="pc-wrap">
  <div class="pc-topline">
   <nav class="pc-breadcrumb" aria-label="Đường dẫn"><a href="{{ route('home') }}">Trang chủ</a><span>/</span><a href="{{ route('customer.dashboard') }}">Góc của sen</a>@unless(request()->routeIs('customer.dashboard'))<span>/</span><span>@yield('page-title')</span>@endunless</nav>
   <a class="pc-text-link" href="{{ route('shop') }}">Tiếp tục mua sắm ↗</a>
  </div>
  @unless(request()->routeIs('customer.dashboard'))
  <div class="pc-toolbar">
   <a class="pc-back" href="{{ route('customer.dashboard') }}"><span class="material-symbols-outlined">arrow_back</span> Các mục tài khoản</a>
   <details class="pc-menu"><summary>Chuyển mục <span class="material-symbols-outlined">apps</span></summary><nav aria-label="Menu tài khoản">@foreach($menu as [$key,$icon,$label])<a href="{{ route('customer.'.$key) }}" @if(request()->routeIs('customer.'.$key)) aria-current="page" @endif><span class="material-symbols-outlined">{{ $icon }}</span>{{ $label }}</a>@endforeach</nav></details>
  </div>
  @endunless
  <section class="pc-content">
   @if(session('success'))<div class="pc-alert" role="status">{{ session('success') }}</div>@endif
   @if($errors->any())<div class="pc-alert pc-error" role="alert"><strong>Vui lòng kiểm tra lại:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
   @yield('account-content')
  </section>
 </div>
</main>
@endsection
