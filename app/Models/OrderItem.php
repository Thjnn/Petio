<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public $timestamps = false;

    protected $guarded = ['id', 'line_total'];

    public function review(): \Illuminate\Database\Eloquent\Relations\HasOne {return $this->hasOne(ProductReview::class);}
    public function imageSrc(): string {return asset($this->image_url ?: $this->variant?->product?->thumbnail_url ?: 'assets/images/petio-package.svg');}
    public function variant(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
