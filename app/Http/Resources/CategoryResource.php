<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'           => $this->id,
            'name_ar'      => $this->name_ar,
            'name_en'      => $this->name_en,
            'slug'         => $this->slug,
            'icon'         => $this->icon,
            'image_url'    => $this->image_url,
            // > 0 means this category is a phone brand with models to browse
            'models_count' => (int) ($this->phone_models_count ?? 0),
        ];
    }
}
