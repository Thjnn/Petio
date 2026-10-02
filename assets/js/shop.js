/* ==========================================================================
   shop.js — Tìm kiếm, lọc và sắp xếp sản phẩm (chỉ dùng ở shop.html)

   Không cần backend: mọi thông tin sản phẩm đã nằm sẵn trên thẻ HTML dưới
   dạng data-* (data-name, data-price, data-cat, data-pet). Script chỉ việc
   đọc rồi ẩn/hiện và sắp xếp lại các thẻ đó.

   Hỗ trợ link dạng shop.html?q=hạt  và  shop.html?cat=do-choi
   (cat=cho / cat=meo được hiểu là lọc theo loài thú cưng).
   ========================================================================== */
(function () {
  "use strict";

  function init() {
    var grid = document.getElementById("product-grid");
    if (!grid) return;

    var cards = Array.prototype.slice.call(
      grid.querySelectorAll("[data-product]")
    );

    var searchInput = document.getElementById("filter-search");
    var priceInput = document.getElementById("filter-price");
    var priceLabel = document.getElementById("price-max-label");
    var sortSelect = document.getElementById("sort-by");
    var countEl = document.getElementById("result-count");
    var noResult = document.getElementById("no-result");

    /* Ghi nhớ thứ tự ban đầu để "Mặc định" có thể khôi phục */
    cards.forEach(function (card, i) {
      card.dataset.order = i;
    });

    /* --------------------------------------------------------------
       Bỏ dấu tiếng Việt để tìm "hat" cũng ra "hạt"
       -------------------------------------------------------------- */
    function boDau(str) {
      return str
        .toLowerCase()
        .normalize("NFD")
        .replace(/[̀-ͯ]/g, "") // xóa các dấu thanh, dấu mũ
        .replace(/đ/g, "d");
    }

    function formatPrice(n) {
      return Number(n).toLocaleString("vi-VN") + "đ";
    }

    function getRadio(name) {
      var el = document.querySelector('input[name="' + name + '"]:checked');
      return el ? el.value : "all";
    }

    /* --------------------------------------------------------------
       Lọc + sắp xếp + cập nhật giao diện
       -------------------------------------------------------------- */
    function apply() {
      var keyword = boDau((searchInput ? searchInput.value : "").trim());
      var cat = getRadio("cat");
      var pet = getRadio("pet");
      var maxPrice = priceInput ? Number(priceInput.value) : Infinity;

      var visible = 0;

      cards.forEach(function (card) {
        var name = boDau(card.dataset.name || "");
        var price = Number(card.dataset.price || 0);

        var hopKeyword = keyword === "" || name.indexOf(keyword) !== -1;
        var hopCat = cat === "all" || card.dataset.cat === cat;
        // Sản phẩm dùng chung cho cả chó và mèo luôn hợp lệ
        var hopPet =
          pet === "all" ||
          card.dataset.pet === pet ||
          card.dataset.pet === "chung";
        var hopGia = price <= maxPrice;

        var hienThi = hopKeyword && hopCat && hopPet && hopGia;

        card.hidden = !hienThi;
        if (hienThi) visible++;
      });

      sapXep();

      if (countEl) countEl.textContent = visible;
      if (noResult) noResult.hidden = visible !== 0;
      grid.hidden = visible === 0;
    }

    function sapXep() {
      var kieu = sortSelect ? sortSelect.value : "default";

      var daSapXep = cards.slice().sort(function (a, b) {
        switch (kieu) {
          case "price-asc":
            return Number(a.dataset.price) - Number(b.dataset.price);
          case "price-desc":
            return Number(b.dataset.price) - Number(a.dataset.price);
          case "name-asc":
            return a.dataset.name.localeCompare(b.dataset.name, "vi");
          default:
            return Number(a.dataset.order) - Number(b.dataset.order);
        }
      });

      // appendChild trên phần tử đã có sẵn = di chuyển, không tạo mới
      daSapXep.forEach(function (card) {
        grid.appendChild(card);
      });
    }

    /* --------------------------------------------------------------
       Đọc tham số trên URL (từ ô tìm kiếm ở header hoặc menu con)
       -------------------------------------------------------------- */
    function docURL() {
      var params = new URLSearchParams(window.location.search);

      var q = params.get("q");
      if (q && searchInput) searchInput.value = q;

      var cat = params.get("cat");
      if (!cat) return;

      // cat=cho / cat=meo thực chất là lọc theo loài
      var nhom = cat === "cho" || cat === "meo" ? "pet" : "cat";
      var radio = document.querySelector(
        'input[name="' + nhom + '"][value="' + cat + '"]'
      );
      if (radio) radio.checked = true;
    }

    /* --------------------------------------------------------------
       Bộ lọc dạng ngăn kéo trên mobile
       -------------------------------------------------------------- */
    function initSidebar() {
      var sidebar = document.getElementById("sidebar");
      var overlay = document.getElementById("sidebar-overlay");
      var openBtn = document.getElementById("filter-toggle");
      var closeBtn = document.getElementById("sidebar-close");

      if (!sidebar || !overlay || !openBtn) return;

      function open() {
        sidebar.classList.add("is-open");
        overlay.hidden = false;
        requestAnimationFrame(function () {
          overlay.classList.add("is-open");
        });
        document.body.classList.add("no-scroll");
      }

      function close() {
        sidebar.classList.remove("is-open");
        overlay.classList.remove("is-open");
        document.body.classList.remove("no-scroll");
        window.setTimeout(function () {
          if (!sidebar.classList.contains("is-open")) overlay.hidden = true;
        }, 300);
      }

      openBtn.addEventListener("click", open);
      if (closeBtn) closeBtn.addEventListener("click", close);
      overlay.addEventListener("click", close);

      document.addEventListener("keydown", function (e) {
        if (e.key === "Escape" && sidebar.classList.contains("is-open")) close();
      });

      window.addEventListener("resize", function () {
        if (window.innerWidth > 992 && sidebar.classList.contains("is-open")) {
          close();
        }
      });
    }

    /* --------------------------------------------------------------
       Gắn sự kiện
       -------------------------------------------------------------- */
    if (searchInput) searchInput.addEventListener("input", apply);
    if (sortSelect) sortSelect.addEventListener("change", apply);

    if (priceInput) {
      priceInput.addEventListener("input", function () {
        if (priceLabel) priceLabel.textContent = formatPrice(priceInput.value);
        apply();
      });
    }

    document
      .querySelectorAll('input[name="cat"], input[name="pet"]')
      .forEach(function (radio) {
        radio.addEventListener("change", apply);
      });

    function xoaBoLoc() {
      if (searchInput) searchInput.value = "";
      if (priceInput) {
        priceInput.value = priceInput.max;
        if (priceLabel) priceLabel.textContent = formatPrice(priceInput.max);
      }
      if (sortSelect) sortSelect.value = "default";

      var catAll = document.querySelector('input[name="cat"][value="all"]');
      var petAll = document.querySelector('input[name="pet"][value="all"]');
      if (catAll) catAll.checked = true;
      if (petAll) petAll.checked = true;

      apply();
    }

    ["filter-reset", "no-result-reset"].forEach(function (id) {
      var btn = document.getElementById(id);
      if (btn) btn.addEventListener("click", xoaBoLoc);
    });

    /* Ô tìm kiếm ở header: đang ở trang shop thì lọc tại chỗ,
       không cần tải lại trang. */
    document.querySelectorAll(".header .search-box").forEach(function (form) {
      form.addEventListener("submit", function (e) {
        e.preventDefault();
        var input = form.querySelector("input");
        if (searchInput && input) {
          searchInput.value = input.value;
          apply();
          document
            .getElementById("product-grid")
            .scrollIntoView({ behavior: "smooth", block: "start" });
        }
      });
    });

    initSidebar();
    docURL();
    apply();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
