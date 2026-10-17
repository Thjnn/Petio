<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'first_order_only' => 'boolean',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    /** Lý do mã không dùng được cho đơn này, null = dùng được. */
    public function rejectReason(int $subtotal, ?User $user): ?string
    {
        return match (true) {
            ! $this->is_active,
            $this->starts_at && $this->starts_at->isFuture(),
            $this->expires_at && $this->expires_at->isPast() => 'Mã giảm giá đã hết hạn hoặc chưa được áp dụng.',
            $this->usage_limit !== null && $this->used_count >= $this->usage_limit => 'Mã giảm giá đã hết lượt sử dụng.',
            $subtotal < $this->min_order_value => 'Đơn hàng chưa đủ '.number_format($this->min_order_value, 0, ',', '.').'đ để dùng mã này.',
            $this->first_order_only && ! $user => 'Vui lòng đăng nhập để dùng mã cho đơn đầu tiên.',
            $this->first_order_only && $user->orders()->where('status', '!=', 'cancelled')->exists() => 'Mã này chỉ dành cho đơn hàng đầu tiên.',
            default => null,
        };
    }

    public function discountFor(int $subtotal): int
    {
        $discount = $this->discount_type === 'percent'
            ? intdiv($subtotal * $this->discount_value, 100)
            : $this->discount_value;

        if ($this->max_discount !== null) {
            $discount = min($discount, $this->max_discount);
        }

        return min($discount, $subtotal);
    }
}
