<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingRate;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Fallback shipping cost used only when an order arrives for a city that
     * has no active rate configured. We don't reject the order in that case
     * because the rate table can change between when the user sees the cart
     * and when they tap "place order" — we'd rather charge a known default
     * than blow up at checkout.
     */
    private const FALLBACK_SHIPPING = 2.000;

    /**
     * Place an order from validated checkout data.
     * Generates a sequential order number inside a DB transaction with lock.
     *
     * @param  \App\Models\User  $user
     * @param  array             $data  validated from PlaceOrderRequest
     * @return \App\Models\Order
     */
    public function placeOrder(\App\Models\User $user, array $data): Order
    {
        return DB::transaction(function () use ($user, $data) {

            // 1. Generate collision-safe sequential order number
            $orderNumber = $this->generateOrderNumber();

            // 2. Fetch real product prices (never trust client-sent prices)
            $productIds = collect($data['items'])->pluck('product_id')->unique();
            $products   = Product::active()->whereIn('id', $productIds)->get()->keyBy('id');

            // Reject the whole order if any item references a missing/inactive product
            if ($products->count() !== $productIds->count()) {
                $missing = $productIds->diff($products->keys())->values()->all();
                throw new \Illuminate\Validation\ValidationException(
                    validator(
                        ['items' => $data['items']],
                        ['items' => 'required'],
                    ),
                    response()->json([
                        'success' => false,
                        'message' => 'بعض المنتجات في طلبك غير متاحة',
                        'errors'  => ['items' => ['Missing or inactive products: ' . implode(', ', $missing)]],
                    ], 422)
                );
            }

            $productsTotal = collect($data['items'])->sum(
                fn ($item) => $products[$item['product_id']]->price * $item['quantity']
            );

            // 2b. Look up shipping cost from the rates table — falls back to
            // the constant if the city isn't configured. The rate is captured
            // on the order row so historical orders show the price the
            // customer actually paid, not whatever the admin changes it to
            // next week.
            $shippingCost = ShippingRate::priceFor($data['city']) ?? self::FALLBACK_SHIPPING;

            $totalAmount = $productsTotal + $shippingCost;

            // 3. Create the order
            /** @var Order $order */
            $order = Order::create([
                'order_number'     => $orderNumber,
                'user_id'          => $user->id,
                'customer_name'    => $data['full_name'],
                'customer_phone'   => $data['phone'],
                'customer_city'    => $data['city'],
                'customer_address' => $data['address'],
                'customer_notes'   => $data['notes'] ?? null,
                'payment_method'   => $data['payment_method'],
                'status'           => OrderStatus::Confirmed,
                'shipping_cost'    => $shippingCost,
                'total_amount'     => $totalAmount,
            ]);

            // 4. Create snapshot order items
            foreach ($data['items'] as $item) {
                $product = $products[$item['product_id']];
                $order->items()->create([
                    'product_id'     => $product->id,
                    'name_ar'        => $product->name_ar,
                    'name_en'        => $product->name_en,
                    'price'          => $product->price,
                    'currency'       => $product->currency,
                    'image_url'      => $product->image_url,
                    'selected_size'  => $item['selected_size']  ?? '',
                    'selected_color' => $item['selected_color'] ?? '',
                    'quantity'       => $item['quantity'],
                ]);
            }

            // 5. Write initial status log
            $order->statusLogs()->create([
                'from_status' => null,
                'to_status'   => OrderStatus::Confirmed->value,
                'changed_by'  => null,
                'note'        => 'تم تأكيد الطلب',
            ]);

            return $order->load('items');
        });
    }

    /**
     * Generate the next sequential order number.
     * Must be called inside a DB transaction with lockForUpdate().
     * Format: JB-00001, JB-00002, ...
     *
     * Uses the highest numeric suffix found, ignoring rows whose order_number
     * does not match the JB-xxxxx pattern (robust to manual inserts / imports).
     */
    private function generateOrderNumber(): string
    {
        $last = Order::withTrashed()
                     ->lockForUpdate()
                     ->where('order_number', 'like', 'JB-%')
                     ->orderByRaw('CAST(SUBSTRING(order_number, 4) AS UNSIGNED) DESC')
                     ->first();

        $next = 1;
        if ($last && preg_match('/^JB-(\d+)$/', $last->order_number, $m)) {
            $next = (int) $m[1] + 1;
        }

        return 'JB-' . str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }
}