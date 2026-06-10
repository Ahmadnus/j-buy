<?php

namespace Database\Factories;

use App\Enums\MembershipTier;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name_ar'               => fake()->name(),
            'username'              => fake()->unique()->userName(),
            'email'                 => fake()->unique()->safeEmail(),
            'phone'                 => '079' . fake()->numerify('#######'),
            'address'               => fake()->address(),
            'avatar_url'            => null,
            'membership_tier'       => MembershipTier::Standard->value,
            'notifications_enabled' => true,
            'password'              => bcrypt('password'),
            'remember_token'        => Str::random(10),
        ];
    }
}
