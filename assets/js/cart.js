/* ==========================================================================
   cart.js — Giỏ hàng lưu bằng localStorage
   Dữ liệu sản phẩm được đọc trực tiếp từ các thuộc tính data-* trên thẻ HTML,
   nên không cần backend và không cần file dữ liệu riêng.

   Thẻ sản phẩm cần có:
     <div class="card" data-product
          data-id="p01"
          data-name="Hạt Pedigree vị gà và trứng 400gr"
          data-price="57000"
          data-img="./assets/file_anh/....jpg">   (trang trong html/ dùng "../assets/...")
       ...
       <button class="btn add-to-cart" data-add-to-cart>Thêm vào giỏ</button>
     </div>

   Trang chi tiết sản phẩm có thể thêm ô số lượng: <input data-qty value="1">
   ========================================================================== */
(function () {
  "use strict";

  var STORAGE_KEY = "petio_cart";

  // Thư mục gốc của site, suy ra từ vị trí của chính file này (assets/js/cart.js)
  var SITE_ROOT = document.currentScript
    ? document.currentScript.src.replace(/assets\/js\/[^\/]*$/, "")
    : "";

  /* ------------------------------------------------------------------
     Đọc / ghi localStorage
     ------------------------------------------------------------------ */
  function load() {
    try {
      var raw = localStorage.getItem(STORAGE_KEY);
      var items = raw ? JSON.parse(raw) : [];
      return Array.isArray(items) ? items : [];
    } catch (e) {
      // localStorage bị chặn (chế độ ẩn danh) hoặc dữ liệu hỏng
      return [];
    }
  }

  function save(items) {
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(items));
    } catch (e) {
      /* Không lưu được thì giỏ vẫn chạy trong phiên hiện tại */
    }
  }

  /* ------------------------------------------------------------------
     Tiện ích
     ------------------------------------------------------------------ */
  function formatPrice(n) {
    return Number(n).toLocaleString("vi-VN") + "đ";
  }

  /* Ảnh trong giỏ được lưu dạng "./assets/..." (thêm từ index.html) hoặc
     "../assets/..." (thêm từ html/*.html). Quy về gốc site để trang nào cũng mở được. */
  function resolveImg(path) {
    if (!path || /^(?:[a-z]+:|\/)/i.test(path)) return path;
    return SITE_ROOT + path.replace(/^(?:\.{1,2}\/)+/, "");
  }

  function totalQty(items) {
    return items.reduce(function (sum, it) {
      return sum + it.qty;
    }, 0);
  }

  function totalPrice(items) {
    return items.reduce(function (sum, it) {
      return sum + it.price * it.qty;
    }, 0);
  }

  /* ------------------------------------------------------------------
     Toast "Đã thêm vào giỏ"
     ------------------------------------------------------------------ */
  var toastTimer = null;

  function showToast(message) {
    var toast = document.getElementById("toast");
    if (!toast) return;

    toast.innerHTML =
      '<span class="material-symbols-outlined">check_circle</span>' +
      "<span></span>";
    toast.lastElementChild.textContent = message;
    toast.classList.add("is-open");

    window.clearTimeout(toastTimer);
    toastTimer = window.setTimeout(function () {
      toast.classList.remove("is-open");
    }, 2200);
  }

  /* ------------------------------------------------------------------
     Cập nhật badge trên icon giỏ hàng
     ------------------------------------------------------------------ */
  function renderBadge(items, bump) {
    var badge = document.getElementById("cart-badge");
    if (!badge) return;

    var count = totalQty(items);
    badge.textContent = count;
    badge.hidden = count === 0;

    if (bump && count > 0) {
      badge.classList.remove("is-bump");
      // Ép trình duyệt tính lại để animation chạy lại từ đầu
      void badge.offsetWidth;
      badge.classList.add("is-bump");
    }
  }

  /* ------------------------------------------------------------------
     Vẽ nội dung modal giỏ hàng
     ------------------------------------------------------------------ */
  function renderCart(bump) {
    var items = load();

    renderBadge(items, bump);

    var list = document.getElementById("cart-items");
    var empty = document.getElementById("cart-empty");
    var foot = document.getElementById("cart-foot");
    var totalEl = document.getElementById("cart-total");

    if (!list || !empty || !foot) return;

    var isEmpty = items.length === 0;
    empty.hidden = !isEmpty;
    list.hidden = isEmpty;
    foot.hidden = isEmpty;

    if (isEmpty) {
      list.innerHTML = "";
      return;
    }

    list.innerHTML = "";

    items.forEach(function (item) {
      var li = document.createElement("li");
      li.className = "cart-item";
      li.dataset.id = item.id;

      var img = document.createElement("img");
      img.className = "cart-item-img";
      img.src = resolveImg(item.img);
      img.alt = item.name;
      img.loading = "lazy";

      var info = document.createElement("div");

      var name = document.createElement("p");
      name.className = "cart-item-name";
      name.textContent = item.name;

      var price = document.createElement("p");
      price.className = "cart-item-price";
      price.textContent = formatPrice(item.price);

      var qty = document.createElement("div");
      qty.className = "cart-qty";
      qty.innerHTML =
        '<button type="button" data-dec aria-label="Giảm số lượng">−</button>' +
        "<span>" +
        item.qty +
        "</span>" +
        '<button type="button" data-inc aria-label="Tăng số lượng">+</button>';

      info.appendChild(name);
      info.appendChild(price);
      info.appendChild(qty);

      var remove = document.createElement("button");
      remove.type = "button";
      remove.className = "cart-item-remove";
      remove.setAttribute("data-remove", "");
      remove.setAttribute("aria-label", "Xóa " + item.name);
      remove.innerHTML =
        '<span class="material-symbols-outlined">delete</span>';

      li.appendChild(img);
      li.appendChild(info);
      li.appendChild(remove);
      list.appendChild(li);
    });

    if (totalEl) totalEl.textContent = formatPrice(totalPrice(items));
  }

  /* ------------------------------------------------------------------
     Các thao tác trên giỏ
     ------------------------------------------------------------------ */
  function addItem(product, qty) {
    if (!product || !product.id) return;

    qty = Math.max(1, parseInt(qty, 10) || 1);

    var items = load();
    var existing = items.find(function (it) {
      return it.id === product.id;
    });

    if (existing) {
      existing.qty += qty;
    } else {
      items.push({
        id: product.id,
        name: product.name,
        price: product.price,
        img: product.img,
        qty: qty,
      });
    }

    save(items);
    renderCart(true);
    showToast("Đã thêm “" + product.name + "” vào giỏ");
  }

  function changeQty(id, delta) {
    var items = load();
    var item = items.find(function (it) {
      return it.id === id;
    });
    if (!item) return;

    item.qty += delta;

    if (item.qty <= 0) {
      items = items.filter(function (it) {
        return it.id !== id;
      });
    }

    save(items);
    renderCart(false);
  }

  function removeItem(id) {
    save(
      load().filter(function (it) {
        return it.id !== id;
      })
    );
    renderCart(false);
  }

  function clearCart() {
    save([]);
    renderCart(false);
  }

  /* ------------------------------------------------------------------
     Đọc thông tin sản phẩm từ thuộc tính data-* của thẻ HTML
     ------------------------------------------------------------------ */
  function readProduct(el) {
    var card = el.closest("[data-product]");
    if (!card) return null;

    var price = parseInt(
      String(card.dataset.price || "").replace(/\D/g, ""),
      10
    );

    return {
      id: card.dataset.id || card.dataset.name,
      name: card.dataset.name || "Sản phẩm",
      price: isNaN(price) ? 0 : price,
      img: card.dataset.img || "",
      qtyInput: card.querySelector("[data-qty]"),
    };
  }

  /* ------------------------------------------------------------------
     Mở / đóng modal giỏ hàng
     ------------------------------------------------------------------ */
  function initModal() {
    var modal = document.getElementById("cart-modal");
    var openBtn = document.getElementById("open-cart");
    var closeBtn = document.getElementById("close-cart");

    if (!modal || !openBtn) return;

    function open() {
      renderCart(false);
      modal.classList.add("is-open");
      document.body.classList.add("no-scroll");
    }

    function close() {
      modal.classList.remove("is-open");
      document.body.classList.remove("no-scroll");
    }

    openBtn.addEventListener("click", open);
    if (closeBtn) closeBtn.addEventListener("click", close);

    // Bấm ra vùng nền tối để đóng
    modal.addEventListener("click", function (e) {
      if (e.target === modal) close();
    });

    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && modal.classList.contains("is-open")) close();
    });

    // Các nút bên trong modal (ủy quyền sự kiện: nội dung được vẽ lại liên tục)
    modal.addEventListener("click", function (e) {
      var li = e.target.closest(".cart-item");

      if (li) {
        var id = li.dataset.id;
        if (e.target.closest("[data-inc]")) return changeQty(id, 1);
        if (e.target.closest("[data-dec]")) return changeQty(id, -1);
        if (e.target.closest("[data-remove]")) return removeItem(id);
      }

      if (e.target.closest("#cart-clear")) {
        clearCart();
      }

      if (e.target.closest("#cart-checkout")) {
        showToast("Cảm ơn bạn! Đơn hàng đã được ghi nhận.");
        clearCart();
        close();
      }
    });
  }

  /* ------------------------------------------------------------------
     Bắt sự kiện bấm nút "Thêm vào giỏ" / "Mua ngay" ở bất cứ đâu trên trang
     ------------------------------------------------------------------ */
  function initAddButtons() {
    document.addEventListener("click", function (e) {
      var btn = e.target.closest("[data-add-to-cart], [data-buy-now]");
      if (!btn) return;

      e.preventDefault();

      var product = readProduct(btn);
      if (!product) return;

      var qty = product.qtyInput ? product.qtyInput.value : 1;
      addItem(product, qty);

      // "Mua ngay" = thêm vào giỏ rồi mở luôn giỏ hàng
      if (btn.hasAttribute("data-buy-now")) {
        var openBtn = document.getElementById("open-cart");
        if (openBtn) openBtn.click();
      }
    });
  }

  /* ------------------------------------------------------------------
     Ô chọn số lượng (nút − / +) ở trang chi tiết sản phẩm
     ------------------------------------------------------------------ */
  function initQtySelector() {
    document.addEventListener("click", function (e) {
      var btn = e.target.closest("[data-qty-inc], [data-qty-dec]");
      if (!btn) return;

      var wrap = btn.closest("[data-product]") || document;
      var input = wrap.querySelector("[data-qty]");
      if (!input) return;

      var value = parseInt(input.value, 10) || 1;
      var min = parseInt(input.min, 10) || 1;
      var max = parseInt(input.max, 10) || 99;

      value += btn.hasAttribute("data-qty-inc") ? 1 : -1;
      input.value = Math.min(max, Math.max(min, value));
    });
  }

  /* ------------------------------------------------------------------
     Khởi động
     ------------------------------------------------------------------ */
  function init() {
    initModal();
    initAddButtons();
    initQtySelector();
    renderCart(false);

    // Mở giỏ ở tab khác thì tab này cũng cập nhật theo
    window.addEventListener("storage", function (e) {
      if (e.key === STORAGE_KEY) renderCart(false);
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  /* Cho các script khác dùng lại (ví dụ shop.js) */
  window.PetioCart = {
    add: addItem,
    remove: removeItem,
    clear: clearCart,
    items: load,
    formatPrice: formatPrice,
    toast: showToast,
  };
})();
