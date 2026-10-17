/* ==========================================================================
   cart.js — Giỏ hàng lưu ở server (session Laravel), gọi qua các route /cart/*
   Đường dẫn route + CSRF token được layout in sẵn vào window.Petio.

   Thẻ sản phẩm cần có (Blade component <x-product-card> đã in sẵn):
     <article data-product data-id="<variant_id>" data-name="..." data-price="...">
       ...
       <button data-add-to-cart>Thêm vào giỏ</button>
     </article>

   Trang chi tiết sản phẩm có thể thêm ô số lượng: <input data-qty value="1">
   ========================================================================== */
(function () {
  "use strict";

  var P = window.Petio || { routes: {} };

  // Bản sao giỏ hàng mới nhất server trả về
  var state = { items: [], count: 0, total: 0 };

  /* ------------------------------------------------------------------
     Gọi API giỏ hàng
     ------------------------------------------------------------------ */
  function request(url, body) {
    return fetch(url, {
      method: body ? "POST" : "GET",
      headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": P.csrf,
      },
      body: body ? JSON.stringify(body) : undefined,
      credentials: "same-origin",
    }).then(function (res) {
      return res.json().then(function (data) {
        if (!res.ok) throw new Error(data.message || "Có lỗi xảy ra, vui lòng thử lại.");
        state = data;
        return data;
      });
    });
  }

  function onError(err) {
    showToast(err.message);
  }

  /* ------------------------------------------------------------------
     Tiện ích
     ------------------------------------------------------------------ */
  function formatPrice(n) {
    return Number(n).toLocaleString("vi-VN") + "đ";
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
    }, 2600);
  }

  /* ------------------------------------------------------------------
     Cập nhật badge trên icon giỏ hàng
     ------------------------------------------------------------------ */
  function renderBadge(bump) {
    var badge = document.getElementById("cart-badge");
    if (!badge) return;

    badge.textContent = state.count;
    badge.hidden = state.count === 0;

    if (bump && state.count > 0) {
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
    var items = state.items;

    renderBadge(bump);

    var list = document.getElementById("cart-items");
    var empty = document.getElementById("cart-empty");
    var foot = document.getElementById("cart-foot");
    var totalEl = document.getElementById("cart-total");

    if (!list || !empty || !foot) return;

    var isEmpty = items.length === 0;
    empty.hidden = !isEmpty;
    list.hidden = isEmpty;
    foot.hidden = isEmpty;
    list.innerHTML = "";

    if (isEmpty) return;

    items.forEach(function (item) {
      var li = document.createElement("li");
      li.className = "cart-item";
      li.dataset.id = item.id;
      li.dataset.qty = item.qty;

      var img = document.createElement("img");
      img.className = "cart-item-img";
      img.src = item.img;
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
      remove.innerHTML = '<span class="material-symbols-outlined">delete</span>';

      li.appendChild(img);
      li.appendChild(info);
      li.appendChild(remove);
      list.appendChild(li);
    });

    if (totalEl) totalEl.textContent = formatPrice(state.total);
  }

  /* ------------------------------------------------------------------
     Các thao tác trên giỏ
     ------------------------------------------------------------------ */
  function addItem(product, qty) {
    if (!product || !product.id) return Promise.reject(new Error("Không tìm thấy sản phẩm."));

    qty = Math.max(1, parseInt(qty, 10) || 1);

    return request(P.routes.cartAdd, { variant_id: product.id, qty: qty }).then(function () {
      renderCart(true);
      showToast("Đã thêm “" + product.name + "” vào giỏ");
    });
  }

  function setQty(id, qty) {
    return request(P.routes.cartUpdate, { variant_id: id, qty: Math.max(0, qty) })
      .then(function () {
        renderCart(false);
      })
      .catch(onError);
  }

  function clearCart() {
    return request(P.routes.cartClear, {})
      .then(function () {
        renderCart(false);
      })
      .catch(onError);
  }

  /* ------------------------------------------------------------------
     Đọc thông tin sản phẩm từ thuộc tính data-* của thẻ HTML
     ------------------------------------------------------------------ */
  function readProduct(el) {
    var card = el.closest("[data-product]");
    if (!card) return null;

    return {
      id: card.dataset.id,
      name: card.dataset.name || "Sản phẩm",
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
        var qty = parseInt(li.dataset.qty, 10);
        if (e.target.closest("[data-inc]")) return setQty(id, qty + 1);
        if (e.target.closest("[data-dec]")) return setQty(id, qty - 1);
        if (e.target.closest("[data-remove]")) return setQty(id, 0);
      }

      if (e.target.closest("#cart-clear")) clearCart();
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
      btn.disabled = true;

      addItem(product, qty)
        .then(function () {
          // "Mua ngay" = thêm vào giỏ rồi sang thẳng trang thanh toán
          if (btn.hasAttribute("data-buy-now")) window.location.href = P.routes.checkout;
        })
        .catch(onError)
        .finally(function () {
          btn.disabled = false;
        });
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

    request(P.routes.cart)
      .then(function () {
        renderCart(false);
      })
      .catch(function () {
        /* Không tải được giỏ thì giữ giỏ trống, không chặn trang */
      });

    // Thông báo server gửi kèm sau khi chuyển trang (đăng nhập, gửi liên hệ...)
    if (P.flash) showToast(P.flash);
    if (P.error && window.showAlert) window.showAlert(P.error);
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  /* Cho các script khác dùng lại (ví dụ shop.js) */
  window.PetioCart = {
    add: addItem,
    clear: clearCart,
    items: function () {
      return state.items;
    },
    formatPrice: formatPrice,
    toast: showToast,
  };
})();
