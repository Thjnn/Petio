<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerAccountTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    public function test_customer_can_open_every_account_page(): void
    {
        $user = User::findOrFail(2);
        $this->actingAs($user);
        $this->postJson('/cart/add', ['variant_id' => 1, 'qty' => 1])->assertOk();
        $this->post('/checkout', [
            'receiver_name' => 'Khách hàng', 'receiver_phone' => '0912345678',
            'ship_province' => 'Đồng Tháp', 'ship_district' => 'Cao Lãnh',
            'ship_ward' => 'Phường 1', 'ship_address' => '12 Nguyễn Huệ',
            'payment_method' => 'cod',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $product = \App\Models\Product::firstOrFail();
        $this->post(route('customer.favorites.store', $product))->assertRedirect();

        foreach (['', '/ho-so', '/don-hang', '/yeu-thich', '/thong-bao', '/ma-giam-gia',
            '/dia-chi', '/dia-chi/them', '/ho-tro', '/bao-mat', '/ve-petio'] as $path) {
            $this->get('/tai-khoan'.$path)->assertOk();
        }

        $order = $user->orders()->firstOrFail();
        $this->get(route('customer.orders.show', $order))->assertOk();
    }

    public function test_account_requires_an_active_customer(): void
    {
        $this->get('/tai-khoan')->assertRedirect(route('login'));
        $this->actingAs(User::findOrFail(1))->get('/tai-khoan')->assertForbidden();

        $user = User::findOrFail(2);
        $user->update(['status' => 'locked']);
        $this->actingAs($user)->get('/tai-khoan')->assertForbidden();
        $this->get('/tai-khoan/anh-dai-dien')->assertForbidden();
    }

    public function test_customer_can_save_and_edit_an_address(): void
    {
        $this->actingAs(User::findOrFail(2));
        $this->post('/tai-khoan/dia-chi', [
            'label' => 'Nhà', 'receiver_name' => 'Khách hàng', 'receiver_phone' => '0912345678',
            'ship_province' => 'Đồng Tháp', 'ship_district' => 'Cao Lãnh',
            'ship_ward' => 'Phường 1', 'ship_address' => '12 Nguyễn Huệ',
        ])->assertSessionHasNoErrors()->assertRedirect(route('customer.addresses'));
        $address = \App\Models\CustomerAddress::where('user_id', 2)->firstOrFail();
        $this->get(route('customer.addresses.edit', $address))->assertOk()->assertSee('12 Nguyễn Huệ');
    }
}
