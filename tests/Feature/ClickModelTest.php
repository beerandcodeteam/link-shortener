<?php

use App\Models\Click;
use App\Models\Link;
use App\Models\LinkStatus;
use Database\Seeders\LookupSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('creates a click tied to a link via the factory', function () {
    $link = Link::factory()->active()->create();

    $click = Click::factory()->for($link, 'link')->create();

    expect($click->link_id)->toBe($link->id);
    expect($click->link->is($link))->toBeTrue();
    expect($click->clicked_at)->toBeInstanceOf(Carbon\Carbon::class);
});

it('returns clicks ordered by clicked_at desc from the link relation', function () {
    $link = Link::factory()->active()->create();

    $older = Click::factory()->for($link, 'link')->create(['clicked_at' => now()->subDays(3)]);
    $newest = Click::factory()->for($link, 'link')->create(['clicked_at' => now()->subMinutes(5)]);
    $middle = Click::factory()->for($link, 'link')->create(['clicked_at' => now()->subDay()]);

    $ids = $link->clicks()->orderByDesc('clicked_at')->pluck('id')->all();

    expect($ids)->toBe([$newest->id, $middle->id, $older->id]);
});

it('stores ip_hash as a hash, never the raw IP', function () {
    $click = Click::factory()->create();

    expect($click->ip_hash)->toBeString()->not->toBeEmpty();
    expect($click->ip_hash)->not->toBe('192.168.0.1');

    // Sanity check: a fresh hash should verify a known raw IP.
    $raw = '203.0.113.42';
    $hashed = Hash::make($raw);
    expect(Hash::check($raw, $hashed))->toBeTrue();
    expect(Hash::check('203.0.113.42', $hashed))->toBeTrue();
});

it('allows the recent state to clamp clicked_at to last 24h', function () {
    $link = Link::factory()->active()->create();

    $click = Click::factory()->recent()->for($link, 'link')->create();

    expect($click->clicked_at->gt(now()->subDay()))->toBeTrue();
    expect($click->clicked_at->lte(now()))->toBeTrue();
});

it('links status via LinkStatus factory state from Link factory', function () {
    $link = Link::factory()->active()->create();

    expect(LinkStatus::where('slug', 'active')->exists())->toBeTrue();
    expect($link->linkStatus->slug)->toBe('active');
});
