<?php

use App\Models\Link;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('redirects an active short code with a 302 to the original url', function () {
    $link = Link::factory()->active()->create([
        'original_url' => 'https://example.com/destination',
        'short_code' => 'abc1234',
    ]);

    $response = $this->get('/'.$link->short_code);

    $response->assertStatus(302);
    $response->assertRedirect('https://example.com/destination');
});

it('returns 404 for an unknown short code', function () {
    $this->get('/nope999')->assertStatus(404);
});

it('shows the unavailable page (not a redirect) for a disabled short code', function () {
    $link = Link::factory()->disabled()->create([
        'original_url' => 'https://example.com/secret',
        'short_code' => 'disabl3',
    ]);

    $response = $this->get('/'.$link->short_code);

    $response->assertStatus(410);
    $response->assertDontSee('https://example.com/secret');
    $response->assertSee('This link is unavailable', false);
    expect($response->isRedirect())->toBeFalse();
});

it('does not capture application routes with the catch-all', function () {
    foreach (['/login', '/dashboard'] as $path) {
        $response = $this->get($path);

        expect($response->isRedirect())->toBeFalse();
        expect($response->getStatusCode())->not->toBe(302);
    }
});
