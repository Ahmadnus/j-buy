<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ProductResource — used on GET /products (list).
 * Includes is_favorite which is 0/false for guests.
 * The product model has a withFavoriteStatus() scope that adds the column.
 */
class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name_ar'      => $this->name_ar,
            'name_en'      => $this->name_en,
            'price'        => number_format((float) $this->price, 2, '.', ''),
            'currency'     => $this->currency,
            'image_url'    => $this->image_url,
            'rating'       => number_format((float) $this->rating, 2, '.', ''),
            'review_count' => (int) $this->review_count,
            'category'     => $this->whenLoaded('category', fn() => [
                'id'      => $this->category->id,
                'name_ar' => $this->category->name_ar,
                'slug'    => $this->category->slug,
            ]),
            'material_ar'  => $this->material_ar,
            'badge'        => $this->badge,
            // Comes from scopeWithFavoriteStatus() — 0 for guests
            'is_favorite'  => (bool) ($this->is_favorite ?? false),
        ];
    }
}
