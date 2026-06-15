<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponses;
use App\Http\Resources\ShippingRateResource;
use App\Models\ShippingRate;
use Illuminate\Http\JsonResponse;

class ShippingRateController extends Controller
{
    use ApiResponses;

    /**
     * GET /api/v1/shipping-rates
     * Public — no auth required.
     */
    public function index(): JsonResponse
    {
        $rates = ShippingRate::active();
        return $this->success(ShippingRateResource::collection($rates));
    }
}