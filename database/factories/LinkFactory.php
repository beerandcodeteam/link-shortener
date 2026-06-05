<?php

namespace Database\Factories;

use App\Models\Link;
use App\Models\LinkStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class LinkFactory extends Factory
{
    /**
     * The name of the model.*
     *
     * @var string
     */
    protected $model = Link::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'link_status_id' => LinkStatus::factory(),
            'original_url' => 'https://example.com/some-long-path',
            'short_code' => \Illuminate\Support\Str::random(8),
            'click_count' => 0,
        ];
    }

    /**
     * Indicate that the link is active.
     */
    public function active(): self
    {
        return $this->definition()
            ->merge(['link_status_id' => LinkStatus::factory()->definition(['is_active' => true])]);
    }

    /**
     * Indicate that the link is disabled.
     */
    public function disabled(): self
    {
        return $this->definition()
            ->merge(['link_status_id' => LinkStatus::factory()->definition(['is_active' => false])]);
    }

    /**
     * Create a link for a specific user.
     */
    public function forUser(User $user): self
    {
        return $this->definition()
            ->merge(['user_id' => $user->id]);
    }
}
