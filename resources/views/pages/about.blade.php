@extends('layouts.app')

@section('title', 'Về chúng tôi – Petio')

@push('styles')
  <link rel="stylesheet" href="{{ asset('assets/css/aboutus.css') }}" />
@endpush

@section('content')
    <div class="main">
      <!-- hero -->
      <div class="hero">
        <div class="main-content">
          <div class="body">
            <div class="hero-banner reveal reveal-left">
              <img src="{{ asset('assets') }}/file_anh/snapedit_1766414339457.jpg" alt="" />
            </div>
            <div class="hero-sumary reveal reveal-right">
              <h1>Về Petio</h1>
              <p>
                Petio là nền tảng dành cho những người yêu thú cưng, nơi cung
                cấp sản phẩm – dịch vụ – kiến thức giúp thú cưng khỏe mạnh, hạnh
                phúc và được chăm sóc tốt nhất. Chúng tôi tin rằng thú cưng
                không chỉ là vật nuôi, mà là người bạn trong gia đình.
              </p>
            </div>
          </div>
        </div>
      </div>
      <!-- gia tri -->
      <div class="dactrung">
        <div class="title reveal">
          <h1>Giá trị của <span style="color: #ed6436">Petio</span></h1>
        </div>
        <div class="main-content">
          <div class="body">
            <div class="dactrung-img reveal reveal-left">
              <img
                src="{{ asset('assets') }}/file_anh/a087d3c03b7cc5da58280314e5c0d0f5.png"
                alt=""
              />
            </div>
            <div class="dactrung-sumary reveal reveal-right">
              <p>
                <span class="material-symbols-outlined"> pet_supplies </span
                >Mang đến sản phẩm chất lượng, an toàn cho chó mèo
              </p>
              <p>
                <span class="material-symbols-outlined"> import_contacts </span
                >Chia sẻ kiến thức chăm sóc thú cưng khoa học, dễ hiểu
              </p>
              <p>
                <span class="material-symbols-outlined"> diversity_3 </span>Kết
                nối cộng đồng những người yêu thú cưng
              </p>
            </div>
          </div>
        </div>
      </div>
      <!-- khong gian cua hang -->
      <div class="cuahang">
        <div class="main-content">
          <div class="body">
            <div class="title reveal">
              <h1>Không gian <span style="color: #ed6436">cửa hàng</span></h1>
            </div>
            <div class="khung-anh reveal-stagger">
              <div class="cuahang-img">
                <img
                  src="{{ asset('assets') }}/file_anh/2a5d79a1272bb14611d34f4d548d3309.jpg"
                  alt=""
                />
              </div>
              <div class="cuahang-img">
                <img
                  src="{{ asset('assets') }}/file_anh/e31190096b08e2ed1e2edd5734125f24.jpg"
                  alt=""
                />
              </div>
              <div class="cuahang-img">
                <img
                  src="{{ asset('assets') }}/file_anh/8e0c7c3346a91757bee52bff4227d4c3.jpg"
                  alt=""
                />
              </div>
            </div>
            <div class="khung-anh-2 reveal-stagger">
              <div class="cuahang-img">
                <img
                  src="{{ asset('assets') }}/file_anh/725ef2d4a28312498450034e1d4056c9.jpg"
                  alt=""
                />
              </div>
              <div class="cuahang-img">
                <img
                  src="{{ asset('assets') }}/file_anh/65debe00366541a475d514f0fc34f6b3.jpg"
                  alt=""
                />
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- feedback -->
      <div class="phanhoi">
        <div class="main-content">
          <div class="body">
            <div class="feedback-title reveal">
              <h1>
                Phản hồi từ <span style="color: #ed6436">khách hàng</span>
              </h1>
            </div>

            <div class="feedback-content reveal-stagger">
              <div class="feedback-card">
                <div class="feedback-img">
                  <img
                    src="{{ asset('assets') }}/file_anh/ec14dd664f7802084a3dfcf4ed7dc6a9.jpg"
                    alt=""
                  />
                </div>
                <div class="rating">
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                </div>
                <div class="desc">
                  <p>
                    Cửa hàng Petio rất đẹp, sản phẩm đa dạng và chất lượng tốt.
                    Nhân viên tư vấn rất nhiệt tình!
                  </p>
                </div>
              </div>
              <div class="feedback-card">
                <div class="feedback-img">
                  <img
                    src="{{ asset('assets') }}/file_anh/efc91ed4b0c3d0c7f432c717b755a650.jpg"
                    alt=""
                  />
                </div>
                <div class="rating">
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                </div>
                <div class="desc">
                  <p>
                    Cửa hàng Petio rất sạch sẽ. Mình đặc biệt ấn tượng với khu
                    trưng bày sản phẩm rõ ràng, dễ tìm.
                  </p>
                </div>
              </div>
              <div class="feedback-card">
                <div class="feedback-img">
                  <img
                    src="{{ asset('assets') }}/file_anh/5d2fc91cd02dfd072746ddfa132fb85a.jpg"
                    alt=""
                  />
                </div>
                <div class="rating">
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                </div>
                <div class="desc">
                  <p>
                    Không gian cửa hàng rất thoáng, có khu thử đồ chơi cho thú
                    cưng. Mèo mình quên lối về vì thích quá! 😄 Dịch vụ khách
                    hàng rất chuyên nghiệp
                  </p>
                </div>
              </div>
              <div class="feedback-card">
                <div class="feedback-img">
                  <img
                    src="{{ asset('assets') }}/file_anh/bbfb5e9a95c99ef290dc9b81a5785f5d.jpg"
                    alt=""
                  />
                </div>
                <div class="rating">
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                  <span class="fa fa-star checked"></span>
                </div>
                <div class="desc">
                  <p>
                    Giá cả hợp lý, chất lượng tốt. Nhân viên còn hướng dẫn mình
                    cách chọn thức ăn phù hợp với cân nặng của cún. Mình sẽ quay
                    lại!
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- uu dai -->
      <div class="bennefit">
        <div class="body">
          <!-- trái -->
          <div class="body-left">
            <div class="icon-uudai">
              <span class="material-symbols-outlined" style="color: #ed6436">
                attach_email
              </span>
            </div>
            <p>
              Tham gia ngay và nhận ưu đãi giảm giá 10% <br />
              cho lần mua hàng tiếp theo!
            </p>
          </div>
          <!-- giữa -->
          <div class="body-center">
            <h3>
              Đăng ký nhận bản tin hàng tuần để cập nhật những thông tin mới
              nhất
            </h3>
          </div>
          <!-- phải -->
          <div class="body-right">
            <div class="subscribe-box">
              <input
                type="email"
                placeholder="Email Address"
                class="email-input"
              />
              <button type="submit" class="subscribe-btn">SUBSCRIBE</button>
            </div>
          </div>
        </div>
      </div>
    </div>
@endsection
