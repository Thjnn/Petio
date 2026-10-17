<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Dữ liệu mẫu lấy từ chính nội dung website cũ (17 sản phẩm ở shop.html, bảng giá spa,
 * FAQ, bài viết, banner trang chủ). Chạy: php artisan migrate:fresh --seed
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // ---------- Tài khoản admin mẫu ----------
        // MẬT KHẨU TẠM: Admin@12345 — chỉ để chạy thử trên máy. Đổi ngay trước khi đưa web lên mạng.
        DB::table('users')->insert([
            'id' => 1, 'username' => 'admin', 'email' => 'admin@petio.local',
            'password' => Hash::make('Admin@12345'), 'full_name' => 'Quản trị viên', 'role' => 'admin',
            'created_at' => $now, 'updated_at' => $now,
        ]);

        // Khách hàng mẫu để thử đăng nhập / đặt hàng (mật khẩu tạm: Khach@12345)
        DB::table('users')->insert([
            'id' => 2, 'username' => 'khachhang', 'email' => 'khachhang@petio.local', 'phone' => '0900000001',
            'password' => Hash::make('Khach@12345'), 'full_name' => 'Nguyễn Văn Khách', 'role' => 'customer',
            'created_at' => $now, 'updated_at' => $now,
        ]);

        // ---------- Danh mục ----------
        $categories = [
            [1, null, 'Thức ăn thú cưng', 'thuc-an'],
            [2, null, 'Đồ chơi thú cưng', 'do-choi'],
            [3, null, 'Phụ kiện, chuồng, nệm', 'phu-kien'],
            [4, null, 'Vệ sinh - chăm sóc', 've-sinh'],
            [5, null, 'Thuốc và thực phẩm chức năng', 'thuoc'],
            [6, 1, 'Thức ăn hạt', 'thuc-an-hat'],
            [7, 1, 'Pate & nước sốt', 'pate-nuoc-sot'],
            [8, 1, 'Sữa', 'sua'],
            [9, 1, 'Bánh thưởng & xương gặm', 'banh-thuong-xuong-gam'],
            [10, 3, 'Chuồng, nhà, balo, quây', 'chuong-nha-balo-quay'],
            [11, 3, 'Đệm & nệm', 'dem-nem'],
            [12, 3, 'Vòng cổ, dây dắt, rọ mõm', 'vong-co-day-dat-ro-mom'],
            [13, 3, 'Quần áo', 'quan-ao'],
            [14, 3, 'Bình nước, bát ăn, lược', 'binh-nuoc-bat-an-luoc'],
            [15, 4, 'Bỉm, tã, khay vệ sinh', 'bim-ta-khay-ve-sinh'],
            [16, 4, 'Sữa tắm, khăn tắm, nước hoa', 'sua-tam-khan-tam-nuoc-hoa'],
            [17, 4, 'Cát vệ sinh', 'cat-ve-sinh'],
            [18, 5, 'Thuốc thú y', 'thuoc-thu-y'],
            [19, 5, 'Thực phẩm chức năng', 'thuc-pham-chuc-nang'],
        ];
        foreach ($categories as $i => [$id, $parent, $name, $slug]) {
            DB::table('categories')->insert([
                'id' => $id, 'parent_id' => $parent, 'name' => $name, 'slug' => $slug,
                'sort_order' => $i + 1, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // ---------- Thương hiệu ----------
        foreach ([[1, 'Endogard'], [2, 'HUG'], [3, 'Jolly Cat'], [4, 'Pedigree'], [5, 'Pet Own'], [6, 'Zoi Dog']] as [$id, $name]) {
            DB::table('brands')->insert(['id' => $id, 'name' => $name, 'slug' => str($name)->slug()]);
        }

        // ---------- Tag ----------
        $tags = ['Hot', 'Trend', 'Baber', 'Baby Needs', 'Beauty', 'Cosmetic', 'Ear Care', 'Electric',
            'Fashion', 'Food', 'Jwerly', 'Medical', 'Mimimal', 'Organic', 'Simple', 'Sport'];
        foreach ($tags as $i => $name) {
            DB::table('tags')->insert(['id' => $i + 1, 'name' => $name, 'slug' => str($name)->slug()]);
        }

        // ---------- Sản phẩm + biến thể mặc định ----------
        // [id, danh mục, hãng, tên, loài, ảnh (trong assets/file_anh/), sao, lượt đánh giá, nổi bật, giá, giá gốc]
        $dog = 'Chó/';
        $cat = 'Mèo/';
        $products = [
            [1, 6, 4, 'Hạt Pedigree vị gà và trứng 400gr', 'dog', $dog.'Thức Ăn Và Thực Phẩm Dinh Dưỡng/Thức Ăn Hạt/Hat-Pedigree-Puppy-vi-ga-va-trung-400g.jpg', 5, 100, 1, 57000, 63000],
            [2, 6, 6, 'Hạt Zoi Dog thức ăn cho chó 1kg', 'dog', $dog.'Thức Ăn Và Thực Phẩm Dinh Dưỡng/Thức Ăn Hạt/Hat-Zoi-Dog-thuc-an-cho-cho-20kg.jpg', 4, 50, 1, 35000, null],
            [3, 2, null, 'Lốp xe cao su cho chó mèo', 'both', $dog.'Đồ Dùng & Đồ Chơi & Phụ Kiện/Đồ chơi cho chó/ezgif-4-c55d00de52.jpg', 5, 1, 1, 17000, null],
            [4, 2, null, 'Đồ chơi dây thừng hình cục xương', 'dog', $dog.'Đồ Dùng & Đồ Chơi & Phụ Kiện/Đồ chơi cho chó/40ee7d4948b2777f8449a135c62fe00c.jpg', 5, 1, 1, 47000, 56000],
            [5, 8, null, 'Sữa bột colostrum chó mèo hộp 100g', 'both', $cat.'Thức Ăn Và Thực Phẩm Dinh Dưỡng - Copy/Sữa Cho Mèo/2d509a3bc77f520d07ea46f83bcbe567.jpg', 5, 10, 1, 36000, 45000],
            [6, 8, 5, 'Sữa tươi Pet Own cho chó mèo 1 lít, nhập khẩu từ Úc', 'both', $cat.'Thức Ăn Và Thực Phẩm Dinh Dưỡng - Copy/Sữa Cho Mèo/632daf3b28db898b1cbfd071efc01266.jpg', 5, 1000, 1, 84000, 120000],
            [7, 10, null, 'Nhà cây - trụ cào móng cho mèo', 'cat', $cat.'Chuồng, Chậu, Balo Và Túi Vận Chuyển/Chuồng mèo & Chậu Cát Mèo/Nha-cay-tru-cao-mong-cho-meo-1848-4.jpg', 5, 100, 1, 3000000, null],
            [8, 10, null, 'Chuồng tầng bằng sắt cho mèo', 'cat', $cat.'Chuồng, Chậu, Balo Và Túi Vận Chuyển/Chuồng mèo & Chậu Cát Mèo/Chuong-tang-bang-sat-cho-meo.jpg', 5, 1, 1, 2700000, 3000000],
            [9, 9, null, 'Bánh thưởng và xương gặm cho chó', 'dog', $dog.'Thức Ăn Và Thực Phẩm Dinh Dưỡng/Bánh Thưởng Và Xương Gặm/5d2278b966c99a97c3d8-2675-1247x1496.jpg', 4, 32, 0, 45000, null],
            [10, 7, 2, 'Pate HUG cho chó mèo', 'both', $dog.'Thức Ăn Và Thực Phẩm Dinh Dưỡng/Pate & Nước sốt/ezgif-2-b50846658d.jpg', 5, 10, 0, 50000, null],
            [11, 2, null, 'Chuột đuôi bi đồ chơi cho mèo', 'cat', $cat.'Đồ Dùng & Đồ Chơi & Phụ Kiện - Copy/Đồ chơi cho mèo/chuot-duoi-bi.jpg', 4, 18, 0, 25000, null],
            [12, 11, null, 'Nệm điều hòa mát cho chó mèo', 'both', $dog.'Chuồng, nhà, balo, Quây, Đệm/Đệm & Nệm/nem-dieu-hoa.jpg', 4, 26, 0, 180000, 220000],
            [13, 11, null, 'Nệm nhung chữ nhật cho chó mèo', 'both', $dog.'Chuồng, nhà, balo, Quây, Đệm/Đệm & Nệm/nem-chu-nhat-nhung-cho-cho-meo1-min.png', 5, 41, 0, 150000, null],
            [14, 12, null, 'Vòng cổ và dây dắt cho chó', 'dog', $dog.'Đồ Dùng & Đồ Chơi & Phụ Kiện/Vòng cổ, Dây Dắt Và Rọ Mõm/650d8ec958409371787eb9f658975e5c.jpg', 4, 12, 0, 65000, null],
            [15, 17, 3, 'Cát vệ sinh cho mèo Jolly Cat', 'cat', $cat.'Vệ Sinh Và Chăm Sóc - Copy/Cát vệ sinh mèo/Cat-Jolly-Cat.jpg', 5, 64, 0, 90000, null],
            [16, 16, null, 'Sữa tắm khử mùi cho chó', 'dog', $dog.'Vệ Sinh Và Chăm Sóc/Sữa Tắm & Khăn Tắm & Nước Hoa/1476c0b0458948b7a6b3a668b98ce5e3.jpg', 4, 23, 0, 120000, 140000],
            [17, 18, 1, 'Thuốc tẩy giun Endogard cho chó', 'dog', $dog.'Thuốc Và Thực Phẩm Chức Năng/Thuốc Thú Y/Endogard_2019.jpg', 5, 87, 0, 75000, null],
        ];
        foreach ($products as [$id, $catId, $brandId, $name, $pet, $img, $stars, $reviews, $featured, $price, $oldPrice]) {
            DB::table('products')->insert([
                'id' => $id, 'category_id' => $catId, 'brand_id' => $brandId, 'name' => $name,
                'slug' => str($name)->slug(), 'pet_type' => $pet, 'thumbnail_url' => 'assets/file_anh/'.$img,
                'rating_avg' => $stars, 'rating_count' => $reviews, 'is_featured' => $featured,
                'created_at' => $now, 'updated_at' => $now,
            ]);
            DB::table('product_variants')->insert([
                'product_id' => $id, 'sku' => sprintf('PT-%03d', $id), 'name' => $id === 1 ? '400g' : 'Mặc định',
                'price' => $price, 'old_price' => $oldPrice, 'stock_qty' => 100, 'is_default' => true, 'sort_order' => 1,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }

        // Pedigree Puppy có thêm size 1.3kg + mô tả chi tiết (lấy từ product.html cũ)
        DB::table('product_variants')->insert([
            'product_id' => 1, 'sku' => 'PT-001-1K3', 'name' => '1.3kg', 'price' => 133000,
            'stock_qty' => 100, 'sort_order' => 2, 'created_at' => $now, 'updated_at' => $now,
        ]);
        DB::table('products')->where('id', 1)->update([
            'short_description' => 'Thức ăn dạng hạt Pedigree Puppy vị gà sốt trứng đem đến những bữa ăn đầy đủ chất dinh dưỡng, cùng hương vị ngon miệng kích thích các bé ăn uống.',
            'description' => file_get_contents(__DIR__.'/content/product-1.html'),
        ]);

        // Badge "Hot" trên thẻ sản phẩm (tag 1); Pedigree còn có "Trend" (tag 2)
        foreach ([1, 3, 4, 5, 6, 7, 8, 11, 14, 17] as $productId) {
            DB::table('product_tags')->insert(['product_id' => $productId, 'tag_id' => 1]);
        }
        DB::table('product_tags')->insert(['product_id' => 1, 'tag_id' => 2]);

        // ---------- Mã giảm giá (theo các dòng khuyến mãi trên web) ----------
        DB::table('coupons')->insert([
            ['code' => 'FIRST35', 'description' => 'Giảm 35% cho đơn hàng đầu tiên', 'discount_type' => 'percent', 'discount_value' => 35, 'min_order_value' => 0, 'first_order_only' => true, 'created_at' => $now],
            ['code' => 'NEWS10', 'description' => 'Giảm 10% lần mua tiếp theo cho người đăng ký bản tin', 'discount_type' => 'percent', 'discount_value' => 10, 'min_order_value' => 0, 'first_order_only' => false, 'created_at' => $now],
            ['code' => 'BIG25', 'description' => 'Giảm 25% cho đơn online từ 1.000.000đ', 'discount_type' => 'percent', 'discount_value' => 25, 'min_order_value' => 1000000, 'first_order_only' => false, 'created_at' => $now],
        ]);

        // ---------- Spa: dịch vụ + bảng giá theo cân nặng (ảnh CtAOPls.jpeg) ----------
        DB::table('spa_services')->insert([
            ['id' => 1, 'name' => 'Tắm - Vệ sinh', 'description' => 'Bath — quy trình tắm vệ sinh 12 bước', 'pricing_type' => 'by_weight', 'price_from' => null, 'price_to' => null, 'sort_order' => 1],
            ['id' => 2, 'name' => 'Tắm - Cạo lông', 'description' => 'Bath - Shave', 'pricing_type' => 'by_weight', 'price_from' => null, 'price_to' => null, 'sort_order' => 2],
            ['id' => 3, 'name' => 'Tắm - Tạo kiểu', 'description' => 'Grooming', 'pricing_type' => 'by_weight', 'price_from' => null, 'price_to' => null, 'sort_order' => 3],
            ['id' => 4, 'name' => 'Nhuộm', 'description' => 'Dye — giá tùy trọng lượng và kiểu nhuộm', 'pricing_type' => 'range', 'price_from' => 250000, 'price_to' => 500000, 'sort_order' => 4],
        ]);
        $bands = [[0, 1], [1, 3], [3, 5], [5, 8], [8, 11], [11, 15], [15, 20], [20, 25], [25, 30], [30, 35]];
        $prices = [
            1 => [150000, 190000, 220000, 260000, 300000, 340000, 390000, 440000, 490000, 540000],
            2 => [220000, 250000, 280000, 320000, 360000, 390000, 440000, 490000, 540000, 590000],
            3 => [290000, 340000, 390000, 440000, 490000, 540000, 590000, 640000, 690000, 740000],
        ];
        foreach ($prices as $serviceId => $row) {
            foreach ($bands as $i => [$over, $upTo]) {
                DB::table('spa_service_prices')->insert([
                    'service_id' => $serviceId, 'weight_over_kg' => $over, 'weight_up_to_kg' => $upTo, 'price' => $row[$i],
                ]);
            }
        }

        // ---------- Tin tức ----------
        foreach ([[1, 'Mẹo vặt'], [2, 'Thời trang'], [3, 'Lối sống'], [4, 'Chuyện ngắn'], [5, 'Kiến thức']] as [$id, $name]) {
            DB::table('blog_categories')->insert(['id' => $id, 'name' => $name, 'slug' => str($name)->slug()]);
        }

        // Chỉ bài 1 có nội dung (blogchitiet.html cũ); 5 bài còn lại để 'draft' chờ nhập nội dung.
        $posts = [
            [1, 5, '10 dấu hiệu cho thấy thú cưng của bạn đang bị stress', '2c4b931519752f7b1a81fdc5591f18e9.jpg'],
            [2, 5, 'Những bí mật sức khỏe của thú cưng có thể bạn chưa biết', '28663722373b36780f79cea9fd3ff5e1.jpg'],
            [3, 3, 'Nuôi thú cưng cần chuẩn bị những gì?', 'cho-phoc-soc-mini-mau-trang-ma-PS3003-600x800.jpg'],
            [4, 3, 'Top 5 mèo cảnh được ưa chuộng ở Việt Nam', 'ml-768x576.jpg'],
            [5, 5, 'Cách chăm sóc chó Poodle: Hướng dẫn chi tiết', 'cho-poodle-trang-truong-thanh-ma-PD2985-768x768.jpg'],
            [6, 3, 'Nhà có trẻ con nên chọn mèo nào?', '2e62a80625f788a5afa8a263fec26c13.jpg'],
        ];
        foreach ($posts as [$id, $catId, $title, $img]) {
            $published = $id === 1;
            DB::table('blog_posts')->insert([
                'id' => $id, 'category_id' => $catId, 'author_id' => 1, 'updated_by' => 1,
                'title' => $title, 'slug' => str($title)->slug(), 'thumbnail_url' => 'assets/file_anh/'.$img,
                'summary' => $published ? 'Với tư cách là người nuôi thú cưng, chúng ta thường tập trung vào việc làm chúng luôn vui vẻ, nhưng bạn có biết những bạn nhỏ này cũng có thể bị stress giống như con người không?' : null,
                'content' => $published ? file_get_contents(__DIR__.'/content/post-1.html') : '',
                'status' => $published ? 'published' : 'draft', 'published_at' => $published ? $now : null,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
        DB::table('post_tags')->insert([['post_id' => 1, 'tag_id' => 12], ['post_id' => 2, 'tag_id' => 12]]);

        // ---------- FAQ (FAQ.html cũ; bỏ mục "03. Đặt hàng" vì chép trùng mục 01) ----------
        $faqs = [
            ['Đặt hàng', 1, [
                ['Khi nào tôi nhận được đơn hàng?', 'Khi đặt hàng, ngày giao hàng sẽ được ghi rõ. Sau khi đơn hàng được đặt, thời gian giao hàng cũng sẽ được ghi rõ trên xác nhận đơn hàng. Do đó, trong quá trình đặt hàng, ngày giao hàng trên trang web không bao giờ khác với ngày giao hàng trên xác nhận đơn hàng.'],
                ['Tôi hiện thấy thời gian giao hàng (một phần) đơn hàng của mình bị kéo dài. Tôi có thể hủy đơn hàng bằng cách nào?', 'Nếu đơn hàng có thời gian giao hàng lâu hơn so với thời gian bạn đã thấy trước đó, tất nhiên bạn có thể hủy (một phần) đơn hàng. Để làm điều này, bạn có thể liên hệ với bộ phận chăm sóc khách hàng của chúng tôi. Họ sẽ hủy đơn hàng cho bạn. Số tiền mua hàng sẽ được hoàn lại vào tài khoản ngân hàng của bạn trong vòng hai ngày làm việc. Khi đơn hàng đã được giao, bạn không thể hủy đơn hàng nữa.'],
                ['Khi nào tôi sẽ nhận được hóa đơn cho đơn hàng của mình?', 'Sau khi thanh toán, bạn sẽ không tự động nhận được hóa đơn. Nếu muốn nhận hóa đơn, bạn có thể thực hiện theo hai cách. Cách thứ nhất là thông qua tài khoản của bạn tại cửa hàng. Khi đăng nhập vào tài khoản, bạn có thể xem đơn hàng và tải hóa đơn xuống.'],
            ]],
            ['Lô hàng', 2, [
                ['Theo dõi đơn hàng', 'Nhập mã đơn hàng để theo dõi hành trình vận chuyển nhanh chóng và chính xác. Hệ thống sẽ cập nhật trạng thái theo thời gian thực, giúp bạn biết được đơn đang ở giai đoạn nào và thời gian dự kiến giao hàng.'],
                ['Chính sách giao hàng', 'Chúng tôi cam kết giao hàng nhanh và an toàn. Thời gian xử lý đơn: 1–2 ngày làm việc để xác nhận và chuẩn bị hàng (không tính thứ Bảy, Chủ Nhật và ngày lễ). Thời gian giao dự kiến: trong nội thành 1–3 ngày; tỉnh thành khác 3–7 ngày. Chúng tôi giao trong phạm vi toàn quốc; một số khu vực hẻo lánh có thể lâu hơn. Nếu giao không thành công, shipper sẽ liên hệ và sắp xếp giao lại trong 1–2 ngày tiếp theo.'],
                ['Phí vận chuyển', 'Miễn phí giao hàng cho đơn từ 500.000₫ trở lên. Các tỉnh thành xa trung tâm có thể áp dụng phí bổ sung. Nếu khách hàng chọn giao nhanh/khẩn cấp sẽ áp dụng phụ phí.'],
            ]],
            ['Đổi trả – Đổi hàng – Khiếu nại', 3, [
                ['Chính sách đổi trả', 'Chúng tôi hỗ trợ đổi trả cho các sản phẩm bị lỗi kỹ thuật, hư hỏng trong quá trình vận chuyển hoặc không đúng như mô tả. Thời hạn đổi trả: trong vòng 7 ngày kể từ khi khách nhận hàng.'],
                ['Đổi hàng', 'Nếu khách muốn đổi sang kích thước, màu sắc hoặc sản phẩm khác, chúng tôi hỗ trợ với các điều kiện: sản phẩm mới 100%, chưa qua sử dụng hoặc giặt/rửa; phí vận chuyển hai chiều do khách hàng chi trả (trừ trường hợp lỗi do nhà bán); hỗ trợ đổi tối đa 1 lần cho mỗi đơn hàng. Thời gian xử lý: 2–5 ngày làm việc sau khi nhận được sản phẩm.'],
                ['Khiếu nại', 'Chúng tôi luôn sẵn sàng tiếp nhận mọi phản ánh liên quan đến chất lượng sản phẩm, dịch vụ giao hàng hoặc thái độ phục vụ. Thời gian tiếp nhận khiếu nại: trong vòng 7–14 ngày kể từ khi khách nhận hàng. Thời gian phản hồi: tối đa 24–48 giờ làm việc. Kênh hỗ trợ: hotline, email, fanpage hoặc mục hỗ trợ trên website.'],
            ]],
        ];
        foreach ($faqs as [$section, $order, $items]) {
            foreach ($items as $i => [$question, $answer]) {
                DB::table('faqs')->insert([
                    'section_title' => $section, 'section_order' => $order,
                    'question' => $question, 'answer' => $answer, 'sort_order' => $i + 1,
                ]);
            }
        }

        // ---------- Banner trang chủ ----------
        $banner = fn ($position, $title, $subtitle, $img, $alt, $button, $link, $sort) => [
            'position' => $position, 'title' => $title, 'subtitle' => $subtitle, 'image_url' => 'assets/file_anh/'.$img,
            'alt_text' => $alt, 'button_text' => $button, 'link_url' => $link, 'sort_order' => $sort,
            'updated_by' => 1, 'created_at' => $now, 'updated_at' => $now,
        ];
        DB::table('banners')->insert([
            $banner('home_hero', "ĐỒ ĂN THÚ CƯNG\nCHẤT LƯỢNG CAO", 'Giảm giá lên đến 40% hôm nay', 'hero-banner.jpg', 'Thú cưng và đồ ăn Petio', 'Mua ngay', 'shop', 1),
            $banner('home_category', 'Thức Ăn Cho Mèo', null, 'cate-8.jpg', 'Thức ăn cho mèo', null, 'shop?cat=thuc-an', 1),
            $banner('home_category', 'Đồ Chơi Cho Mèo', null, 'cate-7.jpg', 'Đồ chơi cho mèo', null, 'shop?cat=do-choi', 2),
            $banner('home_category', 'Thức Ăn Cho Chó', null, 'cate-5.jpg', 'Thức ăn cho chó', null, 'shop?cat=thuc-an', 3),
            $banner('home_category', 'Đồ Chơi Cho Chó', null, 'cate-6.jpg', 'Đồ chơi cho chó', null, 'shop?cat=do-choi', 4),
            $banner('home_category', 'Thực Phẩm Bổ Sung', null, 'cate-9.jpg', 'Thực phẩm bổ sung', null, 'shop?cat=thuoc', 5),
            $banner('home_promo', "Ưu đãi cho mùa\nhè nóng nực", 'Chỉ dành cho đơn hàng Online', 'banner-1.jpg', 'Ưu đãi mùa hè', 'Mua ngay', 'shop', 1),
            $banner('home_promo', "Dành cho người bạn\nthật sự yêu thương", 'Quà tặng & chăm sóc', 'banner-2.jpg', 'Quà tặng và chăm sóc', 'Mua ngay', 'spa', 2),
            $banner('home_promo', "Mới ra mắt\nnăm nay!", 'Thương hiệu bạn sẽ yêu thích', 'banner-3.jpg', 'Thương hiệu mới', 'Mua ngay', 'shop', 3),
        ]);
    }
}
