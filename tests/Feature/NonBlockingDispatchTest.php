<?php

use App\Jobs\RecordClick as RecordClickJob;
use App\Models\Click;
use App\Models\Link;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed lookup tables so parsers don't hit blank rows during tests.
    app(LookupSeeder::class)->run();
});

// ------------------------------------------------------------------
// Core contract: redirect response is returned immediately, click
// recording runs after the response has been sent (via deferred).
// ------------------------------------------------------------------

test('redirect response is returned while click records after', function (): void {
    $link = Link::factory()->create([
        'original_url' => 'https://example.com/target',
        'click_count'  => 0,
    ]);

    // Before the request there must be zero clicks.
    expect($link->clicks()->count())->toBe(0);

    $response = $this->get($link->short_code);

    // Redirect response is returned immediately (302 to original).
    $response->assertStatus(302)
        ->assertRedirect($link->original_url);

    // Deferred job has executed by this point in the test;
    // click_count was incremented and a clicks row exists.
    $link->refresh();
    expect($link->click_count)->toBe(1)
        ->and($link->clicks()->count())->toBe(1);
});

test('redirect response is still returned before deferred jobs execute', function (): void {
    $link = Link::factory()->create([
        'original_url' => 'https://example.com/after-response',
        'click_count'  => 0,
    ]);

    // No click recorded yet.
    expect(Click::count())->toBe(0);

    $this->get($link->short_code)->assertStatus(302);

    // At this point the request lifecycle is over and deferred
    // jobs (using the 'deferred' queue connection) have run.
    $link->refresh();
    expect($link->click_count)->toBe(1)
        ->and($link->clicks()->count())->toBe(1);
});

test('active link click is recorded even though dispatch does not block redirect', function (): void {
    $link = Link::factory()->create([
        'original_url' => 'https://example.com/nonblocking',
        'click_count'  => 42,
    ]);

    $chromeUA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36';

    $this->get($link->short_code, [
        'HTTP_USER_AGENT' => $chromeUA,
        'HTTP_REFERER'    => 'https://twitter.com/share',
        'HTTP_X_FORWARDED_FOR' => '198.51.100.7',
    ])->assertStatus(302);

    $link->refresh();

    expect($link->click_count)
        ->toBe(43);

    $click = $link->clicks()->first();
    expect($click)->not->toBeNull()
        ->and($click->referrer)->toBe('https://twitter.com/share')
        ->and($click->deviceType->slug)->toBe('desktop')
        ->and($click->browser->slug)->toBe('chrome')
        ->and(strlen($click->ip_hash))->toBe(64)
        ->and($click->ip_hash)->not->toBe('198.51.100.7');
});

// ----- Non-active links must not record clicks (same as before) -----

test('disabled link returns 200 without blocking and records no click', function (): void {
    $link = Link::factory()->disabled()->create([
        'original_url' => 'https://example.com/disabled-nd',
        'click_count'  => 5,
    ]);

    $this->get($link->short_code)->assertStatus(200);

    $link->refresh();
    expect($link->click_count)->toBe(5)
        ->and($link->clicks()->count())->toBe(0);
});

test('unknown short code returns 404 without blocking or creating a click', function (): void {
    $clicksBefore = Click::count();

    $this->get('/nonexistent-code-xyz')->assertStatus(404);

    expect(Click::count())->toBe($clicksBefore);
});
