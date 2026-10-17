<?php

namespace App\Support;

use App\Models\ProductVariant;
use Illuminate\Support\Collection;

/**
 * Giỏ hàng lưu trong session dạng [variant_id => số lượng].
 * Không lưu giá: giá luôn đọc lại từ product_variants để không lệch khi shop đổi giá.
 */
class Cart
{
    public const KEY = 'cart';

    public const MAX_QTY = 99;

    /** Phí ship khi chưa đủ ngưỡng miễn phí (site ghi: miễn phí từ 500.000đ). */
    public const FREE_SHIP_FROM = 500000;

    public const SHIPPING_FEE = 30000;

    public static function quantities(): array
    {
        return session(self::KEY, []);
    }

    public static function set(int $variantId, int $qty): void
    {
        $cart = self::quantities();

        if ($qty <= 0) {
            unset($cart[$variantId]);
        } else {
            $cart[$variantId] = min($qty, self::MAX_QTY);
        }

        session([self::KEY => $cart]);
    }

    public static function clear(): void
    {
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
