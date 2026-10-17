@extends('layouts.app')

@section('title', 'Cửa hàng – Petio')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/shop.css') }}" />
@endpush

@section('content')
    <main class="main">
      <!-- ================= HERO ================= -->
      <section class="shop-hero">
        <div class="container">
          <div class="shop-hero-grid">
            <div class="shop-hero-text reveal reveal-left">
              <h1>Cửa hàng</h1>
              <p>Mọi thứ bé cưng của bạn cần, gom về một nơi.</p>
            </div>

            
          </div>

          <div class="shop-cate reveal-stagger">
            <a href="{{ route('shop', ['cat' => 'thuc-an']) }}" class="shop-cate-item">
              <img src="{{ asset('assets') }}/file_anh/cat-food.jpg" alt="Đồ ăn cho mèo" loading="lazy" />
              <h3>Đồ ăn cho mèo</h3>
            </a>
            <a href="{{ route('shop', ['cat' => 'do-choi']) }}" class="shop-cate-item">
              <img src="{{ asset('assets') }}/file_anh/cat-toy.jpg" alt="Đồ chơi cho mèo" loading="lazy" />
              <h3>Đồ chơi cho mèo</h3>
            </a>
            <a href="{{ route('shop', ['cat' => 'phu-kien']) }}" class="shop-cate-item">
              <img src="{{ asset('assets') }}/file_anh/dog-clothing.jpg" alt="Quần áo cho chó" loading="lazy" />
              <h3>Quần áo cho chó</h3>
            </a>
            <a href="{{ route('shop', ['cat' => 'thuoc']) }}" class="shop-cate-item">
              <img src="{{ asset('assets') }}/file_anh/cate-9.jpg" alt="Thực phẩm chức năng" loading="lazy" />
              <h3>Thực phẩm chức năng</h3>
            </a>
          </div>
        </div>
      </section>

      <!-- ================= DANH SÁCH SẢN PHẨM ================= -->
      <section class="section shop-section">
        <div class="container">
          <!-- nút mở bộ lọc, chỉ hiện trên mobile -->
          <button type="button" class="filter-toggle" id="filter-toggle">
            <span class="material-symbols-outlined">tune</span>
            Bộ lọc sản phẩm
          </button>

          <div class="shop-layout">
            <!-- ---------- BỘ LỌC ---------- -->
            <aside class="sidebar" id="sidebar">
              <div class="sidebar-head">
                <h2>Bộ lọc</h2>
                <button type="button" class="sidebar-close" id="sidebar-close" aria-label="Đóng bộ lọc">
                  <span class="material-symbols-outlined">close</span>
                </button>
              </div>

              <!-- tìm kiếm -->
              <div class="filter-group">
                <h3>Tìm kiếm</h3>
                <div class="filter-search">
                  <span class="material-symbols-outlined">search</span>
                  <input
                    type="search"
                    id="filter-search"
                    placeholder="Nhập tên sản phẩm..."
                    aria-label="Tìm sản phẩm theo tên"
                  />
                </div>
              </div>

              <!-- danh mục -->
              <div class="filter-group">
                <h3>Danh mục</h3>
                <ul class="filter-list" id="filter-cat">
                  <li>
                    <label class="filter-radio">
                      <input type="radio" name="cat" value="all" checked />
                      <span>Tất cả</span>
                    </label>
                  </li>
                  <li>
                    <label class="filter-radio">
                      <input type="radio" name="cat" value="thuc-an" />
                      <span>Thức ăn</span>
                    </label>
                  </li>
                  <li>
                    <label class="filter-radio">
                      <input type="radio" name="cat" value="do-choi" />
                      <span>Đồ chơi</span>
                    </label>
                  </li>
                  <li>
                    <label class="filter-radio">
                      <input type="radio" name="cat" value="phu-kien" />
                      <span>Phụ kiện, chuồng, nệm</span>
                    </label>
                  </li>
                  <li>
                    <label class="filter-radio">
                      <input type="radio" name="cat" value="ve-sinh" />
                      <span>Vệ sinh - chăm sóc</span>
                    </label>
                  </li>
                  <li>
                    <label class="filter-radio">
                      <input type="radio" name="cat" value="thuoc" />
                      <span>Thuốc & TPCN</span>
                    </label>
                  </li>
                </ul>
              </div>

              <!-- thú cưng -->
              <div class="filter-group">
                <h3>Dành cho</h3>
                <ul class="filter-list" id="filter-pet">
                  <li>
                    <label class="filter-radio">
                      <input type="radio" name="pet" value="all" checked />
                      <span>Tất cả</span>
                    </label>
                  </li>
                  <li>
                    <label class="filter-radio">
                      <input type="radio" name="pet" value="cho" />
                      <span>Cho chó</span>
                    </label>
                  </li>
                  <li>
                    <label class="filter-radio">
                      <input type="radio" name="pet" value="meo" />
                      <span>Cho mèo</span>
                    </label>
                  </li>
                </ul>
              </div>

              <!-- khoảng giá -->
              <div class="filter-group">
                <h3>Giá tối đa</h3>
                <input
                  type="range"
                  id="filter-price"
                  class="price-range"
                  min="0"
                  max="{{ $maxPrice }}"
                  step="10000"
                  value="{{ $maxPrice }}"
                  aria-label="Lọc theo giá tối đa"
                />
                <p class="price-display">
                  Từ 0đ đến <strong id="price-max-label">{{ vnd($maxPrice) }}</strong>
                </p>
              </div>

              <button type="button" class="btn btn-outline btn-block" id="filter-reset">
                Xóa bộ lọc
              </button>

              <!-- nhãn hiệu -->
              <div class="filter-group">
                <h3>Nhãn hiệu nổi tiếng</h3>
                <div class="brand-mini">
                  <img src="{{ asset('assets') }}/file_anh/brand-1.jpg" alt="Thương hiệu 1" loading="lazy" />
                  <img src="{{ asset('assets') }}/file_anh/brand-2.jpg" alt="Thương hiệu 2" loading="lazy" />
                  <img src="{{ asset('assets') }}/file_anh/brand-3.jpg" alt="Thương hiệu 3" loading="lazy" />
                  <img src="{{ asset('assets') }}/file_anh/brand-4.jpg" alt="Thương hiệu 4" loading="lazy" />
                  <img src="{{ asset('assets') }}/file_anh/brand-5.jpg" alt="Thương hiệu 5" loading="lazy" />
                </div>
              </div>
            </aside>

            <!-- lớp phủ khi mở bộ lọc trên mobile -->
            <div class="sidebar-overlay" id="sidebar-overlay" hidden></div>

            <!-- ---------- LƯỚI SẢN PHẨM ---------- -->
            <div class="shop-main">
              <div class="shop-bar">
                <p class="shop-count">
                  Hiển thị <strong id="result-count">{{ $products->count() }}</strong> /
                  {{ $products->count() }} sản phẩm
                </p>

                <label class="shop-sort">
                  <span>Sắp xếp:</span>
                  <select id="sort-by">
                    <option value="default">Mặc định</option>
                    <option value="price-asc">Giá: thấp đến cao</option>
                    <option value="price-desc">Giá: cao đến thấp</option>
                    <option value="name-asc">Tên: A → Z</option>
                  </select>
                </label>
              </div>

              <div class="product-grid" id="product-grid">
                @foreach ($products as $product)
                  <x-product-card :product="$product" />
                @endforeach
              </div>

              <!-- hiện khi không có sản phẩm nào khớp bộ lọc -->
              <div class="no-result" id="no-result" hidden>
                <span class="material-symbols-outlined">search_off</span>
                <h3>Không tìm thấy sản phẩm nào</h3>
                <p>Thử đổi từ khóa hoặc xóa bớt bộ lọc nhé.</p>
                <button type="button" class="btn btn-primary" id="no-result-reset">
                  Xóa bộ lọc
                </button>
              </div>
            </div>
          </div>
        </div>
      </section>
    </main>
@endsection

@push('scripts')
  <script src="{{ asset('assets/js/shop.js') }}" defer></script>
@endpush
