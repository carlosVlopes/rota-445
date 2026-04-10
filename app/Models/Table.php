<?php

namespace App\Models;

use App\Enums\OrderStatus;
use App\Enums\TableStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Table extends Model
{
    protected $fillable = [
        'number',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'status' => TableStatus::class,
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function openOrder(): HasOne
    {
        return $this->hasOne(Order::class)->where('status', OrderStatus::Open);
    }

    public function scopeOccupied(Builder $query): void
    {
        $query->where('status', TableStatus::Occupied);
    }

    public function scopeFree(Builder $query): void
    {
        $query->where('status', TableStatus::Free);
    }
}
