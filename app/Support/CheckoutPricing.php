<?php
namespace App\Support;
use App\Models\{Coupon,User};
use Illuminate\Validation\ValidationException;
class CheckoutPricing {
 public function quote(int $subtotal, ?User $user, array $data, bool $lock=false): array {
  $method=$data['shipping_method']??'standard';
  if(!in_array($method,['standard','express'],true))$this->fail('shipping_method','Phương thức giao hàng không hợp lệ.');
  if($method==='express'){
   if(!config('petio.express_enabled'))$this->fail('shipping_method','Petio chưa mở giao hỏa tốc. Hãy chọn giao tiêu chuẩn.');
   $areas=array_map(fn($s)=>mb_strtolower($s),config('petio.express_provinces',[]));
   if(!in_array(mb_strtolower(trim($data['ship_province']??'')),$areas,true))$this->fail('shipping_method','Giao hỏa tốc chưa hỗ trợ tỉnh/thành phố này.');
  }
  $fee=$method==='express'?(int)config('petio.express_fee',60000):Cart::shippingFee($subtotal);
  $codes=[];foreach(['coupon_code','shipping_coupon_code'] as $field)$codes[$field]=strtoupper(trim($data[$field]??''));
  $query=Coupon::whereIn('code',array_filter($codes))->orderBy('id');if($lock)$query->lockForUpdate();
  $found=$query->get()->keyBy('code');$chosen=[];
  foreach($codes as $field=>$code){
   $chosen[$field]=null;if(!$code)continue;$c=$found->get($code);
   if(!$c)$this->fail($field,'Mã ưu đãi không tồn tại.');
   if(($c->scope??'product')!==($field==='coupon_code'?'product':'shipping'))$this->fail($field,'Hãy chọn mã đúng nhóm giảm giá hoặc vận chuyển.');
   if($reason=$c->rejectReason($subtotal,$user))$this->fail($field,$reason);
   if($c->scope==='shipping' && $c->shipping_method && $c->shipping_method!==$method)$this->fail($field,'Mã này chỉ áp dụng cho '.($c->shipping_method==='express'?'giao hỏa tốc.':'giao tiêu chuẩn.'));
   $chosen[$field]=$c;
  }
  $discount=$chosen['coupon_code']?->discountFor($subtotal)??0;$shipDiscount=$chosen['shipping_coupon_code']?->discountFor($fee)??0;
  return ['coupon'=>$chosen['coupon_code'],'shipping_coupon'=>$chosen['shipping_coupon_code'],'discount'=>$discount,'shipping_fee'=>$fee,'shipping_discount'=>$shipDiscount,'total'=>$subtotal-$discount+$fee-$shipDiscount];
 }
 private function fail(string $key,string $message): never {throw ValidationException::withMessages([$key=>$message]);}
}
