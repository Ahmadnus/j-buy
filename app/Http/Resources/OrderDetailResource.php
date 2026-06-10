<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * OrderDetailResource — used on GET /orders/{id} and POST /orders.
 */
class OrderDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => (string) $this->id,
            'order_number'     => $this->order_number,
            'created_at'       => $this->created_at?->toIso8601String(),
            'status'           => $this->status->value,
            'status_label_ar'  => $this->status->labelAr(),
            'status_color'     => $this->status->color(),
            'customer_name'    => $this->customer_name,
            'customer_phone'   => $this->customer_phone,
            'customer_city'    => $this->customer_city,
            'customer_address' => $this->customer_address,
            'customer_notes'   => $this->customer_notes,
            'payment_method'   => $this->payment_method,
            'shipping_cost'    => number_format((float) $this->shipping_cost, 2, '.', ''),
            'products_total'   => number_format($this->products_total, 2, '.', ''),
            'total_amount'     => number_format((float) $this->total_amount, 2, '.', ''),
            'items_count'      => $this->items_count,
            'items'            => $this->items->map(fn($item) => [
                'id'             => $item->id,
                'product_id'     => $item->product_id,
                'name_ar'        => $item->name_ar,
                'price'          => number_format((float) $item->price, 2, '.', ''),
                'selected_size'  => $item->selected_size,
                'selected_color' => $item->selected_color,
                'image_url'      => $item->image_url,
                'quantity'       => $item->quantity,
            ])->values(),
        ];
    }
}
