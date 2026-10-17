<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpaServicePrice extends Model
{
    public $timestamps = false;

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['weight_over_kg' => 'float', 'weight_up_to_kg' => 'float'];
    }

    /** Nhãn mốc cân nặng như bảng giá cũ: "Dưới 1kg", "1.1kg – 3kg". */
    public function label(): string
    {
        $kg = fn (float $w) => rtrim(rtrim(number_format($w, 1, '.', ''), '0'), '.').'kg';

        return $this->weight_over_kg == 0
            ? 'Dưới '.$kg($this->weight_up_to_kg)
            : $kg($this->weight_over_kg + 0.1).' – '.$kg($this->weight_up_to_kg);
    }
}
