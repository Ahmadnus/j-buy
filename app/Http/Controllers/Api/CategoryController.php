<?php
namespace App\Http\Controllers\Api;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends ApiController {
    public function index(): JsonResponse {
        $categories = Category::active()->get();
        return $this->success(CategoryResource::collection($categories));
    }
}
