<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ShippingRateResource;
use App\Models\ShippingRate;
use Illuminate\Http\JsonResponse;

class ShippingRateController extends ApiController
{
    /**
     * GET /api/v1/shipping-rates
     *
     * Returns all active rates. Mobile app calls this when the user opens
     * the cart so it can show city options + recompute total when the user
     * changes city. Not auth-gated — these are public pricing info.
     */
    public function index(): JsonResponse
    {
        $rates = ShippingRate::active();

        return response()->json([
            'success' => true,
            'data'    => ShippingRateResource::collection($rates),
        ]);
    }
}