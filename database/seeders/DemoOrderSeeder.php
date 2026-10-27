<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\ProductVariant;
use App\Support\Cart;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Khách hàng + đơn hàng + liên hệ + bản tin mẫu để thử trang quản trị.
 * Chạy riêng (không xóa dữ liệu đang có): php artisan db:seed --class=DemoOrderSeeder
 * Chạy lại được: khách cập nhật theo username, đơn mẫu (mã PTyymmdd-Dnnn) bị xóa rồi tạo lại.
 */
class DemoOrderSeeder extends Seeder
{
    public function run(): void
    {
        mt_srand(2026); // cố định để lần nào chạy cũng ra cùng một bộ dữ liệu

        // ---------- Khách hàng (mật khẩu tạm: Khach@12345) ----------
        $customers = [
            ['tranthimai', 'Trần Thị Mai', '0901234561', 'Hồ Chí Minh', 'Quận 1', 'Phường Bến Nghé', '12 Lê Lợi'],
            ['lequochuy', 'Lê Quốc Huy', '0912345672', 'Hồ Chí Minh', 'Quận 7', 'Phường Tân Phong', '88 Nguyễn Thị Thập'],
            ['phamngoclan', 'Phạm Ngọc Lan', '0923456783', 'Hà Nội', 'Quận Cầu Giấy', 'Phường Dịch Vọng', '25 Xuân Thủy'],
            ['hoangminhtuan', 'Hoàng Minh Tuấn', '0934567894', 'Hà Nội', 'Quận Đống Đa', 'Phường Láng Hạ', '102 Thái Hà'],
            ['vothuytrang', 'Võ Thùy Trang', '0945678905', 'Đà Nẵng', 'Quận Hải Châu', 'Phường Thạch Thang', '45 Bạch Đằng'],
            ['dangvankhoa', 'Đặng Văn Khoa', '0956789016', 'Cần Thơ', 'Quận Ninh Kiều', 'Phường An Hòa', '7 Mậu Thân'],
            ['buithanhha', 'Bùi Thanh Hà', '0967890127', 'Hồ Chí Minh', 'Quận Bình Thạnh', 'Phường 25', '210 Điện Biên Phủ'],
            ['ngothaovy', 'Ngô Thảo Vy', '0978901238', 'Hải Phòng', 'Quận Lê Chân', 'Phường An Biên', '33 Tô Hiệu'],
            ['duongnhatnam', 'Dương Nhật Nam', '0989012349', 'Bình Dương', 'TP Thủ Dầu Một', 'Phường Phú Cường', '9 Yersin'],
            ['lyhoanganh', 'Lý Hoàng Anh', '0990123450', 'Hồ Chí Minh', 'TP Thủ Đức', 'Phường Linh Trung', '1 Võ Văn Ngân'],
        ];
        $password = Hash::make('Khach@12345');
        foreach ($customers as $i => [$username, $name, $phone]) {
            $joined = now()->subDays(90 - $i * 5);
            DB::table('users')->updateOrInsert(['username' => $username], [
                'email' => "$username@petio.local", 'phone' => $phone, 'password' => $password,
                'full_name' => $name, 'role' => 'customer', 'status' => $i === 9 ? 'locked' : 'active',
                'last_login_at' => now()->subDays(mt_rand(0, 10)), 'created_at' => $joined, 'updated_at' => $joined,
            ]);
        }
        $userIds = DB::table('users')->whereIn('username', array_column($customers, 0))->pluck('id', 'username');

        $guests = [
            ['Nguyễn Hữu Phước', '0381112223', 'Đồng Nai', 'TP Biên Hòa', 'Phường Tân Mai', '56 Phạm Văn Thuận'],
            ['Trịnh Mỹ Linh', '0392223334', 'Hồ Chí Minh', 'Quận 3', 'Phường Võ Thị Sáu', '150 Nam Kỳ Khởi Nghĩa'],
            ['Cao Gia Bảo', '0373334445', 'Khánh Hòa', 'TP Nha Trang', 'Phường Lộc Thọ', '20 Trần Phú'],
        ];

        // ---------- Đơn hàng ----------
        // Mỗi trạng thái kèm chuỗi lịch sử dẫn tới nó; số lần lặp = tỉ lệ xuất hiện.
        $flows = [
            'pending' => ['pending'],
            'confirmed' => ['pending', 'confirmed'],
            'shipping' => ['pending', 'confirmed', 'shipping'],
            'completed' => ['pending', 'confirmed', 'shipping', 'completed'],
            'cancelled' => ['pending', 'cancelled'],
            'returned' => ['pending', 'confirmed', 'shipping', 'completed', 'returned'],
        ];
        $statuses = ['pending', 'pending', 'pending', 'confirmed', 'confirmed', 'shipping', 'shipping',
            'completed', 'completed', 'completed', 'completed', 'completed', 'completed', 'cancelled', 'cancelled', 'returned'];
        $notes = [
            'confirmed' => 'Đã gọi xác nhận với khách', 'shipping' => 'Đã bàn giao cho đơn vị vận chuyển',
            'completed' => 'Giao hàng thành công', 'cancelled' => 'Khách hủy đơn', 'returned' => 'Khách trả hàng do sản phẩm lỗi',
        ];
        $cancelReasons = ['Khách đổi ý không mua nữa', 'Không liên lạc được với khách', 'Khách đặt nhầm sản phẩm'];
        $orderNotes = [null, null, null, 'Giao giờ hành chính giúp mình', 'Gọi trước khi giao', 'Để hàng ở bảo vệ tòa nhà'];

        $variants = ProductVariant::with('product')->where('is_active', true)->get();
        $coupons = Coupon::whereIn('code', ['NEWS10', 'BIG25'])->get()->keyBy('code');

        DB::transaction(function () use ($customers, $userIds, $guests, $flows, $statuses, $notes, $cancelReasons, $orderNotes, $variants, $coupons) {
            DB::table('orders')->where('order_code', 'like', 'PT%-D%')->delete();

            for ($n = 1; $n <= 40; $n++) {
                // 1/5 số đơn là khách vãng lai
                if ($n % 5 === 0) {
                    [$name, $phone, $province, $district, $ward, $address] = $guests[$n % count($guests)];
                    $userId = null;
                    $email = null;
                } else {
                    [$username, $name, $phone, $province, $district, $ward, $address] = $customers[mt_rand(0, count($customers) - 1)];
                    $userId = $userIds[$username];
                    $email = "$username@petio.local";
                }

                $status = $statuses[mt_rand(0, count($statuses) - 1)];
                // Đơn mới thì gần đây, đơn đã xong thì rải trong 60 ngày qua
                $placedAt = now()->subDays(in_array($status, ['pending', 'confirmed']) ? mt_rand(0, 3) : mt_rand(4, 60))
                    ->setTime(mt_rand(8, 21), mt_rand(0, 59));

                $lines = $variants->random(mt_rand(1, 4))->map(fn ($v) => [$v, mt_rand(1, 3)]);
                $subtotal = $lines->sum(fn ($l) => $l[0]->price * $l[1]);

                $coupon = null;
                if (mt_rand(1, 4) === 1) {
                    $coupon = $subtotal >= $coupons['BIG25']->min_order_value ? $coupons['BIG25'] : $coupons['NEWS10'];
                }
                $discount = $coupon?->discountFor($subtotal) ?? 0;
                $shippingFee = Cart::shippingFee($subtotal);

                $method = mt_rand(1, 3) === 1 ? 'bank_transfer' : 'cod';
                $paid = $method === 'bank_transfer' ? $status !== 'pending' : in_array($status, ['completed', 'returned']);
                $paymentStatus = $paid ? ($status === 'returned' || ($status === 'cancelled' && $method === 'bank_transfer') ? 'refunded' : 'paid') : 'unpaid';

                $history = $flows[$status];
                $steps = collect($history)->map(fn ($s, $i) => $placedAt->copy()->addHours($i * mt_rand(6, 30)));

                $order = Order::create([
                    'order_code' => 'PT'.$placedAt->format('ymd').sprintf('-D%03d', $n),
                    'user_id' => $userId, 'coupon_id' => $coupon?->id,
                    'receiver_name' => $name, 'receiver_phone' => $phone, 'receiver_email' => $email,
                    'ship_province' => $province, 'ship_district' => $district, 'ship_ward' => $ward, 'ship_address' => $address,
                    'subtotal' => $subtotal, 'discount_amount' => $discount, 'shipping_fee' => $shippingFee,
                    'total_amount' => $subtotal - $discount + $shippingFee,
                    'payment_method' => $method, 'payment_status' => $paymentStatus,
                    'paid_at' => $paid ? $steps[min(1, count($steps) - 1)] : null,
                    'status' => $status, 'note' => $orderNotes[mt_rand(0, count($orderNotes) - 1)],
                    'cancel_reason' => $status === 'cancelled' ? $cancelReasons[mt_rand(0, count($cancelReasons) - 1)] : null,
                    'created_at' => $placedAt, 'updated_at' => $steps->last(),
                ]);

                foreach ($lines as [$v, $qty]) {
                    $order->items()->create([
                        'variant_id' => $v->id, 'product_name' => $v->product->name, 'variant_name' => $v->name,
                        'sku' => $v->sku, 'unit_price' => $v->price, 'quantity' => $qty,
                    ]);
                }

                foreach ($history as $i => $to) {
                    $order->statusHistory()->create([
                        'from_status' => $history[$i - 1] ?? null, 'to_status' => $to,
                        'changed_by' => $i === 0 ? $userId : 1,
                        'note' => $i === 0 ? 'Khách đặt hàng' : $notes[$to], 'created_at' => $steps[$i],
                    ]);
                }
            }
        });

        // ---------- Tin nhắn liên hệ ----------
        DB::table('contact_messages')->where('email', 'like', '%@petio.local')->delete();
        $messages = [
            ['tranthimai', 'Shop ơi cho mình hỏi spa có nhận tắm cho mèo Anh lông dài không ạ?', 'replied'],
            ['lequochuy', 'Đơn hàng của mình giao thiếu 1 gói pate, nhờ shop kiểm tra giúp.', 'read'],
            ['phamngoclan', 'Bên mình có bán sỉ cát vệ sinh Jolly Cat không? Mình cần khoảng 50 bao/tháng.', 'new'],
            ['vothuytrang', 'Cho mình đặt lịch tắm + cắt tỉa cho bé Poodle 4kg sáng thứ 7 nhé.', 'new'],
        ];
        foreach ($messages as $i => [$username, $content, $status]) {
            $c = $customers[array_search($username, array_column($customers, 0))];
            $at = now()->subDays(6 - $i);
            DB::table('contact_messages')->insert([
                'user_id' => $userIds[$username], 'full_name' => $c[1], 'email' => "$username@petio.local", 'phone' => $c[2],
                'content' => $content, 'status' => $status,
                'admin_note' => $status === 'replied' ? 'Đã tư vấn qua điện thoại' : null,
                'replied_at' => $status === 'replied' ? $at->copy()->addHours(3) : null,
                'created_at' => $at, 'updated_at' => $at,
            ]);
        }

        // ---------- Đăng ký bản tin ----------
        foreach (array_slice($customers, 0, 6) as $i => [$username]) {
            DB::table('newsletter_subscribers')->updateOrInsert(['email' => "$username@petio.local"], [
                'is_active' => $i !== 5, 'subscribed_at' => now()->subDays(40 - $i * 6),
                'unsubscribed_at' => $i === 5 ? now()->subDays(3) : null,
            ]);
        }
    }
}
