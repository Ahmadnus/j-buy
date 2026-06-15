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
        // Build image_urls gallery — Spatie Media Library first, then product_images table.
        // The main image_url is always prepended so it appears first in the slider.
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

        // Always prepend the primary image_url so existing products that have
        // no sub-images still show one image in the slider. When sub-images
        // exist, the main image comes first followed by the gallery images.
        if ($this->image_url) {
            array_unshift($galleryUrls, $this->image_url);
            // Deduplicate in case image_url was already included in the gallery
            $galleryUrls = array_values(array_unique($galleryUrls));
        }

        // Safety: if somehow we still have nothing, return an empty array.
        // Flutter's ProductImageCarousel handles the empty case with a placeholder icon.

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