<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PhoneModel extends Model
{
    protected $fillable = [
        'category_id', 'name_ar', 'name_en', 'image_url',
        'release_year', 'sort_order', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active'    => 'boolean',
            'sort_order'   => 'integer',
            'release_year' => 'integer',
        ];
    }

    /** The brand this model belongs to. */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Spare parts that fit this model. */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)
                     ->orderBy('sort_order')
                     ->orderByDesc('release_year')
                     ->orderBy('id');
    }
}
