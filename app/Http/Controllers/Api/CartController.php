<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use App\Http\Requests\Cart\AddToCartRequest;
use App\Http\Requests\Cart\UpdateCartRequest;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use App\Http\Traits\ApiResponses;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponses;

    public function __construct(private CartService $cartService) {}

    public function index(Request $request): JsonResponse
    {
        $user = $this->cartService->loadCart($request->user());
        return $this->success(new CartResource($user));
    }

    public function store(AddToCartRequest $request): JsonResponse
    {
        $user = $request->user();
        $this->cartService->addItem($user, $request->validated());
        $user = $this->cartService->loadCart($user);
        return $this->success(new CartResource($user));
    }

    public function update(UpdateCartRequest $request, int $id): JsonResponse
    {
        $user = $request->user();
        $item = $this->cartService->updateItem($user, $id, $request->quantity);

        if (! $item) {
            return $this->notFound();
        }

        $user = $this->cartService->loadCart($user);
        return $this->success(new CartResource($user));
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $user = $request->user();
        $this->cartService->removeItem($user, $id);
        $user = $this->cartService->loadCart($user);
        return $this->success(new CartResource($user));
    }

    public function clear(Request $request): JsonResponse
    {
        $this->cartService->clearCart($request->user());
        return $this->noContent();
    }
}