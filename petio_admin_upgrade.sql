-- =============================================================================
--  PETIO — NÂNG CẤP: THÊM PHẦN QUẢN TRỊ (ADMIN)
--  Dành cho DB `petio` ĐÃ tạo từ petio_schema.sql bản đầu (chưa có banners / admin_activity_logs).
--  Nếu bạn tạo DB mới từ petio_schema.sql hiện tại thì KHÔNG cần chạy file này.
--  Chạy đúng 1 lần (chạy lại sẽ báo trùng cột/bảng).
--
--  Thay đổi
--    - bảng mới : banners, admin_activity_logs
--    - categories : thêm created_at, updated_at, updated_by
--    - products, blog_posts : thêm updated_by
--    - users.role : thêm chú thích về quyền admin
-- =============================================================================
USE petio;
SET NAMES utf8mb4;

ALTER TABLE users
  MODIFY role ENUM('customer','staff','admin') NOT NULL DEFAULT 'customer'
  COMMENT 'admin = quản trị viên (xem phần Phân quyền ở đầu file)';

ALTER TABLE categories
  MODIFY is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '0 = ẩn khỏi cửa hàng',
  ADD COLUMN created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  ADD COLUMN updated_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  ADD COLUMN updated_by INT UNSIGNED NULL COMMENT 'Admin sửa gần nhất',
  ADD CONSTRAINT fk_categories_updated_by FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL,
  COMMENT='Danh mục sản phẩm (cây 2 cấp trở lên). Xóa danh mục còn con/còn sản phẩm sẽ bị chặn: phải chuyển đi trước';

ALTER TABLE products
  ADD COLUMN updated_by INT UNSIGNED NULL COMMENT 'Admin sửa gần nhất',
  ADD CONSTRAINT fk_products_updated_by FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL,
  COMMENT='Sản phẩm (thông tin chung). Admin thêm/sửa/xóa; "xóa" nên là ẩn (is_active=0), xóa hẳn vẫn an toàn vì đơn cũ giữ snapshot';

ALTER TABLE blog_posts
  MODIFY author_id INT UNSIGNED NULL COMMENT 'Admin viết bài',
  ADD COLUMN updated_by INT UNSIGNED NULL COMMENT 'Admin sửa bài gần nhất',
  ADD CONSTRAINT fk_blog_posts_updated_by FOREIGN KEY (updated_by) REFERENCES users (id) ON DELETE SET NULL,
  COMMENT='Bài viết / tin tức (admin viết, sửa, ẩn/hiện). Chỉ bài status=published mới hiện ra ngoài web';

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
