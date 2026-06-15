<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponses;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Http\JsonResponse;

class BannerController extends Controller
{
    use ApiResponses;

    public function index(): JsonResponse
    {
        $banners = Banner::active()->get();
        return $this->success(BannerResource::collection($banners));
    }
}