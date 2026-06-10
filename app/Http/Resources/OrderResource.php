<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * OrderResource — used on GET /orders (list).
 * Matches the exact JSON shape from the API spec.
 */
class OrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => (string) $this->id,
            'order_number'  => $this->order_number,
            'created_at'    => $this->created_at?->toIso8601String(),
            'status'        => $this->status->value,
            'status_label_ar' => $this->status->labelAr(),
            'status_color'  => $this->status->color(),
            'total_amount'  => number_format((float) $this->total_amount, 2, '.', ''),
            'shipping_cost' => number_format((float) $this->shipping_cost, 2, '.', ''),
            'items_count'   => $this->items_count,
            'payment_method' => $this->payment_method,
        ];
    }
}
