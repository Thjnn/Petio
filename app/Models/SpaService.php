<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpaService extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    public function prices(): HasMany
    {
        return $this->hasMany(SpaServicePrice::class, 'service_id')->orderBy('weight_over_kg');
    }
}
