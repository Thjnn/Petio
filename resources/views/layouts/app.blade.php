<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}" />
    <title>@yield('title', 'Petio')</title>
    <!-- reset css -->
    <link rel="stylesheet" href="{{ asset('assets/css/reset.css') }}" />
    <!-- nền tảng dùng chung: biến thiết kế, tiện ích, animation -->
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <!-- khung dùng chung: header, menu, giỏ hàng, footer -->
    <link rel="stylesheet" href="{{ asset('assets/css/layout.css') }}" />
    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Alfa+Slab+One&family=Nunito:ital,wght@0,200..1000;1,200..1000&family=Poppins:ital,wght@0,400;0,500;0,600;0,700;1,400&family=Sen:wght@700&display=swap"
      rel="stylesheet"
    />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link href="https://fonts.googleapis.com/css?family=Bangers" rel="stylesheet" />
    <!-- icon ngôi sao (fa 4) + icon khác (fa 6) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <!-- logo web site -->
    <link rel="shortcut icon" href="{{ asset('assets/file_anh/icon-web.png') }}" />

    @stack('styles')
  </head>
  <body>
    @include('partials.header')

    @yield('content')

    @include('partials.footer')

    <!-- nút lên đầu trang -->
    <button type="button" class="back-to-top" id="back-to-top" aria-label="Lên đầu trang">
      <i class="fa-solid fa-arrow-up"></i>
    </button>

    <!-- thông báo nổi (thêm vào giỏ thành công...) -->
    <div class="toast" id="toast" role="status" aria-live="polite"></div>

    <!-- hộp thông báo lỗi -->
    <div id="custom-alert" class="custom-alert" role="alertdialog" aria-modal="true">
      <div class="custom-alert-box">
        <p id="alert-message">Nội dung thông báo</p>
        <button type="button" class="btn btn-primary" onclick="closeAlert()">OK</button>
      </div>
    </div>

    <script>
      window.Petio = {
        csrf: @json(csrf_token()),
        routes: {
          cart: @json(route('cart.index')),
          cartAdd: @json(route('cart.add')),
          cartUpdate: @json(route('cart.update')),
          cartClear: @json(route('cart.clear')),
          checkout: @json(route('checkout.index')),
        },
        // Thông báo từ server sau khi chuyển trang (đăng nhập, gửi liên hệ...)
        flash: @json(session('success')),
        error: @json($errors->first() ?: null),
      };
    </script>
    <script src="{{ asset('assets/js/script.js') }}" defer></script>
    <script src="{{ asset('assets/js/main.js') }}" defer></script>
    <script src="{{ asset('assets/js/cart.js') }}" defer></script>
    @stack('scripts')
  </body>
</html>
