<?php

namespace Database\Factories;

use App\Models\Browser;
use App\Models\Click;
use App\Models\DeviceType;
use App\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

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
            'device_type_id' => fn () => $this->lookupId(DeviceType::class, 'Desktop'),
            'browser_id' => fn () => $this->lookupId(Browser::class, 'Chrome'),
            'referrer' => fake()->optional()->url(),
            'ip_hash' => hash('sha256', fake()->ipv4()),
            'clicked_at' => now(),
        ];
    }

    /**
     * Indicate that the click happened recently (useful for chart windows).
     */
    public function recent(int $withinDays = 7): static
    {
        return $this->state(fn (array $attributes): array => [
            'clicked_at' => fake()->dateTimeBetween("-{$withinDays} days", 'now'),
        ]);
    }

    /**
     * Indicate that the click happened on the given date.
     */
    public function onDate(Carbon $date): static
    {
        return $this->state(fn (array $attributes): array => [
            'clicked_at' => $date,
        ]);
    }

    /**
     * Resolve (or create) a lookup row id by name for the given model class.
     *
     * @param  class-string<Model>  $model
     */
    protected function lookupId(string $model, string $name): int
    {
        return $model::firstOrCreate(
            ['slug' => Str::slug($name)],
            ['name' => $name],
        )->id;
    }
}
