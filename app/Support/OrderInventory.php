<?php

namespace App\Support;

use App\Models\Order;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;

class OrderInventory
{
    // Called only inside a transaction after locking the order row.
    public function restore(Order $order, bool $releaseCoupon): void
    {
        foreach ($order->items()->orderBy('variant_id')->get() as $item) {
            $variant = ProductVariant::whereKey($item->variant_id)->lockForUpdate()->first();
            if ($variant) {
                $variant->increment('stock_qty', $item->quantity);
                $product = $variant->product()->lockForUpdate()->first();
                if ($product) {
                    $product->decrement('sold_count', min($item->quantity, $product->sold_count));
                }
            }
        }
        if ($releaseCoupon && $order->coupon_id) {
            DB::table('coupons')->where('id', $order->coupon_id)->where('used_count', '>', 0)->decrement('used_count');
        }
    }
}
