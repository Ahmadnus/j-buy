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
 */
class CartResource extends JsonResource
{
    /** @param \App\Models\User $resource */
    public function toArray(Request $request): array
    {
        $items    = $this->cartItems;
        $subtotal = $items->sum(fn($i) => $i->total_price);
        $shipping = 2.00;

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
