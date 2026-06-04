<?php

namespace Database\Factories;

use App\Models\Browser;
use App\Models\Click;
use App\Models\DeviceType;
use App\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<Click>
 */
class ClickFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'link_id' => Link::factory(),
            'device_type_id' => DeviceType::inRandomOrder()->value('id') ?? DeviceType::factory(),
            'browser_id' => Browser::inRandomOrder()->value('id') ?? Browser::factory(),
            'referrer' => fake()->boolean(40) ? fake()->url() : null,
            'ip_hash' => Hash::make(fake()->ipv4()),
            'clicked_at' => now()->subMinutes(fake()->numberBetween(0, 60 * 24 * 30)),
        ];
    }

    /**
     * Indicate the click happened within the last 24 hours.
     */
    public function recent(): static
    {
        return $this->state(fn (): array => [
            'clicked_at' => now()->subMinutes(fake()->numberBetween(0, 60 * 24)),
        ]);
    }
}
