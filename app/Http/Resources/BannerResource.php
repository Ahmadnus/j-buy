<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BannerResource extends JsonResource {
    public function toArray(Request $request): array {
        return [
            'id'               => $this->id,
            'title_ar'         => $this->title_ar,
            'title_en'         => $this->title_en,
            'subtitle_ar'      => $this->subtitle_ar,
            'subtitle_en'      => $this->subtitle_en,
            'cta_text_ar'      => $this->cta_text_ar,
            'cta_text_en'      => $this->cta_text_en,
            'image_url'        => $this->image_url,
            'background_color' => $this->background_color,
            'link_type'        => $this->link_type,
            'link_value'       => $this->link_value,
        ];
    }
}