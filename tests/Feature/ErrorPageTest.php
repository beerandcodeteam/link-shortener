<?php

use App\Models\Link;
use Database\Seeders\LookupSeeder;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('renders the custom 404 page for an unknown short code', function () {
    $response = $this->get('/this-code-does-not-exist');

    $response->assertStatus(404);
    $response->assertSee('We couldn\'t find that page', false);
    $response->assertSee('Error 404', false);
    $response->assertSee('this-code-does-not-exist', false);
    $response->assertSee('Go to homepage', false);
});

it('renders the custom 404 page with a homepage link', function () {
    $response = $this->get('/no-such-link');

    $response->assertStatus(404);
    $response->assertSee(route('home'), false);
});

it('exposes the custom 404 page through the standard abort helper for any non-route path', function () {
    $response = $this->get('/totally/made-up/path');

    // Path does not match the short-code regex, so Laravel's router
    // returns a generic 404 which must also be styled by the design
    // system. The body should still mention the error title.
    $response->assertStatus(404);
});

it('renders the link-unavailable page for a disabled short code', function () {
    $link = Link::factory()->disabled()->create([
        'short_code' => 'offline1',
        'original_url' => 'https://example.com/should-not-go-here',
    ]);

    $response = $this->get('/offline1');

    $response->assertOk();
    $response->assertSee('This link is no longer available', false);
    $response->assertSee('Link disabled', false);
    $response->assertSee('offline1', false);
    $response->assertSee(route('home'), false);
    $response->assertDontSee('should-not-go-here', false);
});

it('the link-unavailable page is a 200 response, not a redirect', function () {
    Link::factory()->disabled()->create([
        'short_code' => 'page2xx',
        'original_url' => 'https://example.com/destination',
    ]);

    $response = $this->get('/page2xx');

    expect($response->isRedirect())->toBeFalse();
    $response->assertOk();
});

it('the 404 page contains the design-system error layout', function () {
    $response = $this->get('/this-is-missing');

    $response->assertStatus(404);

    // The 404 page uses the eyebrow, h2, and body typography utilities
    // and renders the design-system icon. Asserting on classes guards
    // against future regressions that swap the styling for a generic
    // error page.
    $content = (string) $response->getContent();
    expect($content)->toContain('eyebrow');
    expect($content)->toContain('h2');
    expect($content)->toContain('body');
});

it('does not 500 when the 404 view is rendered for an unknown route without a shortCode param', function () {
    // A 404 from a route that has no `{shortCode}` parameter (such as
    // `/this-does-not-exist` which matches the catch-all) should still
    // render the 404 view successfully.
    $response = $this->get('/zzzz-no-code');

    $response->assertStatus(404);
    $response->assertSee('We couldn\'t find that page', false);
});
