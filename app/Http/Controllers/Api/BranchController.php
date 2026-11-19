<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BranchResource;
use App\Http\Traits\ApiResponses;
use App\Models\Branch;
use Illuminate\Http\JsonResponse;

class BranchController extends Controller
{
    use ApiResponses;

    /** GET /api/v1/branches — active pickup branches. */
    public function index(): JsonResponse
    {
        return $this->success(BranchResource::collection(Branch::active()->get()));
    }
}
