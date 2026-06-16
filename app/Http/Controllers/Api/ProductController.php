<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponses;
use App\Http\Resources\ProductDetailResource;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ApiResponses;

    /**
     * GET /api/v1/products
     *
     * Query parameters:
     *   category   string   slug filter
     *   search     string   full-text search
     *   badge      string   badge filter
     *   sort       string   newest|oldest|price_asc|price_desc|rating|best_selling|popular
     *   per_page   int      default 20
     *   page       int      default 1
     */
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()?->id;

        $query = Product::active()
                        ->with('category')
                        ->withFavoriteStatus($userId);

        // ── Filters ───────────────────────────────────────────────────────────
        if ($request->filled('category')) {
            $query->forCategory($request->category);
        }
        if ($request->filled('search')) {
            $query->search($request->search);
        }
        if ($request->filled('badge')) {
            $query->where('badge', $request->badge);
        }

        // ── Sorting ───────────────────────────────────────────────────────────
        // All sort options use indexed columns so the DB doesn't do full scans.
        // For columns without a dedicated index, `id` is the tie-breaker which
        // the primary key index covers automatically.
        match ($request->input('sort', 'newest')) {
            'oldest'       => $query->orderBy('id',           'asc'),
            'price_asc'    => $query->orderBy('price',        'asc')->orderBy('id', 'desc'),
            'price_desc'   => $query->orderBy('price',        'desc')->orderBy('id', 'desc'),
            'rating'       => $query->orderBy('rating',       'desc')->orderBy('review_count', 'desc'),
            'best_selling' => $query->orderBy('review_count', 'desc')->orderBy('rating', 'desc'),
            'popular'      => $query->orderBy('review_count', 'desc')->orderBy('rating', 'desc'),
            default        => $query->orderBy('id',           'desc'), // newest
        };

        $perPage  = $request->integer('per_page', 20);
        $products = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => ProductResource::collection($products),
            'meta'    => [
                'current_page' => $products->currentPage(),
                'last_page'    => $products->lastPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
                'has_more'     => $products->hasMorePages(),
            ],
        ]);
    }

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