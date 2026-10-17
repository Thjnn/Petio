<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    protected $guarded = ['id'];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /** Danh mục gốc: bộ lọc ở trang Cửa hàng chỉ lọc theo 5 danh mục cấp 1. */
    public function root(): Category
    {
        return $this->parent ?? $this;
    }
}
