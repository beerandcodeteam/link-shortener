<?php

namespace Database\Factories;

use App\Models\LinkStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $name = fake()->unique()->words(2, true);

        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name).'-'.fake()->unique()->bothify('###'),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
