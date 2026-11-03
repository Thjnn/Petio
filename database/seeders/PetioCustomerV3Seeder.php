<?php
namespace Database\Seeders;
use App\Models\Coupon;
use Illuminate\Database\Seeder;
class PetioCustomerV3Seeder extends Seeder {
 public function run(): void {
  // Không ghi đè mã đã được quản trị chỉnh sửa khi chạy lại.
  foreach([
   ['code'=>'SENMOI20','description'=>'Chào sen mới • giảm 20% cho đơn đầu tiên','scope'=>'product','discount_type'=>'percent','discount_value'=>20,'max_discount'=>50000,'min_order_value'=>99000,'first_order_only'=>true,'new_account_days'=>30],
   ['code'=>'FREESHIP30','description'=>'Quà vận chuyển • giảm tối đa 30.000đ','scope'=>'shipping','discount_type'=>'fixed','discount_value'=>30000,'min_order_value'=>149000,'shipping_method'=>'standard'],
   ['code'=>'CHAO-SEN-SHIP','description'=>'Miễn phí giao tiêu chuẩn cho đơn đầu tiên','scope'=>'shipping','discount_type'=>'fixed','discount_value'=>30000,'min_order_value'=>0,'first_order_only'=>true,'new_account_days'=>30,'shipping_method'=>'standard'],
   ['code'=>'HOATOC20','description'=>'Giảm 20.000đ phí giao hỏa tốc tại khu vực được hỗ trợ','scope'=>'shipping','discount_type'=>'fixed','discount_value'=>20000,'min_order_value'=>199000,'shipping_method'=>'express'],
  ] as $row)Coupon::firstOrCreate(['code'=>$row['code']],$row+['is_active'=>true,'per_user_limit'=>1,'used_count'=>0]);
 }
}
