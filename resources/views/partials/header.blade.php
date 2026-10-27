<header class="header" id="site-header">
  <div class="header-inner">
    <!-- logo -->
    <a href="{{ route('home') }}" class="logo-link" aria-label="Petio - Trang chủ">
      <img src="{{ asset('assets/file_anh/logo-white.png') }}" alt="Petio" class="logo" />
    </a>

    <!-- menu chính (trên mobile biến thành drawer trượt từ phải) -->
    <nav class="nav" id="site-nav" aria-label="Menu chính">
      <!-- ô tìm kiếm này chỉ hiện bên trong drawer trên mobile -->
      <form class="search-box" action="{{ route('shop') }}" method="get" role="search">
        <input type="search" name="q" placeholder="Tìm kiếm sản phẩm..." aria-label="Tìm kiếm sản phẩm" />
        <button type="submit" class="search-btn" aria-label="Tìm kiếm">
          <span class="material-symbols-outlined">search</span>
        </button>
      </form>

      <ul class="nav-list">
        <li class="nav-item"><a href="{{ route('home') }}">Trang chủ</a></li>

        <li class="nav-item has-submenu">
          <a href="{{ route('shop') }}">Cửa hàng</a>
          <button type="button" class="submenu-toggle" aria-label="Mở menu con Cửa hàng" aria-expanded="false">
            <span class="material-symbols-outlined">expand_more</span>
          </button>
          <ul class="submenu">
            <li><a href="{{ route('shop', ['cat' => 'thuc-an']) }}">Thức ăn thú cưng</a></li>
            <li><a href="{{ route('shop', ['cat' => 'do-choi']) }}">Đồ chơi thú cưng</a></li>
            <li><a href="{{ route('shop', ['cat' => 'phu-kien']) }}">Phụ kiện thú cưng</a></li>
            <li><a href="{{ route('shop', ['cat' => 've-sinh']) }}">Vệ sinh - chăm sóc</a></li>
            <li><a href="{{ route('shop', ['cat' => 'thuoc']) }}">Thuốc và thực phẩm chức năng</a></li>
            <li><a href="{{ route('shop', ['cat' => 'meo']) }}">Mèo cảnh</a></li>
            <li><a href="{{ route('shop', ['cat' => 'cho']) }}">Chó cảnh</a></li>
          </ul>
        </li>

        <li class="nav-item"><a href="{{ route('spa') }}">Dịch vụ chăm sóc</a></li>
        <li class="nav-item"><a href="{{ route('blog.index') }}">Tin tức</a></li>

        <li class="nav-item has-submenu">
          <a href="{{ route('faq') }}">Trợ giúp</a>
          <button type="button" class="submenu-toggle" aria-label="Mở menu con Trợ giúp" aria-expanded="false">
            <span class="material-symbols-outlined">expand_more</span>
          </button>
          <ul class="submenu">
            <li><a href="{{ route('faq') }}">Câu hỏi thường gặp</a></li>
            <li><a href="{{ route('about') }}">Về chúng tôi</a></li>
            <li><a href="{{ route('contact') }}">Liên hệ</a></li>
          </ul>
        </li>
      </ul>

      @auth
        @can('admin')
          <a href="{{ route('admin.dashboard') }}" class="nav-account">Trang quản trị</a>
        @endcan
        <form action="{{ route('logout') }}" method="post">
          @csrf
          <button type="submit" class="nav-account">Đăng xuất ({{ auth()->user()->displayName() }})</button>
        </form>
      @else
        <a href="{{ route('login') }}" class="nav-account">Đăng nhập / Đăng ký</a>
      @endauth
    </nav>

    <!-- tìm kiếm / tài khoản / giỏ hàng -->
    <div class="action">
      <form class="search-box" action="{{ route('shop') }}" method="get" role="search">
        <input type="search" name="q" placeholder="Tìm kiếm..." aria-label="Tìm kiếm sản phẩm" />
        <button type="submit" class="search-btn" aria-label="Tìm kiếm">
          <span class="material-symbols-outlined">search</span>
        </button>
      </form>

      @auth
        @can('admin')
          <a href="{{ route('admin.dashboard') }}" class="action-btn action-account" aria-label="Trang quản trị" title="Trang quản trị">
            <span class="material-symbols-outlined">admin_panel_settings</span>
          </a>
        @endcan
        <form action="{{ route('logout') }}" method="post" class="action-account-form">
          @csrf
          <button type="submit" class="action-btn action-account" aria-label="Đăng xuất" title="Đăng xuất ({{ auth()->user()->displayName() }})">
            <span class="material-symbols-outlined">logout</span>
          </button>
        </form>
      @else
        <a href="{{ route('login') }}" class="action-btn action-account" aria-label="Tài khoản">
          <span class="material-symbols-outlined">person</span>
        </a>
      @endauth

      <button type="button" class="action-btn cart-btn" id="open-cart" aria-label="Mở giỏ hàng">
        <span class="material-symbols-outlined">local_mall</span>
        <span class="cart-badge" id="cart-badge" hidden>0</span>
      </button>

      <button type="button" class="nav-toggle" id="nav-toggle" aria-label="Mở menu" aria-expanded="false" aria-controls="site-nav">
        <span class="nav-toggle-bar"></span>
        <span class="nav-toggle-bar"></span>
        <span class="nav-toggle-bar"></span>
      </button>
    </div>
  </div>

  <!-- lớp phủ mờ phía sau drawer -->
  <div class="nav-overlay" id="nav-overlay" hidden></div>

  <!-- modal giỏ hàng -->
  <div class="modal" id="cart-modal" role="dialog" aria-modal="true" aria-label="Giỏ hàng">
    <div class="modal-content">
      <button type="button" class="modal-close" id="close-cart" aria-label="Đóng giỏ hàng">
        <span class="material-symbols-outlined">close</span>
      </button>

      <h2 class="modal-title">Giỏ hàng của bạn</h2>

      <!-- khi giỏ trống -->
      <div class="cart-empty" id="cart-empty">
        <span class="material-symbols-outlined icon">add_shopping_cart</span>
        <p>Chưa có sản phẩm nào trong giỏ.</p>
        <a href="{{ route('shop') }}" class="go-shop">ĐẾN CỬA HÀNG →</a>
      </div>

      <!-- danh sách sản phẩm, do cart.js vẽ ra -->
      <ul class="cart-items" id="cart-items" hidden></ul>

      <div class="cart-foot" id="cart-foot" hidden>
        <div class="cart-total">
          <span>Tổng cộng</span>
          <strong id="cart-total">0đ</strong>
        </div>
        <a href="{{ route('checkout.index') }}" class="btn btn-primary btn-block" id="cart-checkout">Thanh toán</a>
        <button type="button" class="cart-clear" id="cart-clear">Xóa toàn bộ giỏ hàng</button>
      </div>

      <p class="cart-free">MIỄN PHÍ VẬN CHUYỂN CHO ĐƠN HÀNG TỪ <strong>500.000đ</strong></p>
    </div>
  </div>
</header>
