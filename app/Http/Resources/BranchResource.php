<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'name_ar'       => $this->name_ar,
            'name_en'       => $this->name_en,
            'city_ar'       => $this->city_ar,
            'address_ar'    => $this->address_ar,
            'address_en'    => $this->address_en,
            'phone'         => $this->phone,
            'working_hours' => $this->working_hours,
            'latitude'      => $this->latitude,
            'longitude'     => $this->longitude,
        ];
    }
}
