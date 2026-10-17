{{-- Khối "Đăng ký nhận bản tin" — đặt bên trong <div class="body"> của .bennefit --}}
<!-- trái -->
<div class="body-left">
  <div class="icon-uudai">
    <span class="material-symbols-outlined" style="color: #ed6436">attach_email</span>
  </div>
  <p>
    Tham gia ngay và nhận ưu đãi giảm giá 10% <br />
    cho lần mua hàng tiếp theo!
  </p>
</div>
<!-- giữa -->
<div class="body-center">
  <h3>Đăng ký nhận bản tin hàng tuần để cập nhật những thông tin mới nhất</h3>
</div>
<!-- phải -->
<div class="body-right">
  <form class="subscribe-box" action="{{ route('newsletter.store') }}" method="post">
    @csrf
    <input type="email" name="email" placeholder="Email Address" class="email-input" required aria-label="Email nhận bản tin" />
    <button type="submit" class="subscribe-btn">SUBSCRIBE</button>
  </form>
</div>
