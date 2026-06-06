<?php

namespace Database\Factories;

use App\Models\Browser;
use App\Models\Click;
use App\Models\DeviceType;
use App\Models\Link;
use Illuminate\Database\Eloquent\Factories\Factory;

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
            'device_type_id' => $this->resolveLookupId(DeviceType::class, 'desktop'),
            'browser_id' => $this->resolveLookupId(Browser::class, 'chrome'),
            'referrer' => fake()->url(),
            'ip_hash' => fake()->sha256(),
            'clicked_at' => fake()->datetimeBetween('-7 days', now()),
        ];
    }

    /**
     * Resolve a lookup record ID, falling back to any existing record.
     */
    protected function resolveLookupId(string $model, string $fallbackSlug): ?int
    {
        return $model::where('slug', $fallbackSlug)->value('id')
            ?? $model::inRandomOrder()->value('id');
    }

    /**
     * Indicate that the click should be tied to a specific link.
     */
    public function forLink(int|string|Link $link): static
    {
        return $this->state(fn (array $attributes) => [
            'link_id' => $link instanceof Link ? $link->getKey() : $link,
        ]);
    }

    /**
     * Indicate the device type for the click.
     */
    public function onDevice(string $slug): static
    {
        return $this->state(fn (array $attributes) => [
            'device_type_id' => DeviceType::where('slug', $slug)->value('id'),
        ]);
    }

    /**
     * Indicate the browser for the click.
     */
    public function withBrowser(string $slug): static
    {
        return $this->state(fn (array $attributes) => [
            'browser_id' => Browser::where('slug', $slug)->value('id'),
        ]);
    }

    /**
     * Indicate the click happened recently (within last hour).
     */
    public function recent(): static
    {
        return $this->state(fn (array $attributes) => [
            'clicked_at' => fake()->datetimeBetween('-1 hours', now()),
        ]);
    }

    /**
     * Indicate the click happened today.
     */
    public function today(): static
    {
        return $this->state(fn (array $attributes) => [
            'clicked_at' => fake()->dateTimeBetween('today', 'tomorrow'),
        ]);
    }

    /**
     * Indicate the click happened this month.
     */
    public function thisMonth(): static
    {
        return $this->state(fn (array $attributes) => [
            'clicked_at' => fake()->dateTimeBetween('-1 month', now()),
        ]);
    }

    /**
     * Set a specific click date/time.
     */
    public function onDate(\DateTimeInterface $date): static
    {
        return $this->state(fn (array $attributes) => [
            'clicked_at' => $date,
        ]);
    }
}
