<?php

namespace Database\Factories;

use App\Models\Browser;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Browser>
 */
class BrowserFactory extends Factory
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
        ];
    }
}
