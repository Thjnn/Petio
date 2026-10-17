@extends('layouts.app')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" />
@endpush

@section('content')
<main class="main">
  <!-- ================= HERO ================= -->
  @if ($hero)
    <section class="hero">
      <div class="hero-banner">
        <img src="{{ asset($hero->image_url) }}" alt="{{ $hero->alt_text }}" fetchpriority="high" />
      </div>
      <div class="container">
        <div class="hero-summary">
          <h1>
            @foreach ($hero->titleLines() as $line)
              <span class="hero-line-{{ $loop->iteration }}">{{ $line }}</span>
            @endforeach
          </h1>
          <p>{{ $hero->subtitle }}</p>
          @if ($hero->button_text)
            <a href="{{ url($hero->link_url ?? 'shop') }}" class="btn btn-primary buynow">{{ $hero->button_text }}</a>
          @endif
        </div>
      </div>
    </section>
  @endif

  <!-- ================= DANH MỤC HÀNG ĐẦU ================= -->
  <section class="section top-categories">
    <div class="container">
      <h2 class="section-title reveal"><span class="hl">Danh mục</span> hàng đầu</h2>

      <div class="categories-grid reveal-stagger">
        @foreach ($categoryBanners as $banner)
          <a href="{{ url($banner->link_url ?? 'shop') }}" class="category-card">
            <div class="category-img">
              <img src="{{ asset($banner->image_url) }}" alt="{{ $banner->alt_text }}" loading="lazy" />
            </div>
            <h3>{{ $banner->title }}</h3>
          </a>
        @endforeach
      </div>
    </div>
  </section>

  <!-- ================= BANNER ƯU ĐÃI ================= -->
  <section class="promo-banner">
    <div class="container">
      <div class="promo-grid">
        @foreach ($promoBanners as $banner)
          <article class="promo-card reveal {{ $loop->index ? 'reveal-d'.$loop->index : '' }}">
            <img src="{{ asset($banner->image_url) }}" alt="{{ $banner->alt_text }}" loading="lazy" />
            <div class="promo-summary">
              <p>{{ $banner->subtitle }}</p>
              <h3>{!! collect($banner->titleLines())->map(fn ($l) => e($l))->join('<br />') !!}</h3>
              @if ($banner->button_text)
                <a href="{{ url($banner->link_url ?? 'shop') }}" class="btn btn-primary">{{ $banner->button_text }}</a>
              @endif
            </div>
          </article>
        @endforeach
      </div>
    </div>
  </section>

  <!-- ================= MẶT HÀNG ĐƯỢC YÊU THÍCH ================= -->
  <section class="section best-seller">
    <div class="container">
      <h2 class="section-title reveal"><span class="hl">Mặt hàng</span> được yêu thích nhất</h2>

      <div class="product-grid">
        @foreach ($featured as $product)
          <x-product-card :product="$product" :delay="$loop->index % 4" />
        @endforeach
      </div>
    </div>
  </section>

  <!-- ================= GÓI CHĂM SÓC ================= -->
  <section class="vital-care">
    <div class="container">
      <div class="vital-care-grid">
        <div class="vital-left reveal reveal-left">
          <img src="{{ asset('assets/file_anh/logo-banner.png') }}" alt="Petio Vital Care" loading="lazy" />
          <p>
            Tiết kiệm cực lớn mỗi năm cùng gói chăm sóc sức khỏe thú cưng
            được chuyên gia thú y khuyên dùng!
          </p>
          <a href="{{ route('spa') }}" class="btn btn-light">Tham gia ngay</a>
        </div>

        <div class="vital-right reveal reveal-right">
          <img src="{{ asset('assets/file_anh/banner-4.jpg') }}" alt="Chăm sóc thú cưng" loading="lazy" />
        </div>
      </div>
    </div>
  </section>

  <!-- ================= LỢI ÍCH ================= -->
  <section class="section benefits-section">
    <div class="container">
      <div class="benefit-head reveal">
        <img src="{{ asset('assets/file_anh/icon-1.png') }}" alt="" class="benefit-icon" loading="lazy" />
        <h2 class="section-title"><span class="hl">Thú cưng cần gì,</span> chúng tôi có ngay!</h2>
      </div>

      <div class="benefits-grid reveal-stagger">
        <div class="benefit-item">
          <span class="material-symbols-outlined icon-benefits">delivery_truck_speed</span>
          <h3>Giao hàng trong ngày</h3>
          <p>Đặt hàng trước 2 giờ chiều hôm nay để được giao miễn phí cho đơn từ 500.000đ</p>
        </div>

        <div class="benefit-item">
          <span class="material-symbols-outlined icon-benefits">package</span>
          <h3>Hoàn trả trong 30 ngày</h3>
          <p>Giảm 35% cho đơn đầu tiên với mã FIRST35!</p>
        </div>

        <div class="benefit-item">
          <span class="material-symbols-outlined icon-benefits">shield_person</span>
          <h3>Bảo mật thanh toán</h3>
          <p>Giảm ngay 25% cho đơn hàng trực tuyến từ 1 triệu trở lên với mã BIG25!</p>
        </div>

        <div class="benefit-item">
          <span class="material-symbols-outlined icon-benefits">support_agent</span>
          <h3>Hỗ trợ 24/7</h3>
          <p>Mua sắm online — đơn hàng được giao nhanh chóng, hỗ trợ bất cứ lúc nào!</p>
        </div>
      </div>
    </div>
  </section>

  <!-- ================= ĐẢM BẢO ĐỔI MỚI ================= -->
  <section class="feature-section">
    <div class="feature-banner">
      <img src="{{ asset('assets/file_anh/demo_img_1.png') }}" alt="Bữa ăn cho thú cưng" loading="lazy" />
    </div>

    <div class="container">
      <div class="feature-summary reveal reveal-right">
        <img src="{{ asset('assets/file_anh/icon-bat-an.png') }}" alt="" class="feature-icon" loading="lazy" />
        <h2>Thử Ngay, Mê Say, Đảm Bảo Đổi Mới!</h2>
        <p>Tại Petio, chúng tôi tự tin rằng người bạn bốn chân của bạn sẽ cực kỳ yêu thích bữa ăn này!</p>
        <p>Nếu thú cưng của bạn không hài lòng...</p>
        <p>Chúng tôi sẵn sàng hỗ trợ bạn tìm kiếm một sản phẩm thay thế hoàn hảo.</p>
        <a href="{{ route('shop') }}" class="btn btn-light">Tham gia ngay</a>
      </div>
    </div>
  </section>

  <!-- ================= GỢI Ý CHO BẠN ================= -->
  <section class="section recommended">
    <div class="container">
      <h2 class="section-title reveal"><span class="hl">Gợi ý</span> cho bạn!</h2>

      <div class="product-grid">
        @foreach ($recommended as $product)
          <x-product-card :product="$product" :delay="$loop->index" learn-more />
        @endforeach
      </div>
    </div>
  </section>

  <!-- ================= MUỐN MỘT BÉ THÚ CƯNG MỚI ================= -->
  <section class="adopt">
    <div class="container">
      <div class="adopt-grid">
        <div class="adopt-img adopt-img--left">
          <img src="{{ asset('assets/file_anh/images-chotrang-removebg-preview.png') }}" alt="Chó trắng" loading="lazy" />
        </div>

        <div class="adopt-center reveal">
          <h2>MUỐN MỘT BÉ THÚ CƯNG MỚI?</h2>
          <p>Khám phá mọi thứ bạn cần để<br />bé con cảm thấy như ở nhà</p>
          <div class="adopt-actions">
            <a href="{{ route('shop', ['cat' => 'cho']) }}" class="btn btn-light">Mua Bé Cún</a>
            <a href="{{ route('shop', ['cat' => 'meo']) }}" class="btn btn-light">Mua Bé Mèo</a>
          </div>
        </div>

        <div class="adopt-img adopt-img--right">
          <img src="{{ asset('assets/file_anh/dca621417c247ce20a7186c2d658b977-removebg-preview.png') }}" alt="Mèo" loading="lazy" />
        </div>
      </div>
    </div>
  </section>

  <!-- ================= THƯƠNG HIỆU NỔI TIẾNG ================= -->
  <section class="section popular">
    <div class="container">
      <h2 class="section-title reveal"><span class="hl">Các thương hiệu</span> nổi tiếng</h2>

      <div class="brand-grid reveal-stagger">
        @for ($i = 1; $i <= 5; $i++)
          <div class="brand-item">
            <img src="{{ asset("assets/file_anh/brand-{$i}.jpg") }}" alt="Thương hiệu {{ $i }}" loading="lazy" />
          </div>
        @endfor
      </div>
    </div>
  </section>
</main>
@endsection
