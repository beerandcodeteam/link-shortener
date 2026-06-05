<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\LinkStatus;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RedirectTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that an active link redirects to its original URL.
     */
    public function test_active_link_redirects(): void
    {
        $status = LinkStatus::create([
            'name' => 'Active',
            'slug' => 'active',
            'description' => 'An active link',
            'is_active' => true,
        ]);

        $link = Link::create([
            'user_id' => 1,
            'link_status_id' => $status->id,
            'original_url' => 'https://google.com',
            'short_code' => 'google',
        ]);

        $response = $this->get('/google');

        $response->assertRedirect();
        $response->assertStatus(302);
        // Note: Laravel's redirect()->away() might append a trailing slash or keep the protocol.
        // We check if it contains the core domain/path correctly.
        $this->assertStringContainsString('google.com', $response->headers->get('Location'));
    }

    /**
     * Test that a non-existent code returns 404.
     */
    public function test_missing_link_returns_404(): void
    {
        $response = $this->get('/nonexistent');

        $response->assertStatus(404);
    }

    /**
     * Test that a disabled link shows the unavailable page.
     */
    public function test_disabled_link_shows_unavailable(): void
    {
        $status = LinkStatus::create([
            'name' => 'Disabled',
            'slug' => 'disabled',
            'description' => 'A disabled link',
            'is_active' => false,
        ]);

        $link = Link::create([
            'user_id' => 1,
            'link_status_id' => $status->id,
            'original_url' => 'https://google.com',
            'short_code' => 'disabled-link',
        ]);

        $response = $this->get('/disabled-link');

        $response->assertStatus(200); // Not a redirect
    }

    /**
     * Test that standard application routes are not hijacked.
     */
    public function test_app_routes_are_not_hijacked(): void
    {
        // Standard login/dashboard should be reachable or at least NOT lead to 302 redirects from the redirect logic
        $this->get('/login')->assertStatus(200); // Adjust if default is something else but not a redirect-to-somewhere-else.
        $this->get('/dashboard')->assertStatus(200);
    }
}
