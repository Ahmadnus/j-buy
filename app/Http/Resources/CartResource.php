<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * CartResource wraps a User model's cart items into the documented cart envelope.
 * Used by all cart mutation endpoints (they must return the updated cart).
 *
 * Flutter reads:
 *   data.items[], data.subtotal, data.shipping_cost, data.total, data.item_count
 *
 * Note on shipping:
 * The cart endpoint doesn't know which city the user will ship to (that's
 * chosen at checkout), so we report a *default* shipping_cost based on the
 * user's stored region — and the mobile app overrides this with the
 * city-specific rate via GET /shipping-rates when the user picks a city.
 * The authoritative shipping cost is computed server-side at order-placement
 * time by OrderService, so even if the client sends a wrong total we charge
 * the right amount.
 */
class CartResource extends JsonResource
{
    /** @param \App\Models\User $resource */
    public function toArray(Request $request): array
    {
        $items    = $this->cartItems;
        $subtotal = $items->sum(fn($i) => $i->total_price);

        // Pick shipping based on the user's stored region; falls back to 2.000
        // (the original constant) if region is empty / not in the table.
        $userRegion = $this->resource->region ?? '';
        $shipping = $userRegion
            ? (\App\Models\ShippingRate::priceFor($userRegion) ?? 2.000)
            : 2.000;

        return [
            'items' => $items->map(fn($item) => [
                'id'             => $item->id,
                'product_id'     => $item->product_id,
                'name_ar'        => $item->name_ar,
                'price'          => number_format((float) $item->price, 2, '.', ''),
                'currency'       => $item->currency,
                'image_url'      => $item->image_url,
                'selected_size'  => $item->selected_size,
                'selected_color' => $item->selected_color,
                'quantity'       => $item->quantity,
                'total_price'    => number_format($item->total_price, 2, '.', ''),
            ])->values(),
            'subtotal'      => number_format($subtotal, 2, '.', ''),
            'shipping_cost' => number_format($shipping, 2, '.', ''),
            'total'         => number_format($subtotal + $shipping, 2, '.', ''),
            'item_count'    => (int) $items->sum('quantity'),
        ];
    }
}