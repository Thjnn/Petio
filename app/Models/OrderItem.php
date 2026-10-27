<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public $timestamps = false;

    protected $guarded = ['id', 'line_total'];
}
