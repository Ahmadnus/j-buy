<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponses;
use App\Http\Requests\Order\PlaceOrderRequest;
use App\Http\Resources\OrderDetailResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use ApiResponses;

    public function __construct(private OrderService $orderService) {}

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

    public function store(PlaceOrderRequest $request): JsonResponse
    {
        $order = $this->orderService->placeOrder(
            $request->user(),
            $request->validated()
        );

        $isEn = str_starts_with((string) $request->header('Accept-Language', 'ar'), 'en');
        $msg  = $isEn ? 'Your order has been placed successfully' : 'تم تأكيد طلبك بنجاح';

        return $this->created(new OrderDetailResource($order), $msg);
    }
}