<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShippingRateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'      => $this->id,
            'city_ar' => $this->city_ar,
            'city_en' => $this->city_en,
            'price'   => number_format((float) $this->price, 3, '.', ''),
        ];
    }
}