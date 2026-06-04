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
            'link_status_id' => LinkStatus::factory(),
            'original_url' => fake()->url(),
            'short_code' => Str::lower(Str::random(8)),
            'click_count' => 0,
        ];
    }

    /**
     * Indicate that the link uses the "active" status.
     */
    public function active(): static
    {
        return $this->state(fn (): array => [
            'link_status_id' => LinkStatus::firstOrCreate(
                ['slug' => 'active'],
                ['name' => 'Active', 'description' => 'Link is live and clickable.', 'is_active' => true],
            ),
        ]);
    }

    /**
     * Indicate that the link uses the "disabled" status.
     */
    public function disabled(): static
    {
        return $this->state(fn (): array => [
            'link_status_id' => LinkStatus::firstOrCreate(
                ['slug' => 'disabled'],
                ['name' => 'Disabled', 'description' => 'Link is disabled and returns 404.', 'is_active' => false],
            ),
        ]);
    }

    /**
     * Indicate that the link belongs to the given user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (): array => [
            'user_id' => $user->id,
        ]);
    }
}
