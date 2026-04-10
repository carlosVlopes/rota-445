<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductOptionChoice extends Model
{
    protected $fillable = [
        'option_id',
        'label',
        'price_add',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'price_add' => 'decimal:2',
        ];
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'option_id');
    }
}
