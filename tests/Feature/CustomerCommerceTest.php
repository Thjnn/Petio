<?php
namespace Tests\Feature;
use App\Models\{Coupon,CustomerAddress,Order,ProductReview,User};
use Database\Seeders\PetioCustomerV3Seeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
class CustomerCommerceTest extends TestCase {
 use RefreshDatabase;
 protected $seed=true;
 private function sen(): User {return User::create(['username'=>'sen_'.uniqid(),'full_name'=>'Sen thử nghiệm','password'=>'Password123','role'=>'customer','status'=>'active']);}
 private function data(array $extra=[]): array {return $extra+['receiver_name'=>'Sen mới','receiver_phone'=>'0912345678','ship_province'=>'Hồ Chí Minh','ship_district'=>'Quận 1','ship_ward'=>'Bến Nghé','ship_address'=>'12 Đường Hoa','payment_method'=>'cod','shipping_method'=>'standard'];}
 private function order(User $u,array $data=[]): Order {
  $this->actingAs($u)->postJson('/cart/add',['variant_id'=>1,'qty'=>4])->assertOk();
  $this->post('/checkout',$this->data($data))->assertSessionHasNoErrors()->assertRedirect();
  return $u->orders()->latest('id')->firstOrFail();
 }
 public function test_two_vouchers_checkout_and_cancel(): void {
  $this->seed(PetioCustomerV3Seeder::class);$u=$this->sen();
  $this->actingAs($u)->postJson('/cart/add',['variant_id'=>1,'qty'=>4]);
  $codes=['coupon_code'=>'SENMOI20','shipping_coupon_code'=>'CHAO-SEN-SHIP'];
  $preview=$this->postJson('/checkout/coupon',$codes)->assertOk()->json();
  $this->assertSame(30000,$preview['shipping_discount']);$this->assertGreaterThan(0,$preview['discount']);
  $this->assertDatabaseHas('coupons',['code'=>'SENMOI20','used_count'=>0]);
  $this->post('/checkout',$this->data($codes))->assertSessionHasNoErrors();$o=$u->orders()->latest('id')->first();
  $this->assertEquals($preview['total'],$o->total_amount);$this->assertNotNull($o->items()->first()->image_url);
  $this->get(route('customer.orders.show',$o))->assertOk()->assertSee('Ưu đãi vận chuyển');
  $this->get(route('checkout.success',$o))->assertOk()->assertSee('Giảm phí giao');
  $this->post(route('customer.orders.cancel',$o),['reason'=>'Khác','detail'=>'Cần thay đổi đơn'])->assertSessionHasNoErrors();
  $this->assertDatabaseHas('coupons',['code'=>'SENMOI20','used_count'=>0]);$this->assertDatabaseHas('coupons',['code'=>'CHAO-SEN-SHIP','used_count'=>0]);
  $this->post(route('customer.orders.cancel',$o),['reason'=>'Đổi ý mua hàng'])->assertSessionHasErrors('order');
  $this->assertDatabaseHas('coupons',['code'=>'CHAO-SEN-SHIP','used_count'=>0]);
 }
 public function test_newcomer_and_per_user_limit(): void {
  $this->seed(PetioCustomerV3Seeder::class);$u=$this->sen();$this->order($u,['coupon_code'=>'SENMOI20']);
  $this->postJson('/cart/add',['variant_id'=>1,'qty'=>4]);
  $this->post('/checkout',$this->data(['coupon_code'=>'SENMOI20']))->assertSessionHasErrors('coupon_code');
  $this->postJson('/checkout/coupon',['shipping_coupon_code'=>'CHAO-SEN-SHIP'])->assertUnprocessable();
  $old=$this->sen();$old->forceFill(['created_at'=>now()->subDays(40)])->save();
  $this->actingAs($old)->postJson('/checkout/coupon',['coupon_code'=>'SENMOI20'])->assertUnprocessable();
  $this->actingAs($u)->post('/checkout',$this->data(['shipping_coupon_code'=>'FREESHIP30']))->assertSessionHasNoErrors();
  $this->postJson('/cart/add',['variant_id'=>1,'qty'=>4]);
  $this->post('/checkout',$this->data(['shipping_coupon_code'=>'FREESHIP30']))->assertSessionHasErrors('shipping_coupon_code');
 }
 public function test_voucher_scope_expiry_and_express_coverage(): void {
  $this->seed(PetioCustomerV3Seeder::class);$this->actingAs($this->sen())->postJson('/cart/add',['variant_id'=>1,'qty'=>4]);
  $this->postJson('/checkout/coupon',['coupon_code'=>'FREESHIP30'])->assertUnprocessable()->assertJsonValidationErrors('coupon_code');
  $this->postJson('/checkout/coupon',['shipping_coupon_code'=>'HOATOC20'])->assertUnprocessable();
  $this->postJson('/checkout/coupon',['shipping_method'=>'express'])->assertUnprocessable();
  config(['petio.express_enabled'=>true]);
  $this->postJson('/checkout/coupon',['shipping_method'=>'express','ship_province'=>'Đà Nẵng'])->assertUnprocessable();
  $this->postJson('/checkout/coupon',['shipping_method'=>'express','ship_province'=>'Hồ Chí Minh','shipping_coupon_code'=>'HOATOC20'])->assertOk()->assertJsonPath('shipping_discount',20000)->assertJsonPath('shipping_fee',60000);
  Coupon::where('code','FREESHIP30')->update(['expires_at'=>now()->subDay()]);
  $this->postJson('/checkout/coupon',['shipping_coupon_code'=>'FREESHIP30'])->assertUnprocessable();
 }
 public function test_shipping_discount_is_capped_at_shipping_fee(): void {
  $this->seed(PetioCustomerV3Seeder::class);$this->actingAs($this->sen())->postJson('/cart/add',['variant_id'=>1,'qty'=>10]);
  $quote=$this->postJson('/checkout/coupon',['shipping_coupon_code'=>'FREESHIP30'])->assertOk()->json();
  $this->assertSame(0,$quote['shipping_fee']);$this->assertSame(0,$quote['shipping_discount']);$this->assertGreaterThanOrEqual(500000,$quote['total']);
 }
 public function test_receive_review_ownership_and_duplicate_protection(): void {
  $u=$this->sen();$o=$this->order($u);$item=$o->items()->first();$url=route('customer.orders.receive',$o);$review=route('customer.orders.review',[$o,$item->id]);
  $this->post($url)->assertSessionHasErrors('order');
  $this->post($review,['rating'=>5,'content'=>'Bé nhà mình rất thích sản phẩm này.'])->assertSessionHasErrors('review');
  $o->update(['status'=>'shipping']);
  $this->actingAs($this->sen())->post($url)->assertNotFound();
  $this->post($review,['rating'=>5,'content'=>'Thử đánh giá đơn người khác.'])->assertNotFound();
  $this->actingAs($u)->post($url)->assertSessionHasNoErrors();
  $this->assertNotNull($o->fresh()->received_at);$this->assertSame('completed',$o->fresh()->status);
  $this->post($url)->assertSessionHasErrors('order');
  $this->get('/tai-khoan/don-hang?status=review')->assertSee($o->order_code);
  $this->get(route('customer.orders.show',$o))->assertOk()->assertSee('Mức độ hài lòng');
  $this->post($review,['rating'=>6,'content'=>'Sản phẩm phù hợp với bé nhà mình.'])->assertSessionHasErrors('rating');
  $this->post($review,['rating'=>5,'content'=>'Sản phẩm phù hợp với bé nhà mình.'])->assertSessionHasNoErrors();
  $this->post($review,['rating'=>5,'content'=>'Sản phẩm phù hợp với bé nhà mình.'])->assertSessionHasErrors('review');
  $this->assertSame(1,ProductReview::where('order_item_id',$item->id)->count());
  $this->get(route('products.show',$item->variant->product))->assertOk()->assertSee('Sản phẩm phù hợp với bé nhà mình.');
  $this->get('/tai-khoan/don-hang?status=review')->assertDontSee($o->order_code);
 }
 public function test_review_wrong_item_and_unreceived_order(): void {
  $u=$this->sen();$o=$this->order($u);$other=$this->order($u);$o->update(['status'=>'completed']);
  $this->post(route('customer.orders.review',[$o,$o->items->first()->id]),['rating'=>4,'content'=>'Đánh giá thử sản phẩm.'])->assertSessionHasErrors('review');
  $this->post(route('customer.orders.receive',$o))->assertSessionHasNoErrors();
  $this->post(route('customer.orders.review',[$o,$other->items->first()->id]),['rating'=>4,'content'=>'Đánh giá thử sản phẩm.'])->assertNotFound();
 }
 public function test_completed_order_cannot_move_backwards(): void {
  $o=$this->order($this->sen());$o->update(['status'=>'completed','received_at'=>now()]);
  $this->actingAs(User::find(1))->put(route('admin.orders.update',$o),['status'=>'confirmed','payment_status'=>'paid'])->assertSessionHasErrors('status');
 }
 public function test_buy_again_checks_owner_stock_and_current_price(): void {
  $u=$this->sen();$o=$this->order($u);$v=$o->items()->first()->variant;$v->update(['stock_qty'=>1,'price'=>65000]);
  $this->actingAs($this->sen())->post(route('customer.orders.buy-again',$o))->assertNotFound();
  $this->actingAs($u)->post(route('customer.orders.buy-again',$o))->assertRedirect(route('checkout.index'));
  $this->assertSame(1,session('cart')[$v->id]);$this->get('/checkout')->assertOk()->assertSee('65.000');
 }
 public function test_checkout_picker_renders_groups_and_selected_shipping_code(): void {
  $this->seed(PetioCustomerV3Seeder::class);$this->actingAs($this->sen())->postJson('/cart/add',['variant_id'=>1,'qty'=>4]);
  $this->get('/checkout?shipping_coupon=CHAO-SEN-SHIP')->assertOk()->assertSee('CHAO-SEN-SHIP')->assertSee('Mã giảm giá sản phẩm')->assertSee('Mã miễn / giảm phí vận chuyển')->assertSee('voucher-filter');
 }
 public function test_account_pages_search_default_address_and_support(): void {
  $this->seed(PetioCustomerV3Seeder::class);$this->seed(PetioCustomerV3Seeder::class);
  $this->assertSame(1,Coupon::where('code','SENMOI20')->count());$u=$this->sen();$this->actingAs($u);
  foreach(['','/ho-so','/ma-giam-gia','/dia-chi','/dia-chi/them','/ho-tro','/bao-mat','/ve-petio','/yeu-thich','/thong-bao'] as $path)$this->get('/tai-khoan'.$path)->assertOk();
  $this->get('/tai-khoan/ma-giam-gia?q=SENMOI20')->assertSee('SENMOI20')->assertDontSee('FREESHIP30');
  $this->get('/tai-khoan/ma-giam-gia?type=shipping')->assertSee('FREESHIP30')->assertDontSee('SENMOI20');
  $data=['label'=>'Nhà riêng','user_id'=>$u->id,'is_default'=>true]+collect($this->data())->only(['receiver_name','receiver_phone','ship_province','ship_district','ship_ward','ship_address'])->all();
  $a=CustomerAddress::forceCreate($data);$b=CustomerAddress::forceCreate(array_replace($data,['label'=>'Văn phòng','is_default'=>false]));
  $this->patch(route('customer.addresses.default',$b))->assertSessionHasNoErrors();$this->assertFalse((bool)$a->fresh()->is_default);$this->assertTrue((bool)$b->fresh()->is_default);
  $this->actingAs($this->sen())->patch(route('customer.addresses.default',$a))->assertNotFound();
  $this->actingAs($u)->post('/contact',['full_name'=>'Sen','email'=>'sen@example.test','phone'=>'0912345678','content'=>'Tôi cần hỗ trợ chọn sản phẩm.'])->assertSessionHasNoErrors();
  $this->get('/tai-khoan/ho-tro')->assertSee('Tôi cần hỗ trợ chọn sản phẩm.');
 }
}
