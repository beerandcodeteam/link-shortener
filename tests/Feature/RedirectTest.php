<?php

use App\Models\Link;
use Database\Seeders\LookupSeeder;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('redirects an active short code (302) to the original URL', function () {
    $link = Link::factory()->active()->create([
        'short_code' => 'abc1234',
        'original_url' => 'https://example.com/destination',
    ]);

    $response = $this->get('/abc1234');

    $response->assertStatus(302);
    $response->assertRedirect('https://example.com/destination');
});

it('returns 404 for an unknown short code', function () {
    $response = $this->get('/does-not-exist');

    $response->assertStatus(404);
});

it('renders the unavailable page for a disabled short code and does not redirect', function () {
    $link = Link::factory()->disabled()->create([
        'short_code' => 'offline1',
        'original_url' => 'https://example.com/should-not-go-here',
    ]);

    $response = $this->get('/offline1');

    $response->assertOk();
    $response->assertDontSee('should-not-go-here', false);
    $response->assertSee('no longer available', false);
    $response->assertSee('/offline1', false);
    expect($response->isRedirect())->toBeFalse();
});

it('does not capture the /login app route', function () {
    $response = $this->get('/login');

    $captured = $response->isRedirect()
        && str_starts_with((string) $response->headers->get('Location'), 'http');

    expect($captured)->toBeFalse();
});

it('does not capture the /dashboard app route', function () {
    $response = $this->get('/dashboard');

    // /dashboard is a named, auth-protected route. The catch-all short-link
    // handler must not match it: a guest hit either returns the dashboard
    // page or, when auth is enforced, a 302 to the login route on the same
    // host (never a 302 to a user-supplied original URL).
    expect($response->isRedirect())->toBeTrue();

    $location = (string) $response->headers->get('Location');

    expect($location)->toEndWith('/login');
});
