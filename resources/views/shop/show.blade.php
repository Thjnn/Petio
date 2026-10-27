@extends('layouts.app')

@section('title', $product->name.' – Petio')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/product.css') }}" />
@endpush

@php
  $variant = $product->defaultVariant();
  $discount = $variant->discountPercent();
  $stars = (int) round($product->rating_avg);
  $category = $product->category;
@endphp

@section('content')
    <main class="main">
      <div class="container">
        <!-- đường dẫn -->
        <nav class="breadcrumb" aria-label="Đường dẫn">
          <a href="{{ route('home') }}">Trang chủ</a>
          <span class="material-symbols-outlined">chevron_right</span>
          <a href="{{ route('shop') }}">Cửa hàng</a>
          <span class="material-symbols-outlined">chevron_right</span>
          <span aria-current="page">{{ $product->name }}</span>
        </nav>

        <!-- ================= THÔNG TIN SẢN PHẨM ================= -->
        <section
          class="product-detail"
          data-product
          data-id="{{ $variant->id }}"
          data-name="{{ $product->name }}"
          data-price="{{ $variant->price }}"
        >
          <!-- ảnh -->
          <div class="product-gallery reveal reveal-left">
            <div class="gallery-main">
              <img src="{{ asset($product->thumbnail_url) }}" alt="{{ $product->name }}" fetchpriority="high" />
              <span class="badge badge-deal" data-discount @if (! $discount) hidden @endif>-{{ $discount }}%</span>
            </div>
          </div>

          <!-- thông tin -->
          <div class="product-info reveal reveal-right">
            <div class="rating">
              @for ($i = 1; $i <= 5; $i++)
                <span class="fa fa-star {{ $i <= $stars ? 'checked' : '' }}"></span>
              @endfor
              <span class="rating-count">({{ $product->rating_count }} đánh giá)</span>
            </div>

            <h1>{{ $product->name }}</h1>

            <p class="detail-price">
              <span class="price-old" data-old-price @if (! $discount) hidden @endif>{{ vnd($variant->old_price) }}</span>
              <span class="price-new" data-new-price>{{ vnd($variant->price) }}</span>
            </p>

            @if ($product->short_description)
              <p class="detail-desc">{{ $product->short_description }}</p>
            @endif

            <p class="detail-note">
              <strong>Lưu ý:</strong> Giá sản phẩm có thể thay đổi theo từng thời
              điểm. Kết bạn Zalo hoặc gọi hotline để xem thêm hình ảnh, video chi
              tiết.
            </p>

            <!-- chọn size: chỉ hiện khi có nhiều biến thể -->
            @if ($product->variants->count() > 1)
              <div class="variant-picker" role="radiogroup" aria-label="Chọn loại">
                @foreach ($product->variants as $v)
                  <label class="variant-option">
                    <input
                      type="radio" name="variant" value="{{ $v->id }}" @checked($loop->first)
                      data-price="{{ $v->price }}" data-price-text="{{ vnd($v->price) }}"
                      data-old-price-text="{{ $v->discountPercent() ? vnd($v->old_price) : '' }}"
                      data-discount="{{ $v->discountPercent() }}"
                    />
                    <span>{{ $v->name }}</span>
                  </label>
                @endforeach
              </div>
            @endif

            <!-- số lượng + nút mua -->
            <div class="product-actions">
              <div class="qty-selector">
                <button type="button" class="qty-btn" data-qty-dec aria-label="Giảm số lượng">−</button>
                <input type="number" class="qty-input" data-qty value="1" min="1" max="99" aria-label="Số lượng" />
                <button type="button" class="qty-btn" data-qty-inc aria-label="Tăng số lượng">+</button>
              </div>

              <button type="button" class="btn add-to-cart" data-add-to-cart>
                <span class="material-symbols-outlined">add_shopping_cart</span>
                Thêm vào giỏ
              </button>

              <button type="button" class="btn btn-primary" data-buy-now>Mua ngay</button>
            </div>

            <!-- yêu thích / chia sẻ -->
            <div class="product-extra">
              <button type="button" class="extra-btn" id="fav-btn">
                <span class="material-symbols-outlined">favorite</span>
                Yêu thích
              </button>
              <button type="button" class="extra-btn" id="share-btn">
                <span class="material-symbols-outlined">share</span>
                Chia sẻ
              </button>
            </div>

            <!-- thông tin thêm -->
            <ul class="detail-meta">
              <li>
                <strong>Danh mục:</strong>
                <a href="{{ route('shop', ['cat' => $category->root()->slug]) }}">{{ $category->root()->name }}</a>@if ($category->parent), {{ $category->name }}@endif
              </li>
              @if ($product->tags->isNotEmpty())
                <li><strong>Tag:</strong> {{ $product->tags->pluck('name')->join(', ') }}</li>
              @endif
              @if ($product->brand)
                <li><strong>Hãng:</strong> {{ $product->brand->name }}</li>
              @endif
            </ul>
          </div>
        </section>

        <!-- ================= MÔ TẢ CHI TIẾT (HTML do admin soạn) ================= -->
        @if ($product->description)
          <section class="product-description reveal">
            {!! $product->description !!}
          </section>
        @endif

        <!-- ================= SẢN PHẨM LIÊN QUAN ================= -->
        @if ($related->isNotEmpty())
          <section class="section related-products">
            <h2 class="section-title reveal"><span class="hl">Sản phẩm</span> liên quan</h2>

            <div class="product-grid">
              @foreach ($related as $item)
                <x-product-card :product="$item" :delay="$loop->index" />
              @endforeach
            </div>
          </section>
        @endif
      </div>
    </main>
<div class="container" style="margin:20px auto">
@auth @if(auth()->user()->role === 'customer')
<form method="post" action="{{ route('customer.favorites.store',$product) }}">@csrf<button class="btn btn-outline" type="submit">♡ Lưu sản phẩm yêu thích</button></form>
@endif @else <a href="{{ route('login') }}" class="btn btn-outline">Đăng nhập để lưu yêu thích</a> @endauth
</div>
@endsection

@push('scripts')
  <script>
    // Đổi size: cập nhật biến thể sẽ thêm vào giỏ + giá hiển thị
    document.addEventListener("change", function (e) {
      var radio = e.target.closest('.variant-picker input[name="variant"]');
      if (!radio) return;

      var detail = radio.closest("[data-product]");
      detail.dataset.id = radio.value;
      detail.dataset.price = radio.dataset.price;
      detail.querySelector("[data-new-price]").textContent = radio.dataset.priceText;

      var oldPrice = detail.querySelector("[data-old-price]");
      var badge = detail.querySelector("[data-discount]");
      oldPrice.textContent = radio.dataset.oldPriceText;
      oldPrice.hidden = !radio.dataset.oldPriceText;
      badge.textContent = "-" + radio.dataset.discount + "%";
      badge.hidden = !radio.dataset.discount;
    });
  </script>
@endpush
