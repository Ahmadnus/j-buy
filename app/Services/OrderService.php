<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class OrderService
{
    private const SHIPPING_COST = 2.00;

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
            $products   = Product::whereIn('id', $productIds)->get()->keyBy('id');

            $productsTotal = collect($data['items'])->sum(
                fn ($item) => $products[$item['product_id']]->price * $item['quantity']
            );

            $totalAmount = $productsTotal + self::SHIPPING_COST;

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
                'shipping_cost'    => self::SHIPPING_COST,
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
                    'selected_size'  => $item['selected_size'],
                    'selected_color' => $item['selected_color'],
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
     * Must be called inside a DB transaction.
     * Format: JB-00001, JB-00002, ...
     */
    private function generateOrderNumber(): string
    {
        $last = Order::withTrashed()
                     ->lockForUpdate()
                     ->orderByDesc('id')
                     ->first();

        $next = $last
            ? (intval(substr($last->order_number, 3)) + 1)
            : 1;

        return 'JB-' . str_pad($next, 5, '0', STR_PAD_LEFT);
    }
}
