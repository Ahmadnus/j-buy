<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends ApiController
{
    // ── GET /products ─────────────────────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;

        $query = Product::active()
                        ->with('category')
                        ->withFavoriteStatus($userId);

        if ($request->filled('category')) {
            $query->forCategory($request->category);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('badge')) {
            $query->where('badge', $request->badge);
        }

        $products = $query->paginate($request->integer('per_page', 15));

        return response()->json([
            'success' => true,
            'data'    => ProductResource::collection($products),
            'meta'    => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
            ],
        ]);
    }

    // ── GET /products/{id} ────────────────────────────────────────────────────

    public function show(Request $request, int $id): JsonResponse
    {
        $userId = $request->user()?->id;

        $product = Product::active()
                          ->with(['category', 'images', 'colors', 'sizes'])
                          ->withFavoriteStatus($userId)
                          ->find($id);

        if (! $product) {
            return $this->notFound();
        }

        return $this->success(new ProductDetailResource($product));
    }
}
