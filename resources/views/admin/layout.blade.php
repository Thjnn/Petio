<!doctype html>
<html lang="vi">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>@yield('title', 'Tổng quan') · Quản trị Petio</title>
    <link rel="stylesheet" href="{{ asset('assets/css/reset.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/base.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/admin.css') }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="shortcut icon" href="{{ asset('assets/file_anh/icon-web.png') }}" />
  </head>
  <body class="admin">
    @php
      $menu = [
          ['admin.dashboard', 'dashboard', 'Tổng quan', 'admin.dashboard'],
          ['admin.orders.index', 'receipt_long', 'Đơn hàng', 'admin.orders.*'],
          ['admin.products.index', 'inventory_2', 'Sản phẩm', 'admin.products.*'],
          ['admin.categories.index', 'category', 'Danh mục', 'admin.categories.*'],
          ['admin.coupons.index', 'sell', 'Mã giảm giá', 'admin.coupons.*'],
          ['admin.posts.index', 'article', 'Bài viết', 'admin.posts.*'],
          ['admin.banners.index', 'image', 'Banner', 'admin.banners.*'],
          ['admin.spa.index', 'spa', 'Dịch vụ spa', 'admin.spa.*'],
          ['admin.faqs.index', 'help', 'FAQ', 'admin.faqs.*'],
          ['admin.users.index', 'group', 'Tài khoản', 'admin.users.*'],
          ['admin.contacts.index', 'mail', 'Liên hệ', 'admin.contacts.*'],
          ['admin.newsletter.index', 'campaign', 'Bản tin', 'admin.newsletter.*'],
      ];
    @endphp

    <aside class="admin-sidebar">
      <a href="{{ route('admin.dashboard') }}" class="admin-brand">
        <img src="{{ asset('assets/file_anh/icon-web.png') }}" alt="" width="32" height="32" />
        <span>Petio Admin</span>
      </a>
      <nav class="admin-nav" aria-label="Menu quản trị">
        @foreach ($menu as [$route, $icon, $label, $pattern])
          <a href="{{ route($route) }}" @class(['active' => request()->routeIs($pattern)])>
            <span class="material-symbols-outlined" aria-hidden="true">{{ $icon }}</span>{{ $label }}
          </a>
        @endforeach
      </nav>
      <div class="admin-account">
        <a href="{{ route('home') }}" target="_blank">
          <span class="material-symbols-outlined" aria-hidden="true">open_in_new</span>Xem website
        </a>
        <form action="{{ route('logout') }}" method="post">
          @csrf
          <button type="submit">
            <span class="material-symbols-outlined" aria-hidden="true">logout</span>Đăng xuất ({{ auth()->user()->displayName() }})
          </button>
        </form>
      </div>
    </aside>

    <main class="admin-main">
      <header class="admin-head">
        <h1>@yield('title', 'Tổng quan')</h1>
        <div class="admin-head-actions">@yield('actions')</div>
      </header>

      @if (session('success'))
        <div class="flash flash-success" role="status">{{ session('success') }}</div>
      @endif
      @if ($errors->any())
        <div class="flash flash-error" role="alert">
          <strong>Chưa lưu được:</strong>
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </main>

    @stack('scripts')
  </body>
</html>
