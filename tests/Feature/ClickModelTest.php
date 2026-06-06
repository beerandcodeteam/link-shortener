<?php

use App\Models\Click;
use App\Models\Link;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a click tied to a link via factory', function () {
    $click = Click::factory()->create();

    expect($click->link)->toBeInstanceOf(Link::class);
    expect($click->clicked_at)->not->toBeNull();
});

it('returns a links clicks ordered by clicked_at', function () {
    $link = Link::factory()->create();

    $older = Click::factory()->for($link)->onDate(now()->subDays(3))->create();
    $newer = Click::factory()->for($link)->onDate(now()->subDay())->create();

    $ordered = $link->clicks()->orderBy('clicked_at')->get();

    expect($ordered->pluck('id')->all())->toBe([$older->id, $newer->id]);
});

it('stores a hashed ip and never the raw address', function () {
    $click = Click::factory()->create();

    expect($click->ip_hash)->toHaveLength(64);
    expect($click->ip_hash)->toMatch('/^[0-9a-f]{64}$/');
    expect($click->ip_hash)->not->toContain('.');
});
