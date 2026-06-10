<?php

namespace App\Models;

use App\Enums\MembershipTier;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class User extends Authenticatable implements HasMedia
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'name_ar',
        'username',
        'email',
        'phone',
        'address',
        'avatar_url',
        'membership_tier',
        'notifications_enabled',
        'password',
    ];

    protected $hidden = ['password', 'remember_token'];

    // email_verified_at removed — no verification flow
    protected function casts(): array
    {
        return [
            'notifications_enabled' => 'boolean',
            'membership_tier'       => MembershipTier::class,
            'deleted_at'            => 'datetime',
        ];
    }

    // ── Spatie Media Library ──────────────────────────────────────────────────

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatars')
             ->singleFile()
             ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
             ->width(300)
             ->height(300)
             ->performOnCollections('avatars');
    }

    /**
     * Avatar URL — prefers Spatie Media Library, falls back to avatar_url column.
     * Flutter reads: data.avatar_url (from UserResource)
     */
    public function getAvatarUrlAttribute(mixed $value): ?string
    {
        $mediaUrl = $this->getFirstMediaUrl('avatars', 'thumb');
        return $mediaUrl ?: $value;
    }

    // ── Relationships ─────────────────────────────────────────────────────────

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class)->latest();
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function favorites(): HasMany
    {
        return $this->hasMany(Favorite::class);
    }

    public function favoriteProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'favorites')
                    ->withPivot('created_at');
    }

    public function deviceTokens(): HasMany
    {
        return $this->hasMany(DeviceToken::class);
    }

    // ── Computed attributes (used by UserResource) ────────────────────────────

    public function getOrdersCountAttribute(): int
    {
        return $this->orders()->withoutTrashed()->count();
    }

    public function getWishlistCountAttribute(): int
    {
        return $this->favorites()->count();
    }

    public function getActiveOrdersCountAttribute(): int
    {
        return $this->orders()
                    ->withoutTrashed()
                    ->whereNotIn('status', ['delivered', 'cancelled'])
                    ->count();
    }
}
