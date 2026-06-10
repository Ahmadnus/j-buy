<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * ProductDetailResource — used on GET /products/{id}.
 * Includes image_urls gallery, colors array, sizes array.
 */
class ProductDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        // Gallery: prefer Spatie Media Library, fallback to product_images table
        $galleryUrls = $this->getMedia('product-gallery')
            ->map(fn($m) => $m->getFullUrl())
            ->values()
            ->toArray();

        if (empty($galleryUrls)) {
            $galleryUrls = $this->images
                ->pluck('image_url')
                ->values()
                ->toArray();
        }

        // Ensure primary image_url is first in gallery if gallery is empty
        if (empty($galleryUrls) && $this->image_url) {
            $galleryUrls = [$this->image_url];
        }

        return [
            'id'           => $this->id,
            'name_ar'      => $this->name_ar,
            'name_en'      => $this->name_en,
            'product_code' => $this->product_code,
            'price'        => number_format((float) $this->price, 2, '.', ''),
            'currency'     => $this->currency,
            'size_range'   => $this->size_range,
            'image_urls'   => $galleryUrls,
            'colors'       => $this->colors->map(fn($c) => [
                'id'       => $c->id,
                'name_ar'  => $c->name_ar,
                'name_en'  => $c->name_en,
                'hex_code' => $c->hex_code,
            ])->values(),
            'sizes' => $this->sizes->map(fn($s) => [
                'id'           => $s->id,
                'label'        => $s->label,
                'is_available' => (bool) $s->is_available,
            ])->values(),
            'category' => $this->whenLoaded('category', fn() => [
                'id'      => $this->category->id,
                'name_ar' => $this->category->name_ar,
                'slug'    => $this->category->slug,
            ]),
            'rating'       => number_format((float) $this->rating, 2, '.', ''),
            'review_count' => (int) $this->review_count,
            'is_favorite'  => (bool) ($this->is_favorite ?? false),
            'material_ar'  => $this->material_ar,
            'material_en'  => $this->material_en,
            'badge'        => $this->badge,
            'badge_en'     => $this->badge_en,
        ];
    }
}