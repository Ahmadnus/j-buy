<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductResource;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FavoriteController extends ApiController
{
    // ── GET /favorites ────────────────────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $products = Product::active()
            ->whereHas('favoritedByUsers', fn($q) => $q->where('users.id', $userId))
            ->with('category')
            ->withFavoriteStatus($userId)
            ->get();

        return $this->success(ProductResource::collection($products));
    }

    // ── POST /favorites/{productId} — toggle ──────────────────────────────────

    public function toggle(Request $request, int $productId): JsonResponse
    {
        $product = Product::active()->find($productId);
        if (! $product) {
            return $this->notFound();
        }

        $user     = $request->user();
        $existing = Favorite::where('user_id', $user->id)
                             ->where('product_id', $productId)
                             ->first();

        if ($existing) {
            $existing->delete();
            $isFavorite = false;
        } else {
            Favorite::create(['user_id' => $user->id, 'product_id' => $productId]);
            $isFavorite = true;
        }

        return $this->success([
            'is_favorite' => $isFavorite,
            'product_id'  => $productId,
        ]);
    }

    // ── DELETE /favorites/{productId} ─────────────────────────────────────────

    public function destroy(Request $request, int $productId): JsonResponse
    {
        Favorite::where('user_id', $request->user()->id)
                ->where('product_id', $productId)
                ->delete();

        return $this->noContent();
    }
}
