<?php

namespace Tests\Feature;

use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CustomerAccountTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function customer()
    {
        return User::findOrFail(2);
    }

    private function addressData()
    {
        return ['label' => 'Nhà riêng', 'receiver_name' => 'Khách Petio', 'receiver_phone' => '0912345678', 'ship_province' => 'Đồng Tháp', 'ship_district' => 'Cai Lậy', 'ship_ward' => 'Long Khánh', 'ship_address' => '12 Đường Hoa'];
    }

    public function test_customer_login_registration_and_all_pages(): void
    {
        $this->post('/login', ['login' => 'khachhang', 'password' => 'Khach@12345'])->assertRedirect('/tai-khoan');
        $this->post('/logout');
        $this->post('/register', ['login' => 'senmoi', 'password' => 'Password123', 'password_confirmation' => 'Password123'])->assertRedirect('/tai-khoan');
        $this->post('/logout');
        $paths = ['', '/ho-so', '/don-hang', '/yeu-thich', '/thong-bao', '/ma-giam-gia', '/dia-chi', '/dia-chi/them', '/ho-tro', '/bao-mat', '/ve-petio'];
        for ($i = 0; $i < count($paths); $i++) {
            $this->get('/tai-khoan'.$paths[$i])->assertRedirect('/login');
        }
        $this->actingAs($this->customer());
        for ($i = 0; $i < count($paths); $i++) {
            $this->get('/tai-khoan'.$paths[$i])->assertOk();
        }
    }

    public function test_profile_password_and_avatar_are_persisted(): void
    {
        Storage::fake('public');
        $u = $this->customer();
        $this->actingAs($u);
        $file = UploadedFile::fake()->createWithContent('avatar.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII='));
        $this->put('/tai-khoan/ho-so', ['full_name' => 'Sen Petio', 'email' => 'sen@example.com', 'phone' => '0912345678', 'avatar' => $file])->assertSessionHasNoErrors();
        $u->refresh();
        $this->assertSame('Sen Petio', $u->full_name);
        $this->assertStringStartsWith('storage/avatars/', $u->avatar_url);
        Storage::disk('public')->assertExists(substr($u->avatar_url, 8));
        $this->put('/tai-khoan/mat-khau', ['current_password' => 'wrong', 'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123'])->assertSessionHasErrors('current_password');
        $this->put('/tai-khoan/mat-khau', ['current_password' => 'Khach@12345', 'password' => 'NewPassword123', 'password_confirmation' => 'NewPassword123'])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('NewPassword123', $u->fresh()->password));
    }

    public function test_addresses_enforce_ownership_and_one_default(): void
    {
        $this->actingAs($this->customer());
        $this->post('/tai-khoan/dia-chi', $this->addressData())->assertSessionHasNoErrors();
        $first = CustomerAddress::first();
        $this->assertTrue($first->is_default);
        $this->post('/tai-khoan/dia-chi', array_merge($this->addressData(), ['label' => 'Công ty', 'is_default' => 1]));
        $second = CustomerAddress::latest('id')->first();
        $this->assertFalse($first->fresh()->is_default);
        $this->assertTrue($second->is_default);
        $other = User::create(['username' => 'other', 'password' => 'Password123', 'role' => 'customer', 'status' => 'active']);
        $this->actingAs($other)->get('/tai-khoan/dia-chi/'.$first->id.'/sua')->assertNotFound();
        $this->delete('/tai-khoan/dia-chi/'.$first->id)->assertNotFound();
        $this->put('/tai-khoan/dia-chi/'.$first->id, $this->addressData())->assertNotFound();
        $this->actingAs($this->customer())->delete('/tai-khoan/dia-chi/'.$second->id);
        $this->assertTrue($first->fresh()->is_default);
    }

    public function test_favorites_are_idempotent(): void
    {
        $p = Product::first();
        $url = '/tai-khoan/yeu-thich/'.$p->slug;
        $this->actingAs($this->customer());
        $this->post($url);
        $this->post($url);
        $this->assertDatabaseCount('customer_favorites', 1);
        $this->get('/tai-khoan/yeu-thich')->assertOk()->assertSee($p->name);
        $this->delete($url);
        $this->assertDatabaseCount('customer_favorites', 0);
    }

    public function test_checkout_saved_address_and_private_orders_notifications(): void
    {
        $u = $this->customer();
        $this->actingAs($u);
        $this->post('/tai-khoan/dia-chi', $this->addressData());
        $this->postJson('/cart/add', ['variant_id' => 1, 'qty' => 1])->assertOk();
        $this->get('/checkout?coupon=NEWS10')->assertOk()->assertSee('12 Đường Hoa')->assertSee('NEWS10');
        $this->post('/checkout', $this->addressData() + ['payment_method' => 'cod'])->assertSessionHasNoErrors();
        $order = Order::where('user_id', $u->id)->latest('id')->firstOrFail();
        $this->get('/tai-khoan/don-hang/'.$order->order_code)->assertOk()->assertSee($order->order_code);
        $this->get('/tai-khoan/don-hang')->assertOk();
        $this->get('/tai-khoan/thong-bao')->assertOk()->assertSee($order->order_code);
        $history = $order->statusHistory()->first();
        $other = User::create(['username' => 'outsider', 'password' => 'Password123', 'role' => 'customer', 'status' => 'active']);
        $this->actingAs($other)->get('/tai-khoan/don-hang/'.$order->order_code)->assertNotFound();
        $this->patch('/tai-khoan/thong-bao/'.$history->id)->assertNotFound();
        $this->get('/tai-khoan/thong-bao')->assertDontSee($order->order_code);
        $this->actingAs($u)->patch('/tai-khoan/thong-bao/'.$history->id);
        $this->assertNotNull(DB::table('customer_notice_states')->where('history_id', $history->id)->value('read_at'));
        $this->delete('/tai-khoan/thong-bao/'.$history->id);
        $this->get('/tai-khoan/thong-bao')->assertDontSee($order->order_code);
    }

    public function test_support_and_locked_account(): void
    {
        $u = $this->customer();
        $this->actingAs($u)->post('/contact', ['full_name' => 'Sen Petio', 'email' => 'sen@example.com', 'phone' => '0912345678', 'content' => 'Hỗ trợ đơn hàng'])->assertSessionHasNoErrors();
        $this->get('/tai-khoan/ho-tro')->assertSee('Hỗ trợ đơn hàng');
        $u->update(['status' => 'locked']);
        $this->get('/tai-khoan')->assertForbidden();
    }
}
