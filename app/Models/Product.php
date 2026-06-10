<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Product extends Model implements HasMedia
{
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'category_id', 'name_ar', 'name_en', 'product_code',
        'price', 'currency', 'image_url', 'material_ar',
        'material_en',
        'badge',
        'badge_en', 'size_range', 'rating', 'review_count', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price'        => 'decimal:2',
            'rating'       => 'decimal:2',
            'review_count' => 'integer',
            'is_active'    => 'boolean',
            'deleted_at'   => 'datetime',
        ];
    }

    // ── Spatie Media Library ──────────────────────────────────────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('product-gallery');
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function colors(): HasMany
    {
        return $this->hasMany(ProductColor::class)->orderBy('sort_order');
    }

    public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class)->orderBy('sort_order');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function favoritedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    // ── Query scopes ──────────────────────────────────────────────────────────

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeForCategory(Builder $query, string $slug): Builder
    {
        return $query->whereHas('category', fn ($q) => $q->where('slug', $slug));
    }

    /**
     * Full-text search on MySQL; falls back to LIKE on SQLite (tests).
     */
    public function scopeSearch(Builder $query, string $term): Builder
    {
        if (DB::getDriverName() === 'mysql') {
            return $query->whereRaw(
                'MATCH(name_ar, name_en, material_ar) AGAINST(? IN BOOLEAN MODE)',
                [$term . '*']
            );
        }

        // SQLite fallback (test environment)
        return $query->where(function (Builder $q) use ($term) {
            $q->where('name_ar',    'like', "%{$term}%")
              ->orWhere('name_en',  'like', "%{$term}%")
              ->orWhere('material_ar', 'like', "%{$term}%");
        });
    }

    /**
     * Adds `is_favorite` column — 0/false for guests (null userId).
     */
    public function scopeWithFavoriteStatus(Builder $query, ?int $userId): Builder
    {
        if (! $userId) {
            return $query->selectRaw('products.*, 0 as is_favorite');
        }

        return $query->selectRaw('products.*, EXISTS(
            SELECT 1 FROM favorites
            WHERE favorites.product_id = products.id
            AND   favorites.user_id = ?
        ) as is_favorite', [$userId]);
    }
}