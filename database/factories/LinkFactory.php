<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Link>
 */
class LinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'original_url' => fake()->url(),
            'short_code' => fake()->unique()->lexify('??????'),
            'click_count' => 0,
            'link_status_id' => \App\Models\LinkStatus::where('slug', 'active')->value('id') ?? 1,
        ];
    }

    /**
     * Indicate that the link should be active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'link_status_id' => \App\Models\LinkStatus::where('slug', 'active')->value('id') ?? 1,
        ]);
    }

    /**
     * Indicate that the link should be disabled.
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes) => [
            'link_status_id' => \App\Models\LinkStatus::where('slug', 'disabled')->value('id') ?? 2,
        ]);
    }

    /**
     * Indicate the user that owns the link.
     */
    public function forUser($id): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $id instanceof User ? $id->getKey() : $id,
        ]);
    }
}
