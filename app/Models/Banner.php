<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['starts_at' => 'datetime', 'ends_at' => 'datetime', 'is_active' => 'boolean'];
    }

    /** Banner đang bật và nằm trong khoảng thời gian hiển thị. */
    public function scopeShowing(Builder $query, string $position): void
    {
        $query->where('position', $position)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhere('starts_at', '<=', now()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhere('ends_at', '>=', now()))
            ->orderBy('sort_order');
    }

    /** Các dòng tiêu đề (admin xuống dòng bằng Enter). */
    public function titleLines(): array
    {
        return preg_split('/\R/', (string) $this->title);
    }
}
