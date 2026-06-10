<?php

namespace Database\Factories;

use App\Enums\OrderStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    private static int $counter = 0;

    public function definition(): array
    {
        self::$counter++;
        return [
            'order_number'     => 'JB-' . str_pad(self::$counter, 5, '0', STR_PAD_LEFT),
            'user_id'          => User::factory(),
            'customer_name'    => fake()->name(),
            'customer_phone'   => '079' . fake()->numerify('#######'),
            'customer_city'    => 'عمّان',
            'customer_address' => fake()->address(),
            'customer_notes'   => null,
            'payment_method'   => 'cod',
            'status'           => OrderStatus::Confirmed->value,
            'shipping_cost'    => 2.00,
            'total_amount'     => fake()->randomFloat(2, 5, 100),
        ];
    }
}
