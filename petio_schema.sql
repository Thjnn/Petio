-- =============================================================================
--  PETIO — THIẾT KẾ CƠ SỞ DỮ LIỆU MYSQL
--  Website bán đồ thú cưng + dịch vụ spa + tin tức (Petio-main)
--
--  Yêu cầu : MySQL 8.0.16+ (cần CHECK constraint thật sự được kiểm tra)
--  Cách chạy: mysql -u root -p < petio_schema.sql
--             (hoặc dán vào phpMyAdmin / HeidiSQL / MySQL Workbench của Laragon)
--
--  Quy ước chung
--  - Bảng, cột: snake_case, tiếng Anh. Khóa chính: id INT UNSIGNED AUTO_INCREMENT.
--  - Tiền VND lưu số nguyên (INT UNSIGNED) — VND không có phần lẻ.
--  - utf8mb4 + utf8mb4_unicode_ci: lưu tiếng Việt có dấu, tìm kiếm không phân biệt hoa/thường và dấu.
--  - Dữ liệu "đã chốt" (đơn hàng, lịch spa) lưu SNAPSHOT tên/giá tại thời điểm đặt,
--    để sau này sửa giá sản phẩm không làm sai đơn cũ.
--
--  Nhóm bảng
--    1. Tài khoản ........ users, user_addresses
--    2. Danh mục & SP .... categories, brands, products, product_variants, product_images,
--                          tags, product_tags, product_reviews, wishlists
--    3. Giỏ hàng & đơn ... carts, cart_items, coupons, orders, order_items, order_status_history
--    4. Spa .............. spa_services, spa_service_prices, spa_appointments, spa_appointment_services
--    5. Nội dung ......... blog_categories, blog_posts, post_tags, faqs
--    6. Liên hệ .......... contact_messages, newsletter_subscribers
--    7. Quản trị (admin) . banners, admin_activity_logs
--    8. Views ............ v_product_catalog, v_order_summary
--
--  Phân quyền (users.role)
--    admin    : toàn quyền quản trị — thêm/sửa/xóa sản phẩm, danh mục, thương hiệu; viết/sửa/ẩn bài viết;
--               thay banner; quản lý tài khoản; xem nhật ký thao tác.
--    staff    : (tùy chọn) nhân viên — xử lý đơn hàng, lịch spa, tin nhắn liên hệ.
--    customer : khách hàng.
--  Việc kiểm tra quyền nằm ở tầng ứng dụng (backend), CSDL chỉ lưu role.
-- =============================================================================

CREATE DATABASE IF NOT EXISTS petio
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE petio;
SET NAMES utf8mb4;


-- =============================================================================
-- 1. TÀI KHOẢN
-- =============================================================================

-- Trang dangky.html / dangnhap.html: một ô "Email/Số điện thoại/Tên đăng nhập".
-- => username bắt buộc, email và phone là tùy chọn nhưng nếu có thì không được trùng
--    (MySQL cho phép nhiều giá trị NULL trong cột UNIQUE).
CREATE TABLE users (
  id             INT UNSIGNED  NOT NULL AUTO_INCREMENT,
  username       VARCHAR(50)   NOT NULL,
  email          VARCHAR(150)  NULL,
  phone          VARCHAR(20)   NULL,
  password_hash  VARCHAR(255)  NOT NULL COMMENT 'Chỉ lưu hash (bcrypt/argon2), tuyệt đối không lưu mật khẩu gốc',
  full_name      VARCHAR(100)  NULL,
  avatar_url     VARCHAR(255)  NULL,
  role           ENUM('customer','staff','admin') NOT NULL DEFAULT 'customer'
                   COMMENT 'admin = quản trị viên (xem phần Phân quyền ở đầu file)',
  status         ENUM('active','locked')          NOT NULL DEFAULT 'active',
  last_login_at  DATETIME      NULL,
  created_at     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username),
  UNIQUE KEY uq_users_email    (email),
  UNIQUE KEY uq_users_phone    (phone)
) ENGINE=InnoDB COMMENT='Tài khoản khách hàng / nhân viên / quản trị';

-- Sổ địa chỉ giao hàng của khách (mỗi khách nhiều địa chỉ).
CREATE TABLE user_addresses (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id         INT UNSIGNED NOT NULL,
  receiver_name   VARCHAR(100) NOT NULL,
  receiver_phone  VARCHAR(20)  NOT NULL,
  province        VARCHAR(100) NOT NULL COMMENT 'Tỉnh/Thành phố',
  district        VARCHAR(100) NOT NULL COMMENT 'Quận/Huyện',
  ward            VARCHAR(100) NOT NULL COMMENT 'Phường/Xã',
  address_line    VARCHAR(255) NOT NULL COMMENT 'Số nhà, tên đường',
  is_default      TINYINT(1)   NOT NULL DEFAULT 0,
  created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_addr_user (user_id),
  CONSTRAINT fk_addr_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Địa chỉ giao hàng đã lưu';


-- =============================================================================
-- 2. DANH MỤC & SẢN PHẨM
-- =============================================================================

-- Danh mục dạng cây (cha – con), khớp menu "Cửa hàng": thuc-an, do-choi, phu-kien, ve-sinh, thuoc
-- và các thư mục con trong assets/file_anh (Thức ăn hạt, Pate & nước sốt, Sữa, ...).
-- "Dành cho chó / mèo" KHÔNG nằm ở đây mà là cột products.pet_type (đúng như bộ lọc shop.js).
CREATE TABLE categories (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  parent_id    INT UNSIGNED NULL COMMENT 'NULL = danh mục gốc',
  name         VARCHAR(120) NOT NULL,
  slug         VARCHAR(140) NOT NULL COMMENT 'Khớp ?cat=... trên URL, vd: thuc-an',
  image_url    VARCHAR(255) NULL,
  sort_order   SMALLINT     NOT NULL DEFAULT 0,
  is_active    TINYINT(1)   NOT NULL DEFAULT 1 COMMENT '0 = ẩn khỏi cửa hàng',
  created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  updated_by   INT UNSIGNED NULL COMMENT 'Admin sửa gần nhất',
  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_slug (slug),
  KEY idx_categories_parent (parent_id),
  CONSTRAINT fk_categories_parent     FOREIGN KEY (parent_id)  REFERENCES categories (id) ON DELETE RESTRICT,
  CONSTRAINT fk_categories_updated_by FOREIGN KEY (updated_by) REFERENCES users (id)      ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='Danh mục sản phẩm (cây 2 cấp trở lên). Xóa danh mục còn con/còn sản phẩm sẽ bị chặn: phải chuyển đi trước';

-- Thương hiệu: "Hãng: Pedigree" ở product.html, "Nhãn hiệu nổi tiếng" ở shop.html.
CREATE TABLE brands (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name       VARCHAR(100) NOT NULL,
  slug       VARCHAR(120) NOT NULL,
  logo_url   VARCHAR(255) NULL,
  is_active  TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_brands_name (name),
  UNIQUE KEY uq_brands_slug (slug)
) ENGINE=InnoDB COMMENT='Thương hiệu';

-- Sản phẩm = thông tin chung (tên, mô tả, danh mục, loài).
-- GIÁ và TỒN KHO nằm ở product_variants, vì cùng một sản phẩm có nhiều size/giá
-- (vd: Pedigree Puppy 400g: 57.000đ, 1.3kg: 133.000đ).
CREATE TABLE products (
  id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id        INT UNSIGNED NOT NULL,
  brand_id           INT UNSIGNED NULL,
  name               VARCHAR(200) NOT NULL,
  slug               VARCHAR(220) NOT NULL,
  pet_type           ENUM('dog','cat','both') NOT NULL DEFAULT 'both'
                       COMMENT 'Dành cho: dog=chó, cat=mèo, both=dùng chung (data-pet="chung")',
  short_description  VARCHAR(500) NULL,
  description        MEDIUMTEXT   NULL COMMENT 'Mô tả chi tiết (HTML): công dụng, ưu điểm, thành phần...',
  thumbnail_url      VARCHAR(255) NULL,
  rating_avg         DECIMAL(2,1) NOT NULL DEFAULT 0.0 COMMENT 'Cache từ product_reviews để khỏi tính lại mỗi lần hiển thị',
  rating_count       INT UNSIGNED NOT NULL DEFAULT 0   COMMENT 'Cache số lượt đánh giá, hiện dạng "(100)"',
  sold_count         INT UNSIGNED NOT NULL DEFAULT 0,
  is_featured        TINYINT(1)   NOT NULL DEFAULT 0   COMMENT 'Hiện ở mục "Mặt hàng được yêu thích nhất" trang chủ',
  is_active          TINYINT(1)   NOT NULL DEFAULT 1   COMMENT '0 = ẩn (xóa mềm, giữ lịch sử đơn hàng)',
  created_at         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  updated_by         INT UNSIGNED NULL COMMENT 'Admin sửa gần nhất',
  PRIMARY KEY (id),
  UNIQUE KEY uq_products_slug (slug),
  KEY idx_products_category (category_id, is_active),
  KEY idx_products_brand    (brand_id),
  KEY idx_products_pet      (pet_type),
  KEY idx_products_name     (name),
  CONSTRAINT fk_products_category FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE RESTRICT,
  CONSTRAINT fk_products_brand    FOREIGN KEY (brand_id)    REFERENCES brands (id)     ON DELETE SET NULL,
  CONSTRAINT fk_products_updated_by FOREIGN KEY (updated_by) REFERENCES users (id)     ON DELETE SET NULL,
  CONSTRAINT chk_products_rating  CHECK (rating_avg BETWEEN 0 AND 5)
) ENGINE=InnoDB COMMENT='Sản phẩm (thông tin chung). Admin thêm/sửa/xóa; "xóa" nên là ẩn (is_active=0), xóa hẳn vẫn an toàn vì đơn cũ giữ snapshot';

-- Biến thể: mỗi dòng là một lựa chọn mua được (size / trọng lượng / màu...) kèm giá và tồn kho.
-- Sản phẩm không có size thì tạo đúng 1 biến thể tên "Mặc định".
CREATE TABLE product_variants (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id  INT UNSIGNED NOT NULL,
  sku         VARCHAR(50)  NOT NULL,
  name        VARCHAR(100) NOT NULL DEFAULT 'Mặc định' COMMENT 'vd: 400g, 1.3kg, Size M',
  price       INT UNSIGNED NOT NULL COMMENT 'Giá bán hiện tại (đ)',
  old_price   INT UNSIGNED NULL     COMMENT 'Giá gốc để gạch ngang; NULL = không giảm giá. % giảm = (old_price-price)/old_price',
  stock_qty   INT UNSIGNED NOT NULL DEFAULT 0,
  is_default  TINYINT(1)   NOT NULL DEFAULT 0 COMMENT 'Biến thể hiển thị giá ở trang danh sách',
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  sort_order  SMALLINT     NOT NULL DEFAULT 0,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_variants_sku (sku),
  UNIQUE KEY uq_variants_product_name (product_id, name),
  KEY idx_variants_price (price),
  CONSTRAINT fk_variants_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
  CONSTRAINT chk_variants_old_price CHECK (old_price IS NULL OR old_price >= price)
) ENGINE=InnoDB COMMENT='Biến thể sản phẩm: giá + tồn kho';

CREATE TABLE product_images (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id  INT UNSIGNED NOT NULL,
  image_url   VARCHAR(255) NOT NULL,
  alt_text    VARCHAR(200) NULL,
  sort_order  SMALLINT     NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_images_product (product_id, sort_order),
  CONSTRAINT fk_images_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Ảnh phụ của sản phẩm (ảnh chính nằm ở products.thumbnail_url)';

-- Tag dùng chung cho sản phẩm (Hot, Trend, ...) và bài viết (Medical, Food, ...).
CREATE TABLE tags (
  id    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name  VARCHAR(60)  NOT NULL,
  slug  VARCHAR(80)  NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_tags_name (name),
  UNIQUE KEY uq_tags_slug (slug)
) ENGINE=InnoDB COMMENT='Thẻ tag dùng chung';

CREATE TABLE product_tags (
  product_id  INT UNSIGNED NOT NULL,
  tag_id      INT UNSIGNED NOT NULL,
  PRIMARY KEY (product_id, tag_id),
  KEY idx_ptags_tag (tag_id),
  CONSTRAINT fk_ptags_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
  CONSTRAINT fk_ptags_tag     FOREIGN KEY (tag_id)     REFERENCES tags (id)     ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Sản phẩm – Tag (n-n)';

-- Đánh giá sao ("(100) đánh giá"). Mỗi khách đánh giá một sản phẩm tối đa 1 lần.
CREATE TABLE product_reviews (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  product_id  INT UNSIGNED NOT NULL,
  user_id     INT UNSIGNED NOT NULL,
  rating      TINYINT UNSIGNED NOT NULL,
  comment     TEXT         NULL,
  status      ENUM('pending','approved','hidden') NOT NULL DEFAULT 'pending' COMMENT 'Admin duyệt trước khi hiện',
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_reviews_user_product (user_id, product_id),
  KEY idx_reviews_product (product_id, status),
  CONSTRAINT fk_reviews_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE,
  CONSTRAINT fk_reviews_user    FOREIGN KEY (user_id)    REFERENCES users (id)    ON DELETE CASCADE,
  CONSTRAINT chk_reviews_rating CHECK (rating BETWEEN 1 AND 5)
) ENGINE=InnoDB COMMENT='Đánh giá & nhận xét sản phẩm';

-- Nút "Yêu thích" ở product.html.
CREATE TABLE wishlists (
  user_id     INT UNSIGNED NOT NULL,
  product_id  INT UNSIGNED NOT NULL,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (user_id, product_id),
  KEY idx_wishlists_product (product_id),
  CONSTRAINT fk_wishlists_user    FOREIGN KEY (user_id)    REFERENCES users (id)    ON DELETE CASCADE,
  CONSTRAINT fk_wishlists_product FOREIGN KEY (product_id) REFERENCES products (id) ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Sản phẩm yêu thích';


-- =============================================================================
-- 3. GIỎ HÀNG & ĐƠN HÀNG
-- =============================================================================

-- Hiện giỏ hàng lưu localStorage (khóa petio_cart). Khi có backend: khách đã đăng nhập
-- dùng user_id, khách chưa đăng nhập dùng session_token (cookie), đăng nhập xong thì gộp giỏ.
CREATE TABLE carts (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id        INT UNSIGNED NULL,
  session_token  CHAR(36)     NULL COMMENT 'UUID lưu trong cookie cho khách vãng lai',
  created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_carts_user    (user_id),
  UNIQUE KEY uq_carts_session (session_token),
  CONSTRAINT fk_carts_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE,
  CONSTRAINT chk_carts_owner CHECK (user_id IS NOT NULL OR session_token IS NOT NULL)
) ENGINE=InnoDB COMMENT='Giỏ hàng (mỗi chủ một giỏ)';

-- Không lưu giá trong giỏ: giá luôn đọc từ product_variants để không bị lệch khi shop đổi giá.
CREATE TABLE cart_items (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  cart_id     INT UNSIGNED NOT NULL,
  variant_id  INT UNSIGNED NOT NULL,
  quantity    SMALLINT UNSIGNED NOT NULL DEFAULT 1,
  added_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_cart_items_variant (cart_id, variant_id),
  KEY idx_cart_items_variant (variant_id),
  CONSTRAINT fk_cart_items_cart    FOREIGN KEY (cart_id)    REFERENCES carts (id)            ON DELETE CASCADE,
  CONSTRAINT fk_cart_items_variant FOREIGN KEY (variant_id) REFERENCES product_variants (id) ON DELETE CASCADE,
  CONSTRAINT chk_cart_items_qty CHECK (quantity > 0)
) ENGINE=InnoDB COMMENT='Sản phẩm trong giỏ';

-- Mã giảm giá: "giảm 10% lần mua tiếp theo", "giảm 35% đơn đầu tiên", "giảm 25% đơn từ 1 triệu"...
CREATE TABLE coupons (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  code             VARCHAR(30)  NOT NULL,
  description      VARCHAR(255) NULL,
  discount_type    ENUM('percent','fixed') NOT NULL,
  discount_value   INT UNSIGNED NOT NULL COMMENT 'percent: 1-100 | fixed: số tiền (đ)',
  max_discount     INT UNSIGNED NULL     COMMENT 'Trần giảm tối đa (đ) cho loại percent',
  min_order_value  INT UNSIGNED NOT NULL DEFAULT 0,
  first_order_only TINYINT(1)   NOT NULL DEFAULT 0 COMMENT '1 = chỉ áp dụng đơn đầu tiên của khách',
  usage_limit      INT UNSIGNED NULL     COMMENT 'Tổng số lượt dùng tối đa; NULL = không giới hạn',
  used_count       INT UNSIGNED NOT NULL DEFAULT 0,
  starts_at        DATETIME     NULL,
  expires_at       DATETIME     NULL,
  is_active        TINYINT(1)   NOT NULL DEFAULT 1,
  created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_coupons_code (code),
  CONSTRAINT chk_coupons_percent CHECK (discount_type = 'fixed' OR discount_value BETWEEN 1 AND 100)
) ENGINE=InnoDB COMMENT='Mã khuyến mãi';

CREATE TABLE orders (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_code       VARCHAR(20)  NOT NULL COMMENT 'Mã hiển thị cho khách, vd: PT20261013-0001',
  user_id          INT UNSIGNED NULL     COMMENT 'NULL = khách mua không đăng nhập',
  coupon_id        INT UNSIGNED NULL,

  -- Snapshot thông tin nhận hàng (giữ nguyên dù khách sau này sửa/xóa địa chỉ)
  receiver_name    VARCHAR(100) NOT NULL,
  receiver_phone   VARCHAR(20)  NOT NULL,
  receiver_email   VARCHAR(150) NULL,
  ship_province    VARCHAR(100) NOT NULL,
  ship_district    VARCHAR(100) NOT NULL,
  ship_ward        VARCHAR(100) NOT NULL,
  ship_address     VARCHAR(255) NOT NULL,

  subtotal         INT UNSIGNED NOT NULL COMMENT 'Tổng tiền hàng',
  discount_amount  INT UNSIGNED NOT NULL DEFAULT 0,
  shipping_fee     INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Miễn phí khi đủ ngưỡng (site ghi: từ 500.000đ)',
  total_amount     INT UNSIGNED NOT NULL COMMENT '= subtotal - discount_amount + shipping_fee',

  payment_method   ENUM('cod','bank_transfer','momo','vnpay') NOT NULL DEFAULT 'cod',
  payment_status   ENUM('unpaid','paid','refunded')           NOT NULL DEFAULT 'unpaid',
  paid_at          DATETIME     NULL,
  status           ENUM('pending','confirmed','shipping','completed','cancelled','returned')
                                NOT NULL DEFAULT 'pending',
  note             VARCHAR(500) NULL COMMENT 'Ghi chú của khách',
  cancel_reason    VARCHAR(255) NULL,
  created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_orders_code (order_code),
  KEY idx_orders_user    (user_id, created_at),
  KEY idx_orders_status  (status, created_at),
  KEY idx_orders_coupon  (coupon_id),
  CONSTRAINT fk_orders_user   FOREIGN KEY (user_id)   REFERENCES users (id)   ON DELETE SET NULL,
  CONSTRAINT fk_orders_coupon FOREIGN KEY (coupon_id) REFERENCES coupons (id) ON DELETE SET NULL,
  CONSTRAINT chk_orders_total CHECK (
    CAST(total_amount AS SIGNED) =
      CAST(subtotal AS SIGNED) - CAST(discount_amount AS SIGNED) + CAST(shipping_fee AS SIGNED)
  )
) ENGINE=InnoDB COMMENT='Đơn hàng';

CREATE TABLE order_items (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id      INT UNSIGNED NOT NULL,
  variant_id    INT UNSIGNED NULL COMMENT 'NULL nếu biến thể sau này bị xóa hẳn — đơn cũ vẫn đủ thông tin nhờ snapshot',
  product_name  VARCHAR(200) NOT NULL COMMENT 'Snapshot tên sản phẩm lúc đặt',
  variant_name  VARCHAR(100) NOT NULL COMMENT 'Snapshot tên biến thể lúc đặt',
  sku           VARCHAR(50)  NULL,
  unit_price    INT UNSIGNED NOT NULL COMMENT 'Snapshot đơn giá lúc đặt',
  quantity      SMALLINT UNSIGNED NOT NULL,
  line_total    INT UNSIGNED GENERATED ALWAYS AS (unit_price * quantity) STORED,
  PRIMARY KEY (id),
  KEY idx_order_items_order   (order_id),
  KEY idx_order_items_variant (variant_id),
  CONSTRAINT fk_order_items_order   FOREIGN KEY (order_id)   REFERENCES orders (id)           ON DELETE CASCADE,
  CONSTRAINT fk_order_items_variant FOREIGN KEY (variant_id) REFERENCES product_variants (id) ON DELETE SET NULL,
  CONSTRAINT chk_order_items_qty CHECK (quantity > 0)
) ENGINE=InnoDB COMMENT='Chi tiết đơn hàng (snapshot giá)';

-- Nhật ký chuyển trạng thái đơn: ai đổi, lúc nào, từ trạng thái nào sang trạng thái nào.
CREATE TABLE order_status_history (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_id     INT UNSIGNED NOT NULL,
  from_status  VARCHAR(20)  NULL,
  to_status    VARCHAR(20)  NOT NULL,
  changed_by   INT UNSIGNED NULL COMMENT 'Nhân viên/admin thực hiện; NULL = hệ thống/khách',
  note         VARCHAR(255) NULL,
  created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_osh_order (order_id, created_at),
  CONSTRAINT fk_osh_order FOREIGN KEY (order_id)   REFERENCES orders (id) ON DELETE CASCADE,
  CONSTRAINT fk_osh_user  FOREIGN KEY (changed_by) REFERENCES users (id)  ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='Lịch sử trạng thái đơn hàng';


-- =============================================================================
-- 4. SPA (spa.html)
-- =============================================================================

-- Bảng giá spa trong ảnh CtAOPls.jpeg: 3 gói tính theo cân nặng + "Nhuộm" tính theo khoảng giá.
--   pricing_type = 'by_weight' -> giá nằm ở spa_service_prices (theo mốc cân nặng)
--   pricing_type = 'range'     -> giá từ price_from đến price_to, nhân viên chốt giá khi làm
CREATE TABLE spa_services (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name           VARCHAR(120) NOT NULL,
  description    VARCHAR(500) NULL,
  pricing_type   ENUM('by_weight','range') NOT NULL DEFAULT 'by_weight',
  price_from     INT UNSIGNED NULL,
  price_to       INT UNSIGNED NULL,
  duration_min   SMALLINT UNSIGNED NULL COMMENT 'Thời gian làm dự kiến (phút)',
  sort_order     SMALLINT     NOT NULL DEFAULT 0,
  is_active      TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY uq_spa_services_name (name),
  CONSTRAINT chk_spa_services_range CHECK (
    pricing_type = 'by_weight' OR (price_from IS NOT NULL AND price_to IS NOT NULL AND price_to >= price_from)
  )
) ENGINE=InnoDB COMMENT='Dịch vụ spa';

-- Mốc cân nặng: weight_over_kg < cân nặng <= weight_up_to_kg   (vd: 1 < w <= 3  tương ứng "1.1kg - 3kg").
-- Cách tra giá: WHERE service_id = ? AND ? > weight_over_kg AND ? <= weight_up_to_kg
CREATE TABLE spa_service_prices (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  service_id       INT UNSIGNED NOT NULL,
  weight_over_kg   DECIMAL(5,1) NOT NULL COMMENT 'Cận dưới (không gồm)',
  weight_up_to_kg  DECIMAL(5,1) NOT NULL COMMENT 'Cận trên (gồm)',
  price            INT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_spa_prices_band (service_id, weight_over_kg),
  CONSTRAINT fk_spa_prices_service FOREIGN KEY (service_id) REFERENCES spa_services (id) ON DELETE CASCADE,
  CONSTRAINT chk_spa_prices_band CHECK (weight_up_to_kg > weight_over_kg)
) ENGINE=InnoDB COMMENT='Bảng giá spa theo cân nặng';

-- Lịch hẹn spa. Khách chưa đăng nhập vẫn đặt được nên user_id có thể NULL,
-- thông tin liên hệ và thú cưng được lưu thẳng trên lịch hẹn.
CREATE TABLE spa_appointments (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  appointment_code VARCHAR(20)  NOT NULL,
  user_id          INT UNSIGNED NULL,
  contact_name     VARCHAR(100) NOT NULL,
  contact_phone    VARCHAR(20)  NOT NULL,
  pet_name         VARCHAR(80)  NOT NULL,
  pet_species      ENUM('dog','cat') NOT NULL,
  pet_breed        VARCHAR(80)  NULL,
  pet_weight_kg    DECIMAL(5,1) NOT NULL,
  appointment_at   DATETIME     NOT NULL COMMENT 'Giờ hẹn mang thú cưng tới',
  need_pickup      TINYINT(1)   NOT NULL DEFAULT 0 COMMENT 'Dịch vụ đưa đón thú cưng',
  pickup_address   VARCHAR(255) NULL,
  note             VARCHAR(500) NULL,
  total_price      INT UNSIGNED NOT NULL DEFAULT 0,
  status           ENUM('pending','confirmed','in_progress','completed','cancelled','no_show')
                                NOT NULL DEFAULT 'pending',
  created_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at       DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_spa_appt_code (appointment_code),
  KEY idx_spa_appt_time   (appointment_at, status),
  KEY idx_spa_appt_user   (user_id),
  CONSTRAINT fk_spa_appt_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL,
  CONSTRAINT chk_spa_appt_weight CHECK (pet_weight_kg > 0)
) ENGINE=InnoDB COMMENT='Lịch hẹn spa';

CREATE TABLE spa_appointment_services (
  appointment_id  INT UNSIGNED NOT NULL,
  service_id      INT UNSIGNED NOT NULL,
  price           INT UNSIGNED NOT NULL COMMENT 'Snapshot giá lúc đặt (hoặc giá nhân viên chốt với dịch vụ nhuộm)',
  PRIMARY KEY (appointment_id, service_id),
  KEY idx_spa_appt_services_service (service_id),
  CONSTRAINT fk_sas_appt    FOREIGN KEY (appointment_id) REFERENCES spa_appointments (id) ON DELETE CASCADE,
  CONSTRAINT fk_sas_service FOREIGN KEY (service_id)     REFERENCES spa_services (id)     ON DELETE RESTRICT
) ENGINE=InnoDB COMMENT='Dịch vụ trong lịch hẹn (1 lịch có thể chọn nhiều dịch vụ)';


-- =============================================================================
-- 5. NỘI DUNG: TIN TỨC & FAQ
-- =============================================================================

-- Sidebar blog.html: Mẹo vặt, Thời trang, Lối sống, Chuyện ngắn.
CREATE TABLE blog_categories (
  id    INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name  VARCHAR(100) NOT NULL,
  slug  VARCHAR(120) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_blog_cat_name (name),
  UNIQUE KEY uq_blog_cat_slug (slug)
) ENGINE=InnoDB COMMENT='Chuyên mục bài viết';

CREATE TABLE blog_posts (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  category_id    INT UNSIGNED NULL,
  author_id      INT UNSIGNED NULL COMMENT 'Admin viết bài',
  updated_by     INT UNSIGNED NULL COMMENT 'Admin sửa bài gần nhất',
  title          VARCHAR(255) NOT NULL,
  slug           VARCHAR(280) NOT NULL,
  summary        VARCHAR(500) NULL,
  content        MEDIUMTEXT   NOT NULL COMMENT 'Nội dung bài viết (HTML)',
  thumbnail_url  VARCHAR(255) NULL,
  status         ENUM('draft','published','hidden') NOT NULL DEFAULT 'draft',
  view_count     INT UNSIGNED NOT NULL DEFAULT 0,
  published_at   DATETIME     NULL,
  created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_blog_posts_slug (slug),
  KEY idx_blog_posts_list (status, published_at),
  KEY idx_blog_posts_cat  (category_id),
  CONSTRAINT fk_blog_posts_cat    FOREIGN KEY (category_id) REFERENCES blog_categories (id) ON DELETE SET NULL,
  CONSTRAINT fk_blog_posts_author FOREIGN KEY (author_id)   REFERENCES users (id)           ON DELETE SET NULL,
  CONSTRAINT fk_blog_posts_updated_by FOREIGN KEY (updated_by) REFERENCES users (id)        ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='Bài viết / tin tức (admin viết, sửa, ẩn/hiện). Chỉ bài status=published mới hiện ra ngoài web';

CREATE TABLE post_tags (
  post_id  INT UNSIGNED NOT NULL,
  tag_id   INT UNSIGNED NOT NULL,
  PRIMARY KEY (post_id, tag_id),
  KEY idx_post_tags_tag (tag_id),
  CONSTRAINT fk_post_tags_post FOREIGN KEY (post_id) REFERENCES blog_posts (id) ON DELETE CASCADE,
  CONSTRAINT fk_post_tags_tag  FOREIGN KEY (tag_id)  REFERENCES tags (id)       ON DELETE CASCADE
) ENGINE=InnoDB COMMENT='Bài viết – Tag (n-n)';

-- FAQ.html chia thành các mục: "01. Đặt hàng", "02. Lô hàng", "04. Đổi trả – Đổi hàng – Khiếu nại"...
-- Mỗi mục có nhiều cặp hỏi/đáp -> lưu tiêu đề mục ở section_title, thứ tự mục ở section_order.
CREATE TABLE faqs (
  id             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  section_title  VARCHAR(120) NOT NULL COMMENT 'Tiêu đề mục, vd: Đặt hàng',
  section_order  SMALLINT     NOT NULL DEFAULT 0,
  question       VARCHAR(500) NOT NULL,
  answer         TEXT         NOT NULL,
  sort_order     SMALLINT     NOT NULL DEFAULT 0 COMMENT 'Thứ tự câu hỏi trong mục',
  is_active      TINYINT(1)   NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  KEY idx_faqs_section (is_active, section_order, sort_order)
) ENGINE=InnoDB COMMENT='Câu hỏi thường gặp';


-- =============================================================================
-- 6. LIÊN HỆ & BẢN TIN
-- =============================================================================

-- lienhe.html: TenKhachHang, Email, SDT, NoiDung.
CREATE TABLE contact_messages (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  user_id     INT UNSIGNED NULL COMMENT 'Có giá trị nếu người gửi đang đăng nhập',
  full_name   VARCHAR(100) NOT NULL,
  email       VARCHAR(150) NOT NULL,
  phone       VARCHAR(20)  NOT NULL,
  content     TEXT         NOT NULL,
  status      ENUM('new','read','replied') NOT NULL DEFAULT 'new',
  admin_note  VARCHAR(500) NULL,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  replied_at  DATETIME     NULL,
  PRIMARY KEY (id),
  KEY idx_contact_status (status, created_at),
  CONSTRAINT fk_contact_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='Thắc mắc khách gửi từ trang Liên hệ';

-- Ô "Đăng ký nhận bản tin" (SUBSCRIBE) ở footer.
CREATE TABLE newsletter_subscribers (
  id               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  email            VARCHAR(150) NOT NULL,
  is_active        TINYINT(1)   NOT NULL DEFAULT 1,
  subscribed_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  unsubscribed_at  DATETIME     NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_newsletter_email (email)
) ENGINE=InnoDB COMMENT='Email đăng ký nhận bản tin';


-- =============================================================================
-- 7. QUẢN TRỊ (ADMIN)
-- =============================================================================

-- Banner / hình ảnh quảng cáo trên web. Admin thay banner = sửa image_url (hoặc thêm dòng mới, tắt dòng cũ).
-- Các vị trí lấy từ index.html:
--   home_hero     : banner lớn đầu trang ("ĐỒ ĂN THÚ CƯNG CHẤT LƯỢNG CAO ... Mua ngay")
--   home_category : 5 thẻ "Danh mục hàng đầu"
--   home_promo    : 3 thẻ ưu đãi ("Ưu đãi cho mùa hè nóng nực", ...)
--   home_wide     : các khối rộng giữa/cuối trang chủ (gói chăm sóc sức khỏe, "Muốn một bé thú cưng mới?")
-- Trang chủ lấy: WHERE position='home_promo' AND is_active=1
--                  AND (starts_at IS NULL OR starts_at <= NOW()) AND (ends_at IS NULL OR ends_at >= NOW())
--                ORDER BY sort_order
CREATE TABLE banners (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  position    ENUM('home_hero','home_category','home_promo','home_wide') NOT NULL,
  title       VARCHAR(200) NULL COMMENT 'Tiêu đề lớn; dòng mới = ký tự xuống dòng (LF) trong chuỗi',
  subtitle    VARCHAR(200) NULL COMMENT 'Dòng chữ nhỏ phía trên/dưới tiêu đề',
  image_url   VARCHAR(255) NOT NULL,
  alt_text    VARCHAR(200) NULL,
  button_text VARCHAR(60)  NULL COMMENT 'vd: Mua ngay',
  link_url    VARCHAR(255) NULL COMMENT 'Trang đích khi bấm, vd: html/shop.html?cat=thuc-an',
  sort_order  SMALLINT     NOT NULL DEFAULT 0,
  starts_at   DATETIME     NULL COMMENT 'NULL = hiện ngay',
  ends_at     DATETIME     NULL COMMENT 'NULL = không hết hạn',
  is_active   TINYINT(1)   NOT NULL DEFAULT 1,
  created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  updated_by  INT UNSIGNED NULL COMMENT 'Admin sửa gần nhất',
  PRIMARY KEY (id),
  KEY idx_banners_show (position, is_active, sort_order),
  CONSTRAINT fk_banners_updated_by FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL,
  CONSTRAINT chk_banners_dates CHECK (starts_at IS NULL OR ends_at IS NULL OR ends_at >= starts_at)
) ENGINE=InnoDB COMMENT='Banner quảng cáo do admin quản lý';

-- Nhật ký thao tác của admin: ai làm gì, lúc nào, với đối tượng nào, dữ liệu trước/sau khi sửa.
-- Dùng để truy vết khi có người xóa nhầm sản phẩm hay thay nhầm banner.
-- entity_label lưu tên tại thời điểm thao tác vì đối tượng có thể đã bị xóa.
CREATE TABLE admin_activity_logs (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  admin_id      INT UNSIGNED NULL COMMENT 'NULL nếu tài khoản admin sau này bị xóa',
  action        ENUM('create','update','delete','activate','deactivate') NOT NULL,
  entity_type   ENUM('product','product_variant','category','brand','banner','blog_post','blog_category','coupon','order','user') NOT NULL,
  entity_id     INT UNSIGNED NULL,
  entity_label  VARCHAR(255) NULL COMMENT 'Tên/tiêu đề của đối tượng lúc thao tác',
  old_data      JSON         NULL COMMENT 'Giá trị trước khi sửa/xóa',
  new_data      JSON         NULL COMMENT 'Giá trị sau khi thêm/sửa',
  ip_address    VARCHAR(45)  NULL,
  created_at    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_admin_logs_admin  (admin_id, created_at),
  KEY idx_admin_logs_entity (entity_type, entity_id),
  CONSTRAINT fk_admin_logs_admin FOREIGN KEY (admin_id) REFERENCES users (id) ON DELETE SET NULL
) ENGINE=InnoDB COMMENT='Nhật ký thao tác quản trị';


-- =============================================================================
-- 8. VIEWS (tiện cho truy vấn, không phải bảng lưu dữ liệu)
-- =============================================================================

-- Danh sách sản phẩm cho trang Cửa hàng: lấy giá của biến thể mặc định
-- (nếu chưa đánh dấu is_default thì lấy biến thể rẻ nhất).
CREATE OR REPLACE VIEW v_product_catalog AS
SELECT
  p.id,
  p.name,
  p.slug,
  p.pet_type,
  c.slug                AS category_slug,
  c.name                AS category_name,
  b.name                AS brand_name,
  p.thumbnail_url,
  v.price,
  v.old_price,
  CASE WHEN v.old_price IS NOT NULL AND v.old_price > v.price
       THEN ROUND((v.old_price - v.price) * 100 / v.old_price) END AS discount_percent,
  p.rating_avg,
  p.rating_count,
  p.is_featured,
  (SELECT COALESCE(SUM(stock_qty), 0) FROM product_variants s
    WHERE s.product_id = p.id AND s.is_active = 1) AS total_stock
FROM products p
JOIN categories c ON c.id = p.category_id
LEFT JOIN brands b ON b.id = p.brand_id
JOIN product_variants v ON v.id = (
  SELECT x.id FROM product_variants x
  WHERE x.product_id = p.id AND x.is_active = 1
  ORDER BY x.is_default DESC, x.price ASC, x.id ASC
  LIMIT 1
)
WHERE p.is_active = 1;

-- Tóm tắt đơn hàng: tiện cho trang quản trị.
CREATE OR REPLACE VIEW v_order_summary AS
SELECT
  o.id,
  o.order_code,
  o.created_at,
  COALESCE(u.username, '(khách vãng lai)') AS customer,
  o.receiver_name,
  o.receiver_phone,
  COUNT(oi.id)                    AS item_lines,
  COALESCE(SUM(oi.quantity), 0)   AS item_quantity,
  o.subtotal,
  o.discount_amount,
  o.shipping_fee,
  o.total_amount,
  o.payment_method,
  o.payment_status,
  o.status
FROM orders o
LEFT JOIN users u        ON u.id = o.user_id
LEFT JOIN order_items oi ON oi.order_id = o.id
GROUP BY o.id, u.username;
