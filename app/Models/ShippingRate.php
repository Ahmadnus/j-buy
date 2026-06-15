<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingRate extends Model
{
    protected $fillable = ['city_ar', 'city_en', 'price', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return [
            'price'      => 'decimal:3',
            'is_active'  => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /**
     * All active rates, sorted.
     *
     * NO cache — the table has at most 12 rows (one per Jordanian governorate)
     * so a direct DB query is negligible. Caching Eloquent model collections
     * causes "__PHP_Incomplete_Class" errors on shared hosting when the cache
     * file was written under a different PHP version or before autoload was
     * complete. Removing the cache eliminates that failure entirely.
     */
    public static function active(): \Illuminate\Support\Collection
    {
        return self::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('city_ar')
            ->get();
    }

    /**
     * Look up the shipping price for a city by its canonical Arabic name.
     * Returns null if no active rate exists for that city — callers should
     * fall back to the FALLBACK_SHIPPING constant in OrderService.
     */
    public static function priceFor(string $cityAr): ?float
    {
        $cityAr = trim($cityAr);
        if ($cityAr === '') return null;

        $rate = self::query()
            ->where('is_active', true)
            ->where('city_ar', $cityAr)
            ->value('price');

        return $rate !== null ? (float) $rate : null;
    }

    /**
     * Invalidate is a no-op now (kept so any existing calls don't break).
     */
    protected static function booted(): void
    {
        // No cache to bust.
    }
}