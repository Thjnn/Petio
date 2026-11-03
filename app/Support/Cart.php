<?php

namespace App\Support;

use App\Models\Cart as CartModel;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;

/**
 * Giỏ hàng dạng [variant_id => số lượng].
 * - Đã đăng nhập: lưu trong database (bảng carts, cart_items) nên đăng xuất/đăng nhập lại vẫn còn.
 * - Chưa đăng nhập: lưu trong session; khi đăng nhập thì gộp vào giỏ của tài khoản (mergeGuestCart).
 * Không lưu giá: giá luôn đọc lại từ product_variants để không lệch khi shop đổi giá.
 */
class Cart
{
    public const KEY = 'cart';

    public const MAX_QTY = 99;

    /** Phí ship khi chưa đủ ngưỡng miễn phí (site ghi: miễn phí từ 500.000đ). */
    public const FREE_SHIP_FROM = 500000;

    public const SHIPPING_FEE = 30000;

    /** @return array<int, int> [variant_id => số lượng] */
    public static function quantities(): array
    {
        if ($userId = auth()->id()) {
            $cart = CartModel::where('user_id', $userId)->first();

            return $cart
                ? $cart->items()->pluck('quantity', 'variant_id')->map(fn ($q) => (int) $q)->all()
                : [];
        }

        return session(self::KEY, []);
    }

    public static function set(int $variantId, int $qty): void
    {
        if ($userId = auth()->id()) {
            $cart = CartModel::firstOrCreate(['user_id' => $userId]);

            if ($qty <= 0) {
                $cart->items()->where('variant_id', $variantId)->delete();
            } else {
                $cart->items()->updateOrCreate(
                    ['variant_id' => $variantId],
                    ['quantity' => min($qty, self::MAX_QTY)],
                );
            }

            $cart->touch();

            return;
        }

        $cart = session(self::KEY, []);

        if ($qty <= 0) {
            unset($cart[$variantId]);
        } else {
            $cart[$variantId] = min($qty, self::MAX_QTY);
        }

        session([self::KEY => $cart]);
    }

    public static function clear(): void
    {
        if ($userId = auth()->id()) {
            CartModel::where('user_id', $userId)->delete(); // cart_items xóa theo (cascade)
        }

        session()->forget(self::KEY);
    }

    /**
     * Gọi ngay sau khi đăng nhập/đăng ký: đưa giỏ của khách (session) vào giỏ của tài khoản.
     * Món trùng thì cộng dồn số lượng.
     */
    public static function mergeGuestCart(): void
    {
        $guest = session(self::KEY, []);

        if (! auth()->check() || ! $guest) {
            return;
        }

        $existing = self::quantities();
        // Bỏ qua biến thể đã bị xóa để không vi phạm khóa ngoại.
        $validIds = ProductVariant::whereIn('id', array_keys($guest))->pluck('id')->all();

        foreach ($guest as $variantId => $qty) {
            if (in_array((int) $variantId, $validIds, true)) {
                self::set((int) $variantId, ($existing[$variantId] ?? 0) + (int) $qty);
            }
        }

        session()->forget(self::KEY);
    }

    /**
     * Các dòng trong giỏ kèm biến thể + sản phẩm. Dòng nào có sản phẩm đã bị ẩn/xóa thì bỏ qua.
     *
     * @return Collection<int, array{variant: ProductVariant, qty: int, line_total: int}>
     */
    public static function lines(): Collection
    {
        $qty = self::quantities();

        return ProductVariant::with('product')
            ->whereIn('id', array_keys($qty))
            ->where('is_active', true)
            ->whereHas('product', fn ($q) => $q->where('is_active', true))
            ->get()
            ->map(fn (ProductVariant $v) => [
                'variant' => $v,
                'qty' => $qty[$v->id],
                'line_total' => $v->price * $qty[$v->id],
            ])
            ->values();
    }

    public static function shippingFee(int $subtotal): int
    {
        return $subtotal >= self::FREE_SHIP_FROM ? 0 : self::SHIPPING_FEE;
    }

    /** Dữ liệu cho cart.js vẽ modal giỏ hàng. */
    public static function toJson(): array
    {
        $lines = self::lines();

        return [
            'items' => $lines->map(fn ($l) => [
                'id' => $l['variant']->id,
                'name' => $l['variant']->product->name
                    .($l['variant']->name !== 'Mặc định' ? ' – '.$l['variant']->name : ''),
                'price' => $l['variant']->price,
                'img' => asset($l['variant']->product->thumbnail_url),
                'qty' => $l['qty'],
            ]),
            'count' => $lines->sum('qty'),
            'total' => $lines->sum('line_total'),
        ];
    }
}
