<?php

use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(): void {
    // Seed lookup tables (link_statuses, device_types, browsers)
    \Database\Seeders\LookupSeeder::call();
}

test('active link returns 302 redirect to original url', function (): void {
    $link = Link::factory()->create(['original_url' => 'https://example.com/target']);

    $response = get($link->short_code);

    $response->assertStatus(302);
    $response->assertRedirect('https://example.com/target');
});

test('active link redirects with the original url exactly', function (): void {
    $originalUrl = 'https://example.com/path?query=1&other=value';
    $link = Link::factory()->create(['original_url' => $originalUrl]);

    get($link->short_code)->assertRedirect($originalUrl);
});

test('unknown short code returns 404', function (): void {
    get('/nonexistent-code')->assertStatus(404);
});

test('disabled link returns the unavailable page (not a redirect)', function (): void {
    $link = Link::factory()->disabled()->create(['original_url' => 'https://example.com/target']);

    get($link->short_code)
        ->assertStatus(200)
        ->assertViewIs('pages.link-unavailable')
        ->assertDontRedirect('https://example.com/target');
});

test('disabled link does not return a 302', function (): void {
    $link = Link::factory()->disabled()->create(['original_url' => 'https://example.com/target']);

    get($link->short_code)->assertStatus(200);
});

test('reserved/app routes are not captured by redirect catch-all', function (): void {
    // /gallery should hit its gallery route, not be treated as a short code lookup that fails
    get('/gallery')->assertStatus(200)->assertInView('pages.gallery');
    get('/login')->assertStatus(302)->assertRedirect('/login');
});
