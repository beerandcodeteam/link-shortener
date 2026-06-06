<?php

namespace Database\Factories;

use App\Models\LinkStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LinkStatus>
 */
class LinkStatusFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->word(),
            'slug' => fake()->unique()->slug(),
            'description' => fake()->sentence(),
            'is_active' => fake()->boolean(),
        ];
    }

    /**
     * Indicate the link status is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
            'slug' => 'active',
            'name' => 'Active',
        ]);
    }

    /**
     * Indicate the link status is disabled.
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
            'slug' => 'disabled',
            'name' => 'Disabled',
        ]);
    }
}
