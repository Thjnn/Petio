<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariant extends Model
{
    protected $guarded = ['id'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** % giảm giá làm tròn, null khi không giảm. */
    public function discountPercent(): ?int
    {
        if (! $this->old_price || $this->old_price <= $this->price) {
            return null;
        }

        return (int) round(($this->old_price - $this->price) * 100 / $this->old_price);
    }
}
