<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    protected $fillable = ['label', 'receiver_name', 'receiver_phone', 'ship_province', 'ship_district', 'ship_ward', 'ship_address', 'is_default'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }
}
