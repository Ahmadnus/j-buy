<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * PhoneModelResource — a phone model with (optionally) the spare parts that
 * fit it. The list endpoint embeds a short preview of parts so the app can
 * render "parts + price under each model name" without an extra request.
 */
class PhoneModelResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name_ar'      => $this->name_ar,
            'name_en'      => $this->name_en,
            'image_url'    => $this->image_url,
            'release_year' => $this->release_year,
            'brand'        => $this->whenLoaded('category', fn () => [
                'id'      => $this->category->id,
                'name_ar' => $this->category->name_ar,
                'name_en' => $this->category->name_en,
                'slug'    => $this->category->slug,
            ]),
            'parts_count'  => (int) ($this->products_count ?? 0),
            'min_price'    => $this->products_min_price !== null
                ? number_format((float) $this->products_min_price, 2, '.', '')
                : null,
            'parts'        => ProductResource::collection($this->whenLoaded('products')),
        ];
    }
}
