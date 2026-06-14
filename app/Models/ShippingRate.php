<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ShippingRate extends Model
{
    use HasFactory;

    protected $fillable = ['city_ar', 'city_en', 'price', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'price'      => 'decimal:3',
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    // ── Cache helpers ─────────────────────────────────────────────────────────
    // Shipping rates change rarely (admin-edited, maybe weekly). Caching the
    // active-list and the per-city lookup turns the cart screen + every
    // place-order call into a single in-memory lookup instead of a DB hit.

    private const CACHE_KEY_ACTIVE = 'shipping_rates.active';
    private const CACHE_TTL        = 3600; // 1 hour — long enough to matter,
                                           // short enough that a missed
                                           // invalidation auto-recovers.

    /** Active rates, sorted, cached. */
    public static function active(): \Illuminate\Support\Collection
    {
        return Cache::remember(self::CACHE_KEY_ACTIVE, self::CACHE_TTL, function () {
            return self::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('city_ar')
                ->get();
        });
    }

    /**
     * Look up the price for a city by Arabic name (canonical).
     * Returns null if the city has no active rate — callers must decide
     * whether to reject the order or fall back to a default.
     */
    public static function priceFor(string $cityAr): ?float
    {
        $cityAr = trim($cityAr);
        $rate = self::active()->firstWhere('city_ar', $cityAr);
        return $rate ? (float) $rate->price : null;
    }

    /** Invalidate the cache whenever a rate is created / updated / deleted. */
    protected static function booted(): void
    {
        $bust = fn () => Cache::forget(self::CACHE_KEY_ACTIVE);
        self::saved($bust);
        self::deleted($bust);
    }
}