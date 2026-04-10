<?php

namespace App\Models;

use App\Enums\ProductOptionType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductOption extends Model
{
    protected $fillable = [
        'product_id',
        'label',
        'type',
        'required',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'type' => ProductOptionType::class,
            'required' => 'boolean',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function choices(): HasMany
    {
        return $this->hasMany(ProductOptionChoice::class, 'option_id');
    }
}
