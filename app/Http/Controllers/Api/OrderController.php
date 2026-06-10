<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Order\PlaceOrderRequest;
use App\Http\Resources\OrderDetailResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends ApiController
{
    public function __construct(private OrderService $orderService) {}

    // ── GET /orders ───────────────────────────────────────────────────────────

    public function index(Request $request): JsonResponse
    {
        $orders = Order::forUser($request->user()->id)
                       ->latest()
                       ->paginate(15);

        return response()->json([
            'success' => true,
            'data'    => OrderResource::collection($orders),
            'meta'    => [
                'current_page' => $orders->currentPage(),
                'last_page'    => $orders->lastPage(),
                'per_page'     => $orders->perPage(),
                'total'        => $orders->total(),
            ],
        ]);
    }

    // ── GET /orders/{id} ──────────────────────────────────────────────────────

    public function show(Request $request, int $id): JsonResponse
    {
        $order = Order::with('items')
                      ->where('user_id', $request->user()->id)
                      ->find($id);

        if (! $order) {
            return $this->notFound();
        }

        return $this->success(new OrderDetailResource($order));
    }

    // ── POST /orders ──────────────────────────────────────────────────────────

    public function store(PlaceOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->placeOrder(
            $request->user(),
            $request->validated()
        );

        return $this->created(
            new OrderDetailResource($order),
            'تم تأكيد طلبك بنجاح'
        );
    }
}
