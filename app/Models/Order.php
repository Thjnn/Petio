<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = ['id'];

    public const PAYMENT_METHODS = [
        'cod' => 'Thanh toán khi nhận hàng (COD)',
        'bank_transfer' => 'Chuyển khoản ngân hàng',
    ];

    public const STATUSES = [
        'pending' => 'Chờ xác nhận',
        'confirmed' => 'Đã xác nhận',
        'shipping' => 'Đang giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
        'returned' => 'Trả hàng',
    ];

    /** Đơn hủy/trả đã được hoàn kho nên không đổi trạng thái được nữa. */
    public const FINAL_STATUSES = ['cancelled', 'returned'];

    public const PAYMENT_STATUSES = [
        'unpaid' => 'Chưa thanh toán',
        'paid' => 'Đã thanh toán',
        'refunded' => 'Đã hoàn tiền',
    ];

    protected function casts(): array
    {
        return ['paid_at' => 'datetime'];
    }

    public function getRouteKeyName(): string
    {
        return 'order_code';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistory(): HasMany
    {
        return $this->hasMany(OrderStatusHistory::class);
    }
}
