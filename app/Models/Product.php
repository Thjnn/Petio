<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return [
            'rating_avg' => 'float',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    /** Biến thể đang bán, biến thể mặc định đứng đầu (rồi tới giá rẻ nhất). */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)
            ->where('is_active', true)
            ->orderByDesc('is_default')->orderBy('price')->orderBy('id');
    }

    /** Mọi biến thể kể cả đang tắt — dùng ở trang quản trị. */
    public function allVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class)->orderBy('sort_order')->orderBy('id');
    }

    /** Biến thể hiển thị giá ở thẻ sản phẩm. */
    public function defaultVariant(): ?ProductVariant
    {
        return $this->variants->first();
    }

    /** Sản phẩm đang bán + nạp sẵn những gì thẻ sản phẩm cần, tránh N+1 query. */
    public function scopeForCard(Builder $query): void
    {
        $query->where('is_active', true)
            ->whereHas('variants')
            ->with(['variants', 'tags', 'category.parent']);
    }

    /** Mã loài mà shop.js dùng để lọc: cho / meo / chung. */
    public function petCode(): string
    {
        return ['dog' => 'cho', 'cat' => 'meo'][$this->pet_type] ?? 'chung';
    }

    public function isHot(): bool
    {
        return $this->tags->contains('slug', 'hot');
    }
}
