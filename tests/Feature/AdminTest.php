<?php

namespace Tests\Feature;

use App\Models\Banner;
use App\Models\BlogPost;
use App\Models\Category;
use App\Models\Coupon;
use App\Models\Faq;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\SpaService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected $seed = true;

    private function admin(): User
    {
        return User::where('username', 'admin')->sole();
    }

    public function test_only_admin_and_staff_get_in(): void
    {
        $this->get('/admin')->assertRedirect('/login');

        $customer = User::create(['username' => 'khach', 'password' => 'secret12']);
        $this->actingAs($customer)->get('/admin')->assertForbidden();

        $customer->update(['role' => 'staff']);
        $this->actingAs($customer)->get('/admin')->assertOk();
        // Nhân viên không được đổi vai trò tài khoản
        $this->actingAs($customer)->put("/admin/users/{$customer->id}", ['role' => 'admin', 'status' => 'active'])->assertForbidden();
    }

    public function test_every_admin_page_renders(): void
    {
        $this->actingAs($this->admin());

        $order = $this->placeOrder();
        $contact = \App\Models\ContactMessage::create(['full_name' => 'A', 'email' => 'a@x.vn', 'phone' => '0912345678', 'content' => 'Xin chào']);

        $urls = ['/admin', '/admin/orders', "/admin/orders/{$order->order_code}", '/admin/users', '/admin/contacts',
            "/admin/contacts/{$contact->id}", '/admin/newsletter'];
        foreach (['products' => Product::class, 'categories' => Category::class, 'posts' => BlogPost::class,
            'banners' => Banner::class, 'faqs' => Faq::class, 'spa' => SpaService::class, 'coupons' => Coupon::class] as $path => $model) {
            array_push($urls, "/admin/{$path}", "/admin/{$path}/create", "/admin/{$path}/".$model::value('id').'/edit');
        }

        foreach ($urls as $url) {
            $this->get($url)->assertOk();
        }
        $this->assertSame('read', $contact->fresh()->status);
    }

    public function test_product_saves_variants(): void
    {
        $this->actingAs($this->admin());
        $product = Product::with('allVariants')->find(1);
        $keep = $product->allVariants->first();

        // Giữ 1 biến thể (sửa giá), thêm 1 dòng mới làm mặc định, các dòng cũ khác bị gỡ
        $this->put("/admin/products/{$product->id}", [
            'name' => $product->name, 'category_id' => $product->category_id, 'pet_type' => 'dog',
            'is_active' => 1, 'default_variant' => 99,
            'variants' => [
                0 => ['id' => $keep->id, 'sku' => $keep->sku, 'name' => $keep->name, 'price' => 50000, 'stock_qty' => 7, 'is_active' => 1],
                99 => ['sku' => 'NEW-SKU-1', 'name' => '5kg', 'price' => 500000, 'old_price' => 550000, 'stock_qty' => 3, 'is_active' => 1],
            ],
        ])->assertSessionHasNoErrors()->assertRedirect();

        $variants = $product->allVariants()->get();
        $this->assertSame([$keep->id, $variants[1]->id], $variants->pluck('id')->all());
        $this->assertSame([50000, 500000], $variants->pluck('price')->all());
        $this->assertSame('5kg', $variants->firstWhere('is_default', true)->name);

        // SKU trùng với sản phẩm khác bị chặn, giá gốc phải lớn hơn giá bán
        $otherSku = ProductVariant::where('product_id', '!=', $product->id)->value('sku');
        $this->put("/admin/products/{$product->id}", [
            'name' => $product->name, 'category_id' => $product->category_id, 'pet_type' => 'dog',
            'variants' => [['sku' => $otherSku, 'name' => 'X', 'price' => 10, 'old_price' => 5, 'stock_qty' => 1]],
        ])->assertSessionHasErrors(['variants.0.sku', 'variants.0.old_price']);
    }

    public function test_cancelling_order_restores_stock_once(): void
    {
        $order = $this->placeOrder();
        $this->assertSame(97, ProductVariant::find(1)->stock_qty);

        $this->actingAs($this->admin());
        $this->put("/admin/orders/{$order->order_code}", ['status' => 'cancelled', 'payment_status' => 'unpaid', 'note' => 'Khách đổi ý'])
            ->assertSessionHasNoErrors();
        $this->assertSame(100, ProductVariant::find(1)->stock_qty);
        $this->assertSame('Khách đổi ý', $order->fresh()->cancel_reason);

        // Đơn đã hủy không mở lại được → kho không bị trừ/cộng lần nữa
        $this->put("/admin/orders/{$order->order_code}", ['status' => 'confirmed', 'payment_status' => 'unpaid'])->assertSessionHasErrors('status');
        $this->assertSame(100, ProductVariant::find(1)->stock_qty);
        $this->assertSame(['pending', 'cancelled'], $order->statusHistory()->orderBy('id')->pluck('to_status')->all());
    }

    public function test_admin_cannot_lock_self(): void
    {
        $admin = $this->admin();
        $this->actingAs($admin)->put("/admin/users/{$admin->id}", ['role' => 'admin', 'status' => 'locked'])->assertSessionHasErrors('user');
        $this->assertSame('active', $admin->fresh()->status);
    }

    private function placeOrder(): Order
    {
        $this->postJson('/cart/add', ['variant_id' => 1, 'qty' => 3]);
        $this->post('/checkout', [
            'receiver_name' => 'Nguyễn Văn A', 'receiver_phone' => '0912345678', 'ship_province' => 'Đồng Tháp',
            'ship_district' => 'Cao Lãnh', 'ship_ward' => 'Phường 1', 'ship_address' => '12 Nguyễn Huệ', 'payment_method' => 'cod',
        ])->assertRedirect();

        return Order::latest('id')->first();
    }
}
