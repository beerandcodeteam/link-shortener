<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\LinkStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'link_status_id' => fn () => $this->statusId('active'),
            'original_url' => fake()->url(),
            'short_code' => Str::lower(Str::random(7)),
            'click_count' => 0,
        ];
    }

    /**
     * Indicate that the link is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes): array => [
            'link_status_id' => $this->statusId('active'),
        ]);
    }

    /**
     * Indicate that the link is disabled.
     */
    public function disabled(): static
    {
        return $this->state(fn (array $attributes): array => [
            'link_status_id' => $this->statusId('disabled'),
        ]);
    }

    /**
     * Assign the link to the given user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes): array => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Resolve (or create) the link status id for the given slug.
     */
    protected function statusId(string $slug): int
    {
        return LinkStatus::firstOrCreate(
            ['slug' => $slug],
            ['name' => Str::title($slug), 'is_active' => $slug === 'active'],
        )->id;
    }
}
