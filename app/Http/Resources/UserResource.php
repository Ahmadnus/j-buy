<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Shapes the User model into the exact JSON structure Flutter reads.
 * Computed fields (orders_count etc.) use Eloquent accessors on User model.
 */
class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                    => $this->id,
            'name_ar'               => $this->name_ar,
            'username'              => $this->username,
            'email'                 => $this->email,
            'phone'                 => $this->phone,
            'address'               => $this->address,
            // avatar_url accessor on User model returns Media Library URL first
            'avatar_url'            => $this->avatar_url,
            // Flutter reads 'gold'/'silver'/'standard' then converts to Arabic label
            'membership_tier'       => $this->membership_tier?->value ?? 'standard',
            'notifications_enabled' => (bool) $this->notifications_enabled,
            // Computed via User model accessors — never stored columns
            'orders_count'          => $this->orders_count,
            'wishlist_count'        => $this->wishlist_count,
            'active_orders_count'   => $this->active_orders_count,
        ];
    }
}
