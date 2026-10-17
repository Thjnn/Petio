<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopFlowTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private const ADDRESS = [
        'receiver_name' => 'Nguyễn Văn A',
        'receiver_phone' => '0912345678',
        'ship_province' => 'Đồng Tháp',
        'ship_district' => 'Cao Lãnh',
        'ship_ward' => 'Phường 1',
        'ship_address' => '12 Nguyễn Huệ',
        'payment_method' => 'cod',
    ];

    public function test_public_pages_render(): void
    {
        foreach (['/', '/shop', '/products/hat-pedigree-vi-ga-va-trung-400gr', '/spa', '/blog',
            '/blog/10-dau-hieu-cho-thay-thu-cung-cua-ban-dang-bi-stress', '/faq', '/about', '/contact',
            '/login', '/register'] as $url) {
            $this->get($url)->assertOk();
        }

        // Bài nháp không lộ ra ngoài
        $this->get('/blog/nhung-bi-mat-suc-khoe-cua-thu-cung-co-the-ban-chua-biet')->assertNotFound();
    }

    public function test_guest_checkout_applies_coupon_and_decrements_stock(): void
    {
        // 2 × Pedigree 400g (57.000) + 1 × size 1.3kg (133.000) = 247.000 → chưa đủ freeship
        $this->postJson('/cart/add', ['variant_id' => 1, 'qty' => 2])->assertOk()->assertJson(['count' => 2]);
        $size13 = ProductVariant::where('sku', 'PT-001-1K3')->value('id');
        $this->postJson('/cart/add', ['variant_id' => $size13, 'qty' => 1])
            ->assertOk()->assertJson(['count' => 3, 'total' => 247000]);

        // Không cho thêm quá tồn kho
        $this->postJson('/cart/add', ['variant_id' => 1, 'qty' => 99])->assertStatus(422);

        // BIG25 cần đơn từ 1.000.000đ
        $this->post('/checkout', self::ADDRESS + ['coupon_code' => 'BIG25'])->assertSessionHasErrors('coupon_code');
        $this->assertSame(0, Order::count());

        $this->post('/checkout', self::ADDRESS + ['coupon_code' => 'news10'])->assertRedirect();

        $order = Order::with('items')->sole();
        $this->assertSame(247000, $order->subtotal);
        $this->assertSame(24700, $order->discount_amount);
        $this->assertSame(30000, $order->shipping_fee);
        $this->assertSame(247000 - 24700 + 30000, $order->total_amount);
        $this->assertNull($order->user_id);
        $this->assertSame([114000, 133000], $order->items->pluck('line_total')->sort()->values()->all());

        $this->assertSame(98, ProductVariant::find(1)->stock_qty);
        $this->assertDatabaseHas('coupons', ['code' => 'NEWS10', 'used_count' => 1]);
        $this->assertDatabaseHas('order_status_history', ['order_id' => $order->id, 'to_status' => 'pending']);

        // Giỏ được xóa, khách xem được trang đơn vừa đặt
        $this->getJson('/cart')->assertJson(['count' => 0]);
        $this->get(route('checkout.success', $order))->assertOk()->assertSee($order->order_code);
    }

    public function test_first_order_coupon_needs_login_and_only_works_once(): void
    {
        $this->postJson('/cart/add', ['variant_id' => 1, 'qty' => 1]);
        $this->post('/checkout', self::ADDRESS + ['coupon_code' => 'FIRST35'])->assertSessionHasErrors('coupon_code');

        $this->post('/register', ['login' => '0987654321', 'password' => 'secret12', 'password_confirmation' => 'secret12'])
            ->assertRedirect('/');
        $user = User::where('phone', '0987654321')->sole();
        $this->assertAuthenticatedAs($user);

        // Giỏ lúc chưa đăng nhập vẫn còn sau khi đăng ký → đơn có 2 × 57.000, giảm 35%
        $this->postJson('/cart/add', ['variant_id' => 1, 'qty' => 1]);
        $this->post('/checkout', self::ADDRESS + ['coupon_code' => 'FIRST35'])->assertRedirect();
        $order = $user->orders()->sole();
        $this->assertSame([114000, 39900], [$order->subtotal, $order->discount_amount]);

        $this->postJson('/cart/add', ['variant_id' => 1, 'qty' => 1]);
        $this->post('/checkout', self::ADDRESS + ['coupon_code' => 'FIRST35'])->assertSessionHasErrors('coupon_code');
    }

    public function test_login_by_username_email_or_phone(): void
    {
        foreach (['admin', 'admin@petio.local'] as $login) {
            // Tài khoản quản trị vào thẳng trang quản trị
            $this->post('/login', ['login' => $login, 'password' => 'Admin@12345'])->assertRedirect('/admin');
            $this->assertAuthenticated();
            $this->post('/logout');
        }

        $this->post('/login', ['login' => 'admin', 'password' => 'sai-mat-khau'])->assertSessionHasErrors('login');
        $this->assertGuest();
    }
}
