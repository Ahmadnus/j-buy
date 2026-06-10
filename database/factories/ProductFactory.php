<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'category_id'  => Category::factory(),
            'name_ar'      => fake()->words(3, true),
            'name_en'      => fake()->words(3, true),
            'product_code' => strtoupper(fake()->unique()->bothify('??-###')),
            'price'        => fake()->randomFloat(2, 2, 50),
            'currency'     => 'JOD',
            'image_url'    => 'https://picsum.photos/400/400',
            'material_ar'  => null,
            'badge'        => null,
            'size_range'   => 'S - M - L - XL',
            'rating'       => fake()->randomFloat(2, 3, 5),
            'review_count' => fake()->numberBetween(0, 500),
            'is_active'    => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
