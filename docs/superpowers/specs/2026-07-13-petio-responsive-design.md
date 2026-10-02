# Petio — Thiết kế nâng cấp Responsive + Animation + Chức năng

Ngày: 2026-07-13
Đồ án: Website bán đồ thú cưng Petio — Cao Phúc Thịnh (023101057)

## 1. Vấn đề

Trang web hiện tại chạy được nhưng chỉ đúng trên màn hình desktop rộng:

- **Không có một media query nào** trong cả 13 file CSS.
- Layout dựa trên `display:flex` + `justify-content:space-around` **không có `flex-wrap`**, kèm chiều rộng cứng: `.main-content{max-width:1770px}`, `.main-content-recommend{width:1520px}`, ảnh `340px`/`475px`/`283px`. Dưới ~1400px layout bắt đầu chen chúc, trên điện thoại vỡ hoàn toàn và tràn ngang.
- Menu chỉ có dạng thanh ngang, không có hamburger cho mobile.
- Header/nav/footer bị copy-paste giống hệt ở cả 11 file HTML; đoạn JS back-to-top lặp inline 11 lần.
- `shop.html` và `spa.html` link sai đường dẫn CSS (`assets/back-to-top.css`, đúng là `assets/css/back-to-top.css`) nên nút back-to-top ở 2 trang đó mất style.
- Giỏ hàng chỉ là popup rỗng, ô tìm kiếm không hoạt động, không thêm được sản phẩm vào giỏ.
- Animation mới dừng ở vài `transition` hover.

## 2. Mục tiêu

1. Chạy đúng và đẹp từ 320px đến màn hình rộng.
2. Animation tinh tế, mượt, không lòe loẹt; tôn trọng `prefers-reduced-motion`.
3. Giỏ hàng, tìm kiếm, lọc sản phẩm hoạt động thật.
4. Giữ được tinh thần thiết kế cũ (cam `#ed6436`, các section quen thuộc), chỉ chuẩn hóa lại cho đồng bộ.

## 3. Ràng buộc (đã chốt với người dùng)

- **HTML/CSS/JS thuần.** Không Bootstrap, không Tailwind, không AOS, không build step. Lý do: sinh viên phải giải thích được từng dòng code khi bị chất vấn.
- **Giữ web tĩnh.** Mỗi file HTML tự chứa header/footer của nó — mở bằng double-click cũng chạy, không phụ thuộc server. Đánh đổi: 11 bản header phải được giữ đồng bộ thủ công.
- **Không thiết kế lại từ đầu.** Sửa cho chắc + tinh chỉnh thẩm mỹ, không đổi bố cục lớn (báo cáo `BaoCao.docx` đã có ảnh chụp màn hình cũ).

## 4. Kiến trúc

### 4.1 CSS

Tách theo trách nhiệm, mỗi file một việc:

| File | Trách nhiệm |
|---|---|
| `assets/css/reset.css` | Giữ nguyên. Reset trình duyệt. |
| `assets/css/base.css` *(mới)* | Design token (custom property): bảng màu, thang khoảng cách, bo góc, đổ bóng, thang chữ dùng `clamp()`. Utility dùng lại: `.container`, `.btn`, `.grid`, `.section`. Khai báo `@keyframes` và các lớp `.reveal` cho scroll-animation. |
| `assets/css/layout.css` *(mới)* | Khung dùng chung 11 trang: header + hamburger + drawer mobile, nav, ô tìm kiếm, modal giỏ hàng, toast, footer, back-to-top. |
| `assets/css/<trang>.css` | Chỉ còn phần đặc thù của trang đó. |

`back-to-top.css` bị xóa, nội dung gộp vào `layout.css`. Phần header/footer/modal đang nằm trong `style.css` được chuyển sang `layout.css`; `style.css` chỉ còn phần trang chủ.

**Breakpoint** (chọn theo chuẩn quen thuộc, dễ giải thích):

```
≥ 1200px  desktop lớn   (mặc định)
< 1200px  desktop nhỏ / laptop
< 992px   tablet
< 768px   mobile lớn — chuyển sang menu hamburger
< 576px   mobile nhỏ
```

**Nguyên tắc thay layout cứng:**

- Mọi hàng thẻ (`categories-container`, `seller-container`, `recommend-container`, lưới sản phẩm, lưới bài viết) chuyển từ `flex + space-around` sang
  `display: grid; grid-template-columns: repeat(auto-fit, minmax(<min>, 1fr)); gap: var(--space-…)`.
  Grid tự xuống dòng, không cần đếm breakpoint cho từng lưới.
- Bỏ hết `width`/`height` cứng của ảnh; dùng `width:100%; aspect-ratio; object-fit:cover`.
- `.container` thay cho `.main-content`: `width:min(100% - 2*padding, 1440px); margin-inline:auto`.
- Cỡ chữ tiêu đề dùng `clamp()` thay số cố định (`4rem` → `clamp(2.4rem, 4vw, 4rem)`).
- Section nào cũng dùng `padding-block` từ token thay cho `margin-top:140px`.

### 4.2 JavaScript

| File | Trách nhiệm |
|---|---|
| `script.js` | Giữ các hàm validate form đang được gọi bằng `onsubmit`/`onclick` trong HTML (`kiemtraDN`, `kiemtraDK`, `kiemtralienhe`, `showAlert`, `closeAlert`). Không đổi tên hàm để không phá HTML. |
| `assets/js/main.js` *(mới)* | Mở/đóng menu hamburger, header co lại khi cuộn, nút back-to-top (thay cho 11 đoạn inline), scroll-reveal bằng `IntersectionObserver`, tắt animation khi `prefers-reduced-motion: reduce`. |
| `assets/js/cart.js` *(mới)* | Giỏ hàng lưu ở `localStorage` khóa `petio_cart`: thêm/xóa, tăng giảm số lượng, tính tổng tiền, cập nhật badge trên icon giỏ, render nội dung modal, toast xác nhận. |
| `assets/js/shop.js` *(mới)* | Chỉ dùng ở `shop.html`: tìm kiếm theo tên, lọc theo danh mục và khoảng giá, sắp xếp theo giá/tên. Lọc trực tiếp trên DOM các thẻ sản phẩm có sẵn (không cần backend). |

Mô hình dữ liệu sản phẩm: mỗi thẻ sản phẩm trong HTML mang `data-id`, `data-name`, `data-price`, `data-img`, `data-category`. JS chỉ đọc từ `data-*` — không cần file JSON riêng, không cần server.

### 4.3 Animation

Chỉ dùng `transform` và `opacity` (được GPU tăng tốc, không gây reflow):

- **Scroll-reveal:** phần tử có class `.reveal` bắt đầu ở `opacity:0; translateY(24px)`, thêm `.is-visible` khi lọt vào viewport → trượt lên hiện dần. Có `.reveal-delay-1..4` để các thẻ trong một hàng hiện lệch nhau.
- **Hover thẻ:** nâng nhẹ `translateY(-6px)` + đổ bóng đậm hơn; ảnh bên trong `scale(1.06)` trong khung `overflow:hidden`.
- **Header:** khi cuộn quá 80px thì giảm chiều cao và thêm đổ bóng.
- **Drawer mobile:** trượt từ phải vào, kèm lớp phủ mờ dần.
- **Modal giỏ hàng:** nền fade-in, hộp `scale(.96) → 1`.
- **Toast "Đã thêm vào giỏ":** trượt lên từ đáy, tự ẩn sau 2s.
- **FAQ accordion:** mở/đóng bằng `grid-template-rows: 0fr → 1fr` (mượt hơn `max-height`).
- Toàn bộ nằm trong `@media (prefers-reduced-motion: no-preference)`; người dùng tắt hiệu ứng hệ thống thì trang hiện tĩnh, vẫn đầy đủ nội dung.

## 5. Thứ tự thực hiện

1. `base.css` + `layout.css` — nền tảng.
2. `main.js` + `cart.js` — hành vi dùng chung.
3. Đồng bộ header/footer/`<head>`/back-to-top trên cả 11 file HTML; gỡ script inline; sửa link CSS sai; đổi `lang="en"` → `lang="vi"`.
4. Từng trang: `index` → `shop` → `product` → `spa`/`blog`/`blogchitiet` → `aboutus`/`FAQ`/`lienhe`/`dangnhap`/`dangky`.
5. Kiểm thử ở 375px / 768px / 1440px, xác nhận không tràn ngang, menu mobile và giỏ hàng chạy.

## 6. Tiêu chí hoàn thành

- Không trang nào cuộn ngang được ở 320px.
- Menu hamburger hoạt động ở mọi trang < 768px.
- Thêm sản phẩm vào giỏ ở `shop.html`/`product.html` → badge tăng, modal hiện đúng sản phẩm, tổng tiền đúng, F5 vẫn còn (localStorage).
- Tìm kiếm và lọc ở `shop.html` cho kết quả đúng, có trạng thái "không tìm thấy sản phẩm".
- Toàn bộ trang mở trực tiếp bằng double-click file `.html` vẫn chạy đủ.
