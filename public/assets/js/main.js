/* ==========================================================================
   main.js — Hành vi dùng chung cho cả 11 trang
   - Menu hamburger (drawer) + submenu trên mobile
   - Header co lại khi cuộn
   - Nút back-to-top
   - Scroll reveal: nội dung hiện dần khi cuộn tới
   - Đánh dấu mục menu của trang hiện tại
   Nạp ở cuối <body> với thuộc tính defer.
   ========================================================================== */
(function () {
  "use strict";

  /* Người dùng có bật "giảm chuyển động" trong hệ điều hành không? */
  var reduceMotion = window.matchMedia(
    "(prefers-reduced-motion: reduce)"
  ).matches;

  /* ------------------------------------------------------------------
     1. MENU HAMBURGER (drawer trượt từ phải, chỉ dùng dưới 768px)
     ------------------------------------------------------------------ */
  function initNav() {
    var toggle = document.getElementById("nav-toggle");
    var nav = document.getElementById("site-nav");
    var overlay = document.getElementById("nav-overlay");

    if (!toggle || !nav || !overlay) return;

    function openNav() {
      nav.classList.add("is-open");
      overlay.hidden = false;
      // Đợi 1 frame để trình duyệt kịp vẽ overlay rồi mới fade-in
      requestAnimationFrame(function () {
        overlay.classList.add("is-open");
      });
      toggle.setAttribute("aria-expanded", "true");
      toggle.setAttribute("aria-label", "Đóng menu");
      document.body.classList.add("no-scroll");
    }

    function closeNav() {
      nav.classList.remove("is-open");
      overlay.classList.remove("is-open");
      toggle.setAttribute("aria-expanded", "false");
      toggle.setAttribute("aria-label", "Mở menu");
      document.body.classList.remove("no-scroll");

      // Ẩn hẳn overlay sau khi fade xong để nó không chặn click
      window.setTimeout(function () {
        if (!nav.classList.contains("is-open")) overlay.hidden = true;
      }, reduceMotion ? 0 : 300);
    }

    toggle.addEventListener("click", function () {
      if (nav.classList.contains("is-open")) closeNav();
      else openNav();
    });

    overlay.addEventListener("click", closeNav);

    // Bấm Esc để đóng
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && nav.classList.contains("is-open")) closeNav();
    });

    // Bấm vào một link trong menu thì đóng drawer lại
    nav.querySelectorAll("a").forEach(function (link) {
      link.addEventListener("click", function () {
        if (nav.classList.contains("is-open")) closeNav();
      });
    });

    // Quay lại desktop khi drawer đang mở -> dọn trạng thái
    window.addEventListener("resize", function () {
      if (window.innerWidth > 768 && nav.classList.contains("is-open")) {
        closeNav();
      }
    });

    /* Submenu trên mobile: bấm mũi tên để mở/đóng.
       Trên desktop submenu vẫn mở bằng hover (thuần CSS). */
    nav.querySelectorAll(".submenu-toggle").forEach(function (btn) {
      btn.addEventListener("click", function (e) {
        e.preventDefault();
        var item = btn.closest(".nav-item");
        if (!item) return;

        // Đóng các submenu khác cho gọn
        nav.querySelectorAll(".nav-item.is-expanded").forEach(function (other) {
          if (other !== item) other.classList.remove("is-expanded");
        });

        item.classList.toggle("is-expanded");
        btn.setAttribute(
          "aria-expanded",
          item.classList.contains("is-expanded") ? "true" : "false"
        );
      });
    });
  }

  /* ------------------------------------------------------------------
     2. HEADER CO LẠI KHI CUỘN + NÚT BACK TO TOP
     Gộp chung vào một listener scroll để không chạy hai hàm song song.
     ------------------------------------------------------------------ */
  function initScrollEffects() {
    var header = document.getElementById("site-header");
    var backToTop = document.getElementById("back-to-top");
    var ticking = false;

    function update() {
      var y = window.scrollY || document.documentElement.scrollTop;

      if (header) header.classList.toggle("is-scrolled", y > 80);
      if (backToTop) backToTop.classList.toggle("is-visible", y > 300);

      ticking = false;
    }

    // requestAnimationFrame: gom nhiều sự kiện scroll vào 1 lần vẽ -> mượt
    window.addEventListener(
      "scroll",
      function () {
        if (!ticking) {
          window.requestAnimationFrame(update);
          ticking = true;
        }
      },
      { passive: true }
    );

    update();

    if (backToTop) {
      backToTop.addEventListener("click", function (e) {
        e.preventDefault();
        window.scrollTo({
          top: 0,
          behavior: reduceMotion ? "auto" : "smooth",
        });
      });
    }
  }

  /* ------------------------------------------------------------------
     3. SCROLL REVEAL
     Phần tử .reveal / .reveal-stagger được CSS ẩn sẵn.
     IntersectionObserver báo khi phần tử lọt vào màn hình -> gắn .is-visible.
     ------------------------------------------------------------------ */
  function initReveal() {
    var targets = document.querySelectorAll(".reveal, .reveal-stagger");
    if (!targets.length) return;

    // Trình duyệt quá cũ hoặc người dùng tắt hiệu ứng: hiện hết ngay
    if (reduceMotion || !("IntersectionObserver" in window)) {
      targets.forEach(function (el) {
        el.classList.add("is-visible");
      });
      return;
    }

    // Rải độ trễ cho các con của .reveal-stagger để chúng hiện so le
    document.querySelectorAll(".reveal-stagger").forEach(function (group) {
      Array.prototype.forEach.call(group.children, function (child, i) {
        child.style.transitionDelay = Math.min(i * 0.08, 0.6) + "s";
      });
    });

    var observer = new IntersectionObserver(
      function (entries) {
        entries.forEach(function (entry) {
          if (!entry.isIntersecting) return;
          entry.target.classList.add("is-visible");
          // Hiện xong thì thôi theo dõi — cuộn ngược lên không lặp lại
          observer.unobserve(entry.target);
        });
      },
      {
        // Kích hoạt khi phần tử vào sâu 12% từ đáy màn hình
        rootMargin: "0px 0px -12% 0px",
        threshold: 0.05,
      }
    );

    targets.forEach(function (el) {
      observer.observe(el);
    });
  }

  /* ------------------------------------------------------------------
     4. ĐÁNH DẤU MỤC MENU CỦA TRANG HIỆN TẠI
     ------------------------------------------------------------------ */
  function initActiveLink() {
    var path = window.location.pathname.split("/").pop();

    // Link là URL đầy đủ ("http://petio.test/shop") nên chỉ so đoạn cuối; trang chủ = ""
    function fileOf(link) {
      return new URL(link.href, window.location.href).pathname.split("/").pop();
    }

    document.querySelectorAll(".nav-item").forEach(function (item) {
      var link = item.querySelector(":scope > a");
      if (!link) return;

      var isActive = fileOf(link) === path;

      // Mục có submenu cũng sáng khi đang ở một trang con của nó
      if (!isActive) {
        isActive = Array.prototype.some.call(
          item.querySelectorAll(".submenu a"),
          function (sub) {
            return fileOf(sub) === path;
          }
        );
      }

      item.classList.toggle("is-active", isActive);
    });
  }

  /* ------------------------------------------------------------------
     5. NÚT YÊU THÍCH / CHIA SẺ (trang chi tiết sản phẩm)
     ------------------------------------------------------------------ */
  function initFavShare() {
    var fav = document.getElementById("fav-btn");
    var share = document.getElementById("share-btn");

    if (fav) {
      fav.addEventListener("click", function () {
        var active = fav.classList.toggle("is-active");
        fav.lastChild.textContent = active ? " Đã thích " : " Yêu thích ";
      });
    }

    if (share) {
      share.addEventListener("click", function () {
        var url = window.location.href;

        // Điện thoại: mở bảng chia sẻ của hệ điều hành
        if (navigator.share) {
          navigator.share({ title: document.title, url: url }).catch(function () {
            /* người dùng bấm hủy — không cần làm gì */
          });
          return;
        }

        // Máy tính: chép link vào clipboard
        if (navigator.clipboard) {
          navigator.clipboard.writeText(url).then(function () {
            if (window.PetioCart) PetioCart.toast("Đã sao chép link sản phẩm");
          });
        }
      });
    }
  }

  /* ------------------------------------------------------------------
     Khởi động
     ------------------------------------------------------------------ */
  function init() {
    initNav();
    initScrollEffects();
    initReveal();
    initActiveLink();
    initFavShare();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
