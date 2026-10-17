<footer class="footer">
  <div class="footer-inner">
    <div class="footer-desc">
      <!-- cột 1 -->
      <div class="column reveal">
        <img src="{{ asset('assets/file_anh/logo-white.png') }}" alt="Petio" class="footer-logo" />
        <p class="desc">Nếu bạn có câu hỏi gì? Hãy liên hệ ngay với chúng tôi.</p>
        <a href="mailto:suport@gmail.com" class="mail">suport@gmail.com</a>

        <div class="footer-element">
          <span class="material-symbols-outlined">location_on</span>
          <span>Đồng Tháp, Việt Nam</span>
        </div>

        <div class="footer-element">
          <span class="material-symbols-outlined">call</span>
          <span>(+84) 123-456-789</span>
        </div>
      </div>

      <!-- cột 2 -->
      <div class="column reveal reveal-d1">
        <h3 class="title">Công ty</h3>
        <ul class="list">
          <li><a href="{{ route('about') }}">Về chúng tôi</a></li>
          <li><a href="{{ route('about') }}">Đặc trưng</a></li>
          <li><a href="{{ route('about') }}">Giá trị của chúng tôi</a></li>
          <li><a href="{{ route('blog.index') }}">Tin mới nhất</a></li>
        </ul>
      </div>

      <!-- cột 3 -->
      <div class="column reveal reveal-d2">
        <h3 class="title">Hỗ trợ</h3>
        <ul class="list">
          <li><a href="{{ route('faq') }}">Câu hỏi thường gặp</a></li>
          <li><a href="{{ route('faq') }}">Điều khoản và điều kiện</a></li>
          <li><a href="{{ route('faq') }}">Chính sách bảo mật</a></li>
          <li><a href="{{ route('contact') }}">Kết nối với chúng tôi</a></li>
        </ul>
      </div>

      <!-- cột 4 -->
      <div class="column reveal reveal-d3">
        <h3 class="title">Liên hệ</h3>
        <ul class="list">
          <li><a href="{{ route('contact') }}"><strong>Địa chỉ:</strong> Đồng Tháp</a></li>
          <li><a href="{{ route('contact') }}"><strong>Email:</strong> suport@gmail.com</a></li>
          <li><a href="{{ route('contact') }}"><strong>Điện thoại:</strong> 123-456-789</a></li>
        </ul>
      </div>
    </div>

    <p class="footer-bottom">© 2026 Petio — Đồ án cuối kỳ, Trường Đại học Tiền Giang.</p>
  </div>
</footer>
