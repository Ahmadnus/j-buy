<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name_ar'    => fake()->word(),
            'name_en'    => fake()->word(),
            'slug'       => fake()->unique()->slug(1),
            'icon'       => 'category_outlined',
            'sort_order' => 0,
            'is_active'  => true,
        ];
    }
}
