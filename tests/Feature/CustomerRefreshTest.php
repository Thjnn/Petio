<?php
namespace Tests\Feature;
use App\Models\{CustomerAddress,Order,ProductVariant,User};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
class CustomerRefreshTest extends TestCase
{
    use RefreshDatabase;
    protected $seed = true;
    private function address(User $u): CustomerAddress
    {
        return CustomerAddress::forceCreate(['user_id'=>$u->id,'label'=>'Nhà riêng','receiver_name'=>'Sen Petio','receiver_phone'=>'0912345678','ship_province'=>'Đồng Tháp','ship_district'=>'Cai Lậy','ship_ward'=>'Long Khánh','ship_address'=>'12 Đường Hoa','is_default'=>true]);
    }
    private function place(User $u): Order
    {
        $a=$this->address($u);
        $this->actingAs($u)->postJson('/cart/add',['variant_id'=>1,'qty'=>2])->assertOk();
        $this->post('/checkout',['address_mode'=>'saved','address_id'=>$a->id,'payment_method'=>'cod','coupon_code'=>'NEWS10'])->assertSessionHasNoErrors();
        return $u->orders()->latest('id')->firstOrFail();
    }
    public function test_saved_address_is_used_without_manual_fields(): void
    {
        $u=User::find(2);$a=$this->address($u);
        $this->actingAs($u)->postJson('/cart/add',['variant_id'=>1,'qty'=>1]);
        $this->get('/checkout')->assertOk()->assertSee('Nhà riêng');
        $this->post('/checkout',['address_mode'=>'saved','address_id'=>$a->id,'receiver_name'=>'Tampered','payment_method'=>'cod'])->assertSessionHasNoErrors();
        $this->assertSame('Sen Petio',$u->orders()->latest('id')->first()->receiver_name);
        $other=$this->address(User::find(1));
        $this->post('/checkout',['address_mode'=>'saved','address_id'=>$other->id,'payment_method'=>'cod'])->assertSessionHasErrors('address_id');
    }
    public function test_avatar_save_without_full_name_and_no_symlink(): void
    {
        Storage::fake('public');$u=User::find(2);$this->actingAs($u);
        $bytes=base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII=');
        $this->post('/tai-khoan/anh-dai-dien',['avatar'=>UploadedFile::fake()->createWithContent('avatar.png',$bytes)])->assertSessionHasNoErrors();
        $old=$u->fresh()->avatar_url;Storage::disk('public')->assertExists(substr($old,8));
        $this->get('/tai-khoan/anh-dai-dien')->assertOk()->assertHeader('Content-Type','image/png');
        $this->post('/tai-khoan/anh-dai-dien',['avatar'=>UploadedFile::fake()->createWithContent('new.png',$bytes)])->assertSessionHasNoErrors();
        Storage::disk('public')->assertMissing(substr($old,8));
        $this->post('/tai-khoan/anh-dai-dien',['avatar'=>UploadedFile::fake()->createWithContent('bad.svg','<svg></svg>')])->assertSessionHasErrors('avatar');
    }
    public function test_cancel_restores_stock_and_coupon_exactly_once(): void
    {
        $u=User::find(2);$before=ProductVariant::find(1)->stock_qty;$o=$this->place($u);$url=route('customer.orders.cancel',$o);
        $this->assertSame($before-2,ProductVariant::find(1)->stock_qty);
        $this->post($url,['reason'=>'Khác','detail'=>'Đặt nhầm kích thước'])->assertSessionHasNoErrors();
        $this->assertSame('cancelled',$o->fresh()->status);$this->assertSame($before,ProductVariant::find(1)->stock_qty);
        $this->assertDatabaseHas('coupons',['code'=>'NEWS10','used_count'=>0]);
        $this->assertDatabaseHas('order_status_history',['order_id'=>$o->id,'to_status'=>'cancelled','note'=>'Khác: Đặt nhầm kích thước']);
        $this->post($url,['reason'=>'Đổi ý mua hàng'])->assertSessionHasErrors('order');
        $this->assertSame($before,ProductVariant::find(1)->stock_qty);
        $this->actingAs(User::find(1))->put('/admin/orders/'.$o->order_code,['status'=>'shipping','payment_status'=>'unpaid'])->assertSessionHasErrors('status');
    }
    public function test_cannot_cancel_other_users_or_shipping_orders(): void
    {
        $u=User::find(2);$o=$this->place($u);
        $other=User::create(['username'=>'other_sen','password'=>'Password123','role'=>'customer','status'=>'active']);
        $this->actingAs($other)->post(route('customer.orders.cancel',$o),['reason'=>'Đổi ý mua hàng'])->assertNotFound();
        $o->update(['status'=>'shipping']);$before=ProductVariant::find(1)->stock_qty;
        $this->actingAs($u)->post(route('customer.orders.cancel',$o),['reason'=>'Đổi ý mua hàng'])->assertSessionHasErrors('order');
        $this->assertSame($before,ProductVariant::find(1)->stock_qty);
    }
    public function test_paid_cancellation_does_not_claim_an_automatic_refund(): void
    {
        $u=User::find(2);$o=$this->place($u);$o->update(['payment_status'=>'paid','paid_at'=>now()]);
        $this->post(route('customer.orders.cancel',$o),['reason'=>'Khác'])->assertSessionHasErrors('detail');
        $this->assertSame('pending',$o->fresh()->status);
        $this->post(route('customer.orders.cancel',$o),['reason'=>'Đổi ý mua hàng'])->assertRedirect();
        $this->assertSame('cancelled',$o->fresh()->status);
        $this->assertSame('paid',$o->fresh()->payment_status);
    }
    public function test_coupon_preview_does_not_consume_coupon(): void
    {
        $this->postJson('/cart/add',['variant_id'=>1,'qty'=>2]);
        $this->postJson('/checkout/coupon',['coupon_code'=>'NEWS10'])->assertOk()->assertJson(['discount'=>11400,'total'=>132600]);
        $this->assertDatabaseHas('coupons',['code'=>'NEWS10','used_count'=>0]);
        $this->postJson('/checkout/coupon',['coupon_code'=>'INVALID'])->assertStatus(422);
    }
}
