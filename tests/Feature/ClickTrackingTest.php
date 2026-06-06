<?php

use App\Models\Click;
use App\Models\Link;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Seed lookup tables (link_statuses, browsers, device_types)
    app(LookupSeeder::class)->run();
});

// Successful redirect: count + log

test('successful redirect increments click_count by 1', function () {
    $link = Link::factory()->create([
        'original_url' => 'https://example.com/target',
        'click_count' => 5,
    ]);

    $this->get($link->short_code)->assertStatus(302);

    $link->refresh();
    expect($link->click_count)->toBe(6);
});

test('successful redirect creates exactly one clicks row with the originating link', function () {
    $link = Link::factory()->create([
        'original_url' => 'https://example.com/target',
    ]);

    $this->get($link->short_code)->assertStatus(302);

    expect($link->clicks()->count())->toBe(1);
    expect($link->clicks()->first()->link_id)->toBe($link->id);
});

test('ip_hash stores a hash and does not equal the raw IP', function () {
    $link = Link::factory()->create([
        'original_url' => 'https://example.com/target',
    ]);

    $this->get($link->short_code, ['HTTP_X_FORWARDED_FOR' => '203.0.113.42'])
        ->assertStatus(302);

    $click = $link->clicks()->first();
    expect($click?->ip_hash)->not->toBeNull()
        ->and($click?->ip_hash)->not->toBe('203.0.113.42');

    // Verify it is a SHA-256 hash (64 hex chars)
    expect(strlen($click->ip_hash))->toBe(64);
    expect(ctype_xdigit($click->ip_hash))->toBeTrue();
});

test('referrer is persisted from the request into the clicks table', function () {
    $link = Link::factory()->create([
        'original_url' => 'https://example.com/target',
    ]);

    $this->get($link->short_code, ['HTTP_REFERER' => 'https://whatsapp.com/share'])
        ->assertStatus(302);

    expect($link->clicks()->first()?->referrer)->toBe('https://whatsapp.com/share');
});

test('device_type is resolved and persisted from user-agent (desktop Chrome)', function () {
    $link = Link::factory()->create([
        'original_url' => 'https://example.com/target',
    ]);

    $chromeUA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36';
    $this->get($link->short_code, ['HTTP_USER_AGENT' => $chromeUA])->assertStatus(302);

    expect($link->clicks()->first()?->deviceType?->slug)->toBe('desktop');
});

test('device_type is resolved and persisted from user-agent (mobile Safari)', function () {
    $link = Link::factory()->create([
        'original_url' => 'https://example.com/target',
    ]);

    $iphoneUA = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1';
    $this->get($link->short_code, ['HTTP_USER_AGENT' => $iphoneUA])->assertStatus(302);

    expect($link->clicks()->first()?->deviceType?->slug)->toBe('mobile');
});

test('browser is resolved and persisted from user-agent', function () {
    $link = Link::factory()->create([
        'original_url' => 'https://example.com/target',
    ]);

    // Chrome UA
    $chromeUA = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/125.0.0.0 Safari/537.36';
    $this->get($link->short_code, ['HTTP_USER_AGENT' => $chromeUA])->assertStatus(302);

    expect($link->clicks()->first()?->browser?->slug)->toBe('chrome');
});

test('bot user agent is detected as bot device type', function () {
    $link = Link::factory()->create([
        'original_url' => 'https://example.com/target',
    ]);

    $botUA = 'Googlebot-Image/1.0';
    $this->get($link->short_code, ['HTTP_USER_AGENT' => $botUA])->assertStatus(302);

    expect($link->clicks()->first()?->deviceType?->slug)->toBe('bot');
});

// Disabled link: no counting / no log

test('disabled link does not increment click_count', function () {
    $link = Link::factory()->disabled()->create([
        'original_url' => 'https://example.com/target',
        'click_count' => 5,
    ]);

    $this->get($link->short_code)->assertStatus(200);

    $link->refresh();
    expect($link->click_count)->toBe(5);
});

test('disabled link creates no clicks row', function () {
    $link = Link::factory()->disabled()->create([
        'original_url' => 'https://example.com/target',
    ]);

    $this->get($link->short_code)->assertStatus(200);

    expect($link->clicks()->count())->toBe(0);
});

// Missing link (404): no counting / no log

test('unknown short code does not increment any counter', function () {
    static::withoutMiddleware(); // skip auth middleware for the homepage if needed

    $clicksBefore = Click::count();

    $this->get('/unknown-short-code-xyz123')->assertStatus(404);

    expect(Click::count())->toBe($clicksBefore);
});
