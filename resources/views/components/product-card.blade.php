{{-- Thẻ sản phẩm dùng chung. Các data-* để cart.js thêm vào giỏ và shop.js lọc/sắp xếp. --}}
@props(['product', 'delay' => 0, 'learnMore' => false])
@php
  $variant = $product->defaultVariant();
  $discount = $variant->discountPercent();
  $stars = (int) round($product->rating_avg);
  $url = route('products.show', $product);
@endphp
<article
  class="card product-card reveal {{ $delay ? 'reveal-d'.$delay : '' }}"
  data-product
  data-id="{{ $variant->id }}"
  data-name="{{ $product->name }}"
  data-price="{{ $variant->price }}"
  data-cat="{{ $product->category->root()->slug }}"
  data-pet="{{ $product->petCode() }}"
>
  <div class="media">
    <a href="{{ $url }}">
      <img src="{{ asset($product->thumbnail_url) }}" alt="{{ $product->name }}" loading="lazy" />
    </a>
    @if ($discount)
      <span class="badge badge-deal">-{{ $discount }}%</span>
    @endif
    @if ($product->isHot())
      <span class="badge badge-hot">Hot</span>
    @endif
  </div>
  <div class="product-body">
    <div class="rating">
      @for ($i = 1; $i <= 5; $i++)
        <span class="fa fa-star {{ $i <= $stars ? 'checked' : '' }}"></span>
      @endfor
      <span class="rating-count">({{ $product->rating_count }})</span>
    </div>
    <h3 class="product-name">
      <a href="{{ $url }}">{{ $product->name }}</a>
    </h3>
    <p class="product-price">
      @if ($discount)
        <span class="price-old">{{ vnd($variant->old_price) }}</span>
      @endif
      <span class="price-new">{{ vnd($variant->price) }}</span>
    </p>
    @if ($learnMore)
      <a href="{{ $url }}" class="btn btn-outline">Tìm hiểu ngay</a>
    @else
      <button type="button" class="btn add-to-cart" data-add-to-cart>
        <span class="material-symbols-outlined">add_shopping_cart</span>
        Thêm vào giỏ
      </button>
    @endif
  </div>
</article>
