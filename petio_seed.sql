-- =============================================================================
--  PETIO — DỮ LIỆU MẪU (chạy SAU petio_schema.sql)
--  Lấy từ chính nội dung website: 17 sản phẩm ở shop.html, bảng giá spa (ảnh CtAOPls.jpeg),
--  FAQ, tiêu đề bài viết. Mã giảm giá là ví dụ minh họa theo các dòng khuyến mãi trên web.
--  Dùng được nhiều lần? KHÔNG — chạy lần 2 sẽ trùng khóa. Muốn nạp lại: xóa DB rồi chạy lại cả 2 file.
-- =============================================================================
USE petio;
SET NAMES utf8mb4;

-- ---------- Danh mục ----------
INSERT INTO categories (id, parent_id, name, slug, sort_order) VALUES
  (1, NULL, 'Thức ăn thú cưng', 'thuc-an', 1),
  (2, NULL, 'Đồ chơi thú cưng', 'do-choi', 2),
  (3, NULL, 'Phụ kiện, chuồng, nệm', 'phu-kien', 3),
  (4, NULL, 'Vệ sinh - chăm sóc', 've-sinh', 4),
  (5, NULL, 'Thuốc và thực phẩm chức năng', 'thuoc', 5),
  (6, 1, 'Thức ăn hạt', 'thuc-an-hat', 1),
  (7, 1, 'Pate & nước sốt', 'pate-nuoc-sot', 2),
  (8, 1, 'Sữa', 'sua', 3),
  (9, 1, 'Bánh thưởng & xương gặm', 'banh-thuong-xuong-gam', 4),
  (10, 3, 'Chuồng, nhà, balo, quây', 'chuong-nha-balo-quay', 5),
  (11, 3, 'Đệm & nệm', 'dem-nem', 6),
  (12, 3, 'Vòng cổ, dây dắt, rọ mõm', 'vong-co-day-dat-ro-mom', 7),
  (13, 3, 'Quần áo', 'quan-ao', 8),
  (14, 3, 'Bình nước, bát ăn, lược', 'binh-nuoc-bat-an-luoc', 9),
  (15, 4, 'Bỉm, tã, khay vệ sinh', 'bim-ta-khay-ve-sinh', 10),
  (16, 4, 'Sữa tắm, khăn tắm, nước hoa', 'sua-tam-khan-tam-nuoc-hoa', 11),
  (17, 4, 'Cát vệ sinh', 'cat-ve-sinh', 12),
  (18, 5, 'Thuốc thú y', 'thuoc-thu-y', 13),
  (19, 5, 'Thực phẩm chức năng', 'thuc-pham-chuc-nang', 14);

-- ---------- Thương hiệu ----------
INSERT INTO brands (id, name, slug) VALUES
  (1, 'Endogard', 'endogard'),
  (2, 'HUG', 'hug'),
  (3, 'Jolly Cat', 'jolly-cat'),
  (4, 'Pedigree', 'pedigree'),
  (5, 'Pet Own', 'pet-own'),
  (6, 'Zoi Dog', 'zoi-dog');

-- ---------- Tag ----------
INSERT INTO tags (id, name, slug) VALUES
  (1, 'Hot', 'hot'),
  (2, 'Trend', 'trend'),
  (3, 'Baber', 'baber'),
  (4, 'Baby Needs', 'baby-needs'),
  (5, 'Beauty', 'beauty'),
  (6, 'Cosmetic', 'cosmetic'),
  (7, 'Ear Care', 'ear-care'),
  (8, 'Electric', 'electric'),
  (9, 'Fashion', 'fashion'),
  (10, 'Food', 'food'),
  (11, 'Jwerly', 'jwerly'),
  (12, 'Medical', 'medical'),
  (13, 'Mimimal', 'mimimal'),
  (14, 'Organic', 'organic'),
  (15, 'Simple', 'simple'),
  (16, 'Sport', 'sport');

-- ---------- Sản phẩm ----------
INSERT INTO products (id, category_id, brand_id, name, slug, pet_type, short_description, thumbnail_url, rating_avg, rating_count, is_featured) VALUES
  (1, 6, 4, 'Hạt Pedigree vị gà và trứng 400gr', 'hat-pedigree-vi-ga-va-trung-400gr', 'dog', 'Thức ăn dạng hạt Pedigree Puppy vị gà sốt trứng đem đến những bữa ăn đầy đủ chất dinh dưỡng, cùng hương vị ngon miệng kích thích các bé ăn uống.', 'assets/file_anh/Chó/Thức Ăn Và Thực Phẩm Dinh Dưỡng/Thức Ăn Hạt/Hat-Pedigree-Puppy-vi-ga-va-trung-400g.jpg', 5.0, 100, 1),
  (2, 6, 6, 'Hạt Zoi Dog thức ăn cho chó 1kg', 'hat-zoi-dog-thuc-an-cho-cho-1kg', 'dog', NULL, 'assets/file_anh/Chó/Thức Ăn Và Thực Phẩm Dinh Dưỡng/Thức Ăn Hạt/Hat-Zoi-Dog-thuc-an-cho-cho-20kg.jpg', 4.0, 50, 1),
  (3, 2, NULL, 'Lốp xe cao su cho chó mèo', 'lop-xe-cao-su-cho-cho-meo', 'both', NULL, 'assets/file_anh/Chó/Đồ Dùng & Đồ Chơi & Phụ Kiện/Đồ chơi cho chó/ezgif-4-c55d00de52.jpg', 5.0, 1, 1),
  (4, 2, NULL, 'Đồ chơi dây thừng hình cục xương', 'do-choi-day-thung-hinh-cuc-xuong', 'dog', NULL, 'assets/file_anh/Chó/Đồ Dùng & Đồ Chơi & Phụ Kiện/Đồ chơi cho chó/40ee7d4948b2777f8449a135c62fe00c.jpg', 5.0, 1, 1),
  (5, 8, NULL, 'Sữa bột colostrum chó mèo hộp 100g', 'sua-bot-colostrum-cho-meo-hop-100g', 'both', NULL, 'assets/file_anh/Mèo/Thức Ăn Và Thực Phẩm Dinh Dưỡng - Copy/Sữa Cho Mèo/2d509a3bc77f520d07ea46f83bcbe567.jpg', 5.0, 10, 1),
  (6, 8, 5, 'Sữa tươi Pet Own cho chó mèo 1 lít, nhập khẩu từ Úc', 'sua-tuoi-pet-own-cho-cho-meo-1-lit-nhap-khau-tu-uc', 'both', NULL, 'assets/file_anh/Mèo/Thức Ăn Và Thực Phẩm Dinh Dưỡng - Copy/Sữa Cho Mèo/632daf3b28db898b1cbfd071efc01266.jpg', 5.0, 1000, 1),
  (7, 10, NULL, 'Nhà cây - trụ cào móng cho mèo', 'nha-cay-tru-cao-mong-cho-meo', 'cat', NULL, 'assets/file_anh/Mèo/Chuồng, Chậu, Balo Và Túi Vận Chuyển/Chuồng mèo & Chậu Cát Mèo/Nha-cay-tru-cao-mong-cho-meo-1848-4.jpg', 5.0, 100, 1),
  (8, 10, NULL, 'Chuồng tầng bằng sắt cho mèo', 'chuong-tang-bang-sat-cho-meo', 'cat', NULL, 'assets/file_anh/Mèo/Chuồng, Chậu, Balo Và Túi Vận Chuyển/Chuồng mèo & Chậu Cát Mèo/Chuong-tang-bang-sat-cho-meo.jpg', 5.0, 1, 1),
  (9, 9, NULL, 'Bánh thưởng và xương gặm cho chó', 'banh-thuong-va-xuong-gam-cho-cho', 'dog', NULL, 'assets/file_anh/Chó/Thức Ăn Và Thực Phẩm Dinh Dưỡng/Bánh Thưởng Và Xương Gặm/5d2278b966c99a97c3d8-2675-1247x1496.jpg', 4.0, 32, 0),
  (10, 7, 2, 'Pate HUG cho chó mèo', 'pate-hug-cho-cho-meo', 'both', NULL, 'assets/file_anh/Chó/Thức Ăn Và Thực Phẩm Dinh Dưỡng/Pate & Nước sốt/ezgif-2-b50846658d.jpg', 5.0, 10, 0),
  (11, 2, NULL, 'Chuột đuôi bi đồ chơi cho mèo', 'chuot-duoi-bi-do-choi-cho-meo', 'cat', NULL, 'assets/file_anh/Mèo/Đồ Dùng & Đồ Chơi & Phụ Kiện - Copy/Đồ chơi cho mèo/chuot-duoi-bi.jpg', 4.0, 18, 0),
  (12, 11, NULL, 'Nệm điều hòa mát cho chó mèo', 'nem-dieu-hoa-mat-cho-cho-meo', 'both', NULL, 'assets/file_anh/Chó/Chuồng, nhà, balo, Quây, Đệm/Đệm & Nệm/nem-dieu-hoa.jpg', 4.0, 26, 0),
  (13, 11, NULL, 'Nệm nhung chữ nhật cho chó mèo', 'nem-nhung-chu-nhat-cho-cho-meo', 'both', NULL, 'assets/file_anh/Chó/Chuồng, nhà, balo, Quây, Đệm/Đệm & Nệm/nem-chu-nhat-nhung-cho-cho-meo1-min.png', 5.0, 41, 0),
  (14, 12, NULL, 'Vòng cổ và dây dắt cho chó', 'vong-co-va-day-dat-cho-cho', 'dog', NULL, 'assets/file_anh/Chó/Đồ Dùng & Đồ Chơi & Phụ Kiện/Vòng cổ, Dây Dắt Và Rọ Mõm/650d8ec958409371787eb9f658975e5c.jpg', 4.0, 12, 0),
  (15, 17, 3, 'Cát vệ sinh cho mèo Jolly Cat', 'cat-ve-sinh-cho-meo-jolly-cat', 'cat', NULL, 'assets/file_anh/Mèo/Vệ Sinh Và Chăm Sóc - Copy/Cát vệ sinh mèo/Cat-Jolly-Cat.jpg', 5.0, 64, 0),
  (16, 16, NULL, 'Sữa tắm khử mùi cho chó', 'sua-tam-khu-mui-cho-cho', 'dog', NULL, 'assets/file_anh/Chó/Vệ Sinh Và Chăm Sóc/Sữa Tắm & Khăn Tắm & Nước Hoa/1476c0b0458948b7a6b3a668b98ce5e3.jpg', 4.0, 23, 0),
  (17, 18, 1, 'Thuốc tẩy giun Endogard cho chó', 'thuoc-tay-giun-endogard-cho-cho', 'dog', NULL, 'assets/file_anh/Chó/Thuốc Và Thực Phẩm Chức Năng/Thuốc Thú Y/Endogard_2019.jpg', 5.0, 87, 0);

-- ---------- Biến thể (giá + tồn kho) ----------
-- Mỗi SP có 1 biến thể 'Mặc định'; Pedigree Puppy có thêm size 1.3kg (giá ghi ở product.html). Tồn kho đặt tạm 100.
INSERT INTO product_variants (product_id, sku, name, price, old_price, stock_qty, is_default, sort_order) VALUES
  (1, 'PT-001-400G', '400g', 57000, 63000, 100, 1, 1),
  (1, 'PT-001-1K3', '1.3kg', 133000, NULL, 100, 0, 2),
  (2, 'PT-002', 'Mặc định', 35000, NULL, 100, 1, 1),
  (3, 'PT-003', 'Mặc định', 17000, NULL, 100, 1, 1),
  (4, 'PT-004', 'Mặc định', 47000, 56000, 100, 1, 1),
  (5, 'PT-005', 'Mặc định', 36000, 45000, 100, 1, 1),
  (6, 'PT-006', 'Mặc định', 84000, 120000, 100, 1, 1),
  (7, 'PT-007', 'Mặc định', 3000000, NULL, 100, 1, 1),
  (8, 'PT-008', 'Mặc định', 2700000, 3000000, 100, 1, 1),
  (9, 'PT-009', 'Mặc định', 45000, NULL, 100, 1, 1),
  (10, 'PT-010', 'Mặc định', 50000, NULL, 100, 1, 1),
  (11, 'PT-011', 'Mặc định', 25000, NULL, 100, 1, 1),
  (12, 'PT-012', 'Mặc định', 180000, 220000, 100, 1, 1),
  (13, 'PT-013', 'Mặc định', 150000, NULL, 100, 1, 1),
  (14, 'PT-014', 'Mặc định', 65000, NULL, 100, 1, 1),
  (15, 'PT-015', 'Mặc định', 90000, NULL, 100, 1, 1),
  (16, 'PT-016', 'Mặc định', 120000, 140000, 100, 1, 1),
  (17, 'PT-017', 'Mặc định', 75000, NULL, 100, 1, 1);

-- ---------- Tag sản phẩm (badge 'Hot' trên thẻ sản phẩm; p01 còn có 'Trend' ở product.html) ----------
INSERT INTO product_tags (product_id, tag_id) VALUES
  (1, 1),
  (3, 1),
  (4, 1),
  (5, 1),
  (6, 1),
  (7, 1),
  (8, 1),
  (11, 1),
  (14, 1),
  (17, 1),
  (1, 2);

-- ---------- Mã giảm giá (ví dụ, theo các dòng khuyến mãi trên web) ----------
INSERT INTO coupons (code, description, discount_type, discount_value, max_discount, min_order_value, first_order_only, is_active) VALUES
  ('FIRST35', 'Giảm 35% cho đơn hàng đầu tiên',                      'percent', 35, NULL, 0,       1, 1),
  ('NEWS10',  'Giảm 10% lần mua tiếp theo cho người đăng ký bản tin', 'percent', 10, NULL, 0,       0, 1),
  ('BIG25',   'Giảm 25% cho đơn online từ 1.000.000đ',                'percent', 25, NULL, 1000000, 0, 1);

-- ---------- Spa: dịch vụ + bảng giá theo cân nặng (đúng ảnh CtAOPls.jpeg) ----------
INSERT INTO spa_services (id, name, description, pricing_type, price_from, price_to, sort_order) VALUES
  (1, 'Tắm - Vệ sinh',  'Bath — quy trình tắm vệ sinh 12 bước',                 'by_weight', NULL,   NULL,   1),
  (2, 'Tắm - Cạo lông', 'Bath - Shave',                                          'by_weight', NULL,   NULL,   2),
  (3, 'Tắm - Tạo kiểu', 'Grooming',                                              'by_weight', NULL,   NULL,   3),
  (4, 'Nhuộm',          'Dye — giá tùy trọng lượng và kiểu nhuộm',              'range',     250000, 500000, 4);

INSERT INTO spa_service_prices (service_id, weight_over_kg, weight_up_to_kg, price) VALUES
  (1, 0.0, 1.0, 150000),
  (1, 1.0, 3.0, 190000),
  (1, 3.0, 5.0, 220000),
  (1, 5.0, 8.0, 260000),
  (1, 8.0, 11.0, 300000),
  (1, 11.0, 15.0, 340000),
  (1, 15.0, 20.0, 390000),
  (1, 20.0, 25.0, 440000),
  (1, 25.0, 30.0, 490000),
  (1, 30.0, 35.0, 540000),
  (2, 0.0, 1.0, 220000),
  (2, 1.0, 3.0, 250000),
  (2, 3.0, 5.0, 280000),
  (2, 5.0, 8.0, 320000),
  (2, 8.0, 11.0, 360000),
  (2, 11.0, 15.0, 390000),
  (2, 15.0, 20.0, 440000),
  (2, 20.0, 25.0, 490000),
  (2, 25.0, 30.0, 540000),
  (2, 30.0, 35.0, 590000),
  (3, 0.0, 1.0, 290000),
  (3, 1.0, 3.0, 340000),
  (3, 3.0, 5.0, 390000),
  (3, 5.0, 8.0, 440000),
  (3, 8.0, 11.0, 490000),
  (3, 11.0, 15.0, 540000),
  (3, 15.0, 20.0, 590000),
  (3, 20.0, 25.0, 640000),
  (3, 25.0, 30.0, 690000),
  (3, 30.0, 35.0, 740000);

-- ---------- Tin tức ----------
INSERT INTO blog_categories (id, name, slug) VALUES
  (1, 'Mẹo vặt', 'meo-vat'), (2, 'Thời trang', 'thoi-trang'), (3, 'Lối sống', 'loi-song'), (4, 'Chuyện ngắn', 'chuyen-ngan'), (5, 'Kiến thức', 'kien-thuc');

-- Chỉ bài 1 có nội dung trong blogchitiet.html (ở đây chỉ lấy đoạn mở đầu); 5 bài còn lại để 'draft' chờ nhập nội dung.
INSERT INTO blog_posts (id, category_id, title, slug, summary, content, status, published_at) VALUES
  (1, 5, '10 dấu hiệu cho thấy thú cưng của bạn đang bị stress', '10-dau-hieu-cho-thay-thu-cung-cua-ban-dang-bi-stress', 'Với tư cách là người nuôi thú cưng, chúng ta thường tập trung vào việc làm chúng luôn vui vẻ, nhưng bạn có biết những bạn nhỏ này cũng có thể bị stress giống như con người không ? Thú cưng bị stress có thể dẫn đến những hành vi không mong muốn, thậm chí là gặp các vấn đề sức khỏe, và đôi khi rất khó để nhận ra. Bằng cách tìm hiểu các dấu hiệu stress phổ biến ở thú cưng và hiểu cách giải quyết, chúng ta có thể giúp thú cưng của mình cảm thấy thoải mái, an toàn hơn.', 'Với tư cách là người nuôi thú cưng, chúng ta thường tập trung vào việc làm chúng luôn vui vẻ, nhưng bạn có biết những bạn nhỏ này cũng có thể bị stress giống như con người không ? Thú cưng bị stress có thể dẫn đến những hành vi không mong muốn, thậm chí là gặp các vấn đề sức khỏe, và đôi khi rất khó để nhận ra. Bằng cách tìm hiểu các dấu hiệu stress phổ biến ở thú cưng và hiểu cách giải quyết, chúng ta có thể giúp thú cưng của mình cảm thấy thoải mái, an toàn hơn.', 'published', NOW()),
  (2, 5, 'Những bí mật sức khỏe của thú cưng có thể bạn chưa biết', 'nhung-bi-mat-suc-khoe-cua-thu-cung-co-the-ban-chua-biet', NULL, '', 'draft', NULL),
  (3, 3, 'Nuôi thú cưng cần chủ bị những gì ?', 'nuoi-thu-cung-can-chu-bi-nhung-gi', NULL, '', 'draft', NULL),
  (4, 3, 'Top 5 mèo cảnh được ưa chuộng ở Việt Nam', 'top-5-meo-canh-duoc-ua-chuong-o-viet-nam', NULL, '', 'draft', NULL),
  (5, 5, 'Cách chăm sóc chó Poodle: Hướng dẫn chi tiết', 'cach-cham-soc-cho-poodle-huong-dan-chi-tiet', NULL, '', 'draft', NULL),
  (6, 3, 'Nhà có trẻ con nên chọn mèo nào ?', 'nha-co-tre-con-nen-chon-meo-nao', NULL, '', 'draft', NULL);

INSERT INTO post_tags (post_id, tag_id) VALUES (1, 12), (2, 12);

-- ---------- FAQ (lấy từ FAQ.html) ----------
INSERT INTO faqs (section_title, section_order, question, answer, sort_order) VALUES
  ('Đặt hàng', 1, 'Khi nào tôi nhận được đơn hàng?', 'Khi đặt hàng, ngày giao hàng sẽ được ghi rõ. Sau khi đơn hàng được đặt, thời gian giao hàng cũng sẽ được ghi rõ trên xác nhận đơn hàng. Do đó, trong quá trình đặt hàng, ngày giao hàng trên trang web không bao giờ khác với ngày giao hàng trên xác nhận đơn hàng.', 1),
  ('Đặt hàng', 1, 'Tôi hiện thấy thời gian giao hàng (một phần) đơn hàng của mình bị kéo dài. Tôi có thể hủy đơn hàng bằng cách nào?', 'Nếu đơn hàng có thời gian giao hàng lâu hơn so với thời gian bạn đã thấy trước đó, tất nhiên bạn có thể hủy (một phần) đơn hàng. Để làm điều này, bạn có thể liên hệ với bộ phận chăm sóc khách hàng của chúng tôi. Họ sẽ hủy đơn hàng cho bạn. Số tiền mua hàng sẽ được hoàn lại vào tài khoản ngân hàng của bạn trong vòng hai ngày làm việc. Khi đơn hàng đã được giao, bạn không thể hủy đơn hàng nữa.', 2),
  ('Đặt hàng', 1, 'Khi nào tôi sẽ nhận được hóa đơn cho đơn hàng của mình?', 'Sau khi thanh toán, bạn sẽ không tự động nhận được hóa đơn. Nếu muốn nhận hóa đơn, bạn có thể thực hiện theo hai cách. Cách thứ nhất là thông qua tài khoản của bạn tại cửa hàng. Khi đăng nhập vào tài khoản, bạn có thể xem đơn hàng và tải hóa đơn xuống.', 3),
  ('Lô hàng', 2, 'Theo dõi đơn hàng', 'Nhập mã đơn hàng để theo dõi hành trình vận chuyển nhanh chóng và chính xác. Hệ thống sẽ cập nhật trạng thái theo thời gian thực, giúp bạn biết được đơn đang ở giai đoạn nào và thời gian dự kiến giao hàng.', 1),
  ('Lô hàng', 2, 'Chính sách giao hàng', 'Chúng tôi cam kết giao hàng nhanh và an toàn. Dưới đây là các điều khoản cơ bản về vận chuyển bạn nên biết. Nội dung chính: Thời gian xử lý đơn: 1–2 ngày làm việc để xác nhận và chuẩn bị hàng (không tính thứ Bảy, Chủ Nhật và ngày lễ). Thời gian giao dự kiến: Tùy khu vực — trong nội thành: 1–3 ngày; tỉnh thành khác: 3–7 ngày. Khu vực hỗ trợ: Hiện tại chúng tôi giao trong phạm vi toàn quốc. Một số khu vực hẻo lánh có thể kéo dài thời gian giao. Giao lại khi thất bại: Nếu giao không thành công, shipper sẽ cố gắng liên hệ và sắp xếp giao lại trong 1–2 ngày tiếp theo.', 2),
  ('Lô hàng', 2, 'Phí vận chuyển', 'Minh bạch và rõ ràng về chi phí vận chuyển giúp khách hàng an tâm khi đặt hàng. Chính sách mẫu: Miễn phí giao hàng cho đơn từ 500.000₫ trở lên. Tính phí theo khu vực: Các tỉnh thành xa trung tâm có thể áp dụng phí bổ sung. Phí cố định theo đơn: Nếu khách hàng chọn phương thức giao nhanh/khẩn cấp sẽ áp dụng phụ phí.', 3),
  ('Đặt hàng', 3, 'Khi nào tôi nhận được đơn hàng?', 'Khi đặt hàng, ngày giao hàng sẽ được ghi rõ. Sau khi đơn hàng được đặt, thời gian giao hàng cũng sẽ được ghi rõ trên xác nhận đơn hàng. Do đó, trong quá trình đặt hàng, ngày giao hàng trên trang web không bao giờ khác với ngày giao hàng trên xác nhận đơn hàng.', 1),
  ('Đặt hàng', 3, 'Tôi hiện thấy thời gian giao hàng (một phần) đơn hàng của mình bị kéo dài. Tôi có thể hủy đơn hàng bằng cách nào?', 'Nếu đơn hàng có thời gian giao hàng lâu hơn so với thời gian bạn đã thấy trước đó, tất nhiên bạn có thể hủy (một phần) đơn hàng. Để làm điều này, bạn có thể liên hệ với bộ phận chăm sóc khách hàng của chúng tôi. Họ sẽ hủy đơn hàng cho bạn. Số tiền mua hàng sẽ được hoàn lại vào tài khoản ngân hàng của bạn trong vòng hai ngày làm việc. Khi đơn hàng đã được giao, bạn không thể hủy đơn hàng nữa.', 2),
  ('Đặt hàng', 3, 'Khi nào tôi sẽ nhận được hóa đơn cho đơn hàng của mình?', 'Sau khi thanh toán, bạn sẽ không tự động nhận được hóa đơn. Nếu muốn nhận hóa đơn, bạn có thể thực hiện theo hai cách. Cách thứ nhất là thông qua tài khoản của bạn tại cửa hàng. Khi đăng nhập vào tài khoản, bạn có thể xem đơn hàng và tải hóa đơn xuống.', 3),
  ('Đổi trả – Đổi hàng – Khiếu nại', 4, 'Chính sách đổi trả', 'Chúng tôi hỗ trợ đổi trả cho các sản phẩm bị lỗi kỹ thuật, hư hỏng trong quá trình vận chuyển hoặc không đúng như mô tả. Thời hạn đổi trả: trong vòng 7 ngày kể từ khi khách nhận hàng.', 1),
  ('Đổi trả – Đổi hàng – Khiếu nại', 4, 'Đổi hàng', 'Nếu khách muốn đổi sang kích thước, màu sắc hoặc sản phẩm khác, chúng tôi hỗ trợ với các điều kiện: Sản phẩm mới 100%, chưa qua sử dụng hoặc giặt/rửa. Phí vận chuyển hai chiều do khách hàng chi trả (trừ trường hợp lỗi do nhà bán). Hỗ trợ đổi tối đa 1 lần cho mỗi đơn hàng. Thời gian xử lý: 2–5 ngày làm việc sau khi nhận được sản phẩm.', 2),
  ('Đổi trả – Đổi hàng – Khiếu nại', 4, 'Khiếu nại', 'Chúng tôi luôn sẵn sàng tiếp nhận mọi phản ánh liên quan đến chất lượng sản phẩm, dịch vụ giao hàng hoặc thái độ phục vụ. Thời gian tiếp nhận khiếu nại: Trong vòng 7–14 ngày kể từ khi khách nhận hàng. Thời gian phản hồi: Tối đa 24–48 giờ làm việc kể từ khi tiếp nhận yêu cầu. Các kênh hỗ trợ: Hotline Email hỗ trợ Fanpage hoặc chat trực tuyến Mục hỗ trợ ngay trên website', 3);

-- ---------- Tài khoản admin mẫu ----------
-- !!! MẬT KHẨU TẠM: Admin@12345  — chỉ để thử trên máy local. ĐỔI NGAY (hoặc xóa tài khoản này)
-- !!! trước khi đưa web lên mạng. Cột password_hash là bcrypt ($2y$, tương thích password_verify của PHP / Laravel).
INSERT INTO users (id, username, email, password_hash, full_name, role) VALUES
  (1, 'admin', 'admin@petio.local', '$2y$10$sKWU/sCsKgIRrwAdoJd3JOWNX9kUH8pG3BkFiEcKSrVSs46OP2iL6', 'Quản trị viên', 'admin');

-- Gán bài viết đã có cho admin
UPDATE blog_posts SET author_id = 1, updated_by = 1;

-- ---------- Banner trang chủ (theo index.html) ----------
-- Ghi chú: title dùng \n để xuống dòng (MySQL đổi thành ký tự xuống dòng khi nạp).
INSERT INTO banners (position, title, subtitle, image_url, alt_text, button_text, link_url, sort_order, updated_by) VALUES
  ('home_hero', 'ĐỒ ĂN THÚ CƯNG\nCHẤT LƯỢNG CAO', 'Giảm giá lên đến 40% hôm nay', 'assets/file_anh/hero-banner.jpg', 'Thú cưng và đồ ăn Petio', 'Mua ngay', 'html/shop.html', 1, 1),

  ('home_category', 'Thức Ăn Cho Mèo',   NULL, 'assets/file_anh/cate-8.jpg', 'Thức ăn cho mèo',   NULL, 'html/shop.html?cat=thuc-an', 1, 1),
  ('home_category', 'Đồ Chơi Cho Mèo',   NULL, 'assets/file_anh/cate-7.jpg', 'Đồ chơi cho mèo',   NULL, 'html/shop.html?cat=do-choi', 2, 1),
  ('home_category', 'Thức Ăn Cho Chó',   NULL, 'assets/file_anh/cate-5.jpg', 'Thức ăn cho chó',   NULL, 'html/shop.html?cat=thuc-an', 3, 1),
  ('home_category', 'Đồ Chơi Cho Chó',   NULL, 'assets/file_anh/cate-6.jpg', 'Đồ chơi cho chó',   NULL, 'html/shop.html?cat=do-choi', 4, 1),
  ('home_category', 'Thực Phẩm Bổ Sung', NULL, 'assets/file_anh/cate-9.jpg', 'Thực phẩm bổ sung', NULL, 'html/shop.html?cat=thuoc',   5, 1),

  ('home_promo', 'Ưu đãi cho mùa\nhè nóng nực',           'Chỉ dành cho đơn hàng Online', 'assets/file_anh/banner-1.jpg', 'Ưu đãi mùa hè',        'Mua ngay', 'html/shop.html', 1, 1),
  ('home_promo', 'Dành cho người bạn\nthật sự yêu thương', 'Quà tặng & chăm sóc',          'assets/file_anh/banner-2.jpg', 'Quà tặng và chăm sóc', 'Mua ngay', 'html/spa.html',  2, 1),
  ('home_promo', 'Mới ra mắt\nnăm nay!',                   'Thương hiệu bạn sẽ yêu thích', 'assets/file_anh/banner-3.jpg', 'Thương hiệu mới',      'Mua ngay', 'html/shop.html', 3, 1);
