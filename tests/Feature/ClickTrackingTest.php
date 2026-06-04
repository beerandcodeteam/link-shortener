<?php

use App\Models\Click;
use App\Models\Link;
use Database\Seeders\LookupSeeder;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('records exactly one click row and increments click_count on a successful redirect', function () {
    $link = Link::factory()->active()->create([
        'short_code' => 'clickme',
        'original_url' => 'https://example.com/landing',
        'click_count' => 0,
    ]);

    $this->get('/clickme')->assertStatus(302);

    $link->refresh();

    expect($link->click_count)->toBe(1);
    expect($link->clicks()->count())->toBe(1);

    $click = $link->clicks()->first();
    expect($click->link_id)->toBe($link->id);
    expect($click->clicked_at)->not->toBeNull();
});

it('stores ip_hash as a hash, not the raw IP', function () {
    $link = Link::factory()->active()->create([
        'short_code' => 'ipcheck',
    ]);

    $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.42'])
        ->withHeaders(['User-Agent' => 'Mozilla/5.0 (Macintosh) AppleWebKit/605 Version/16 Safari/605'])
        ->get('/ipcheck')
        ->assertStatus(302);

    $click = $link->clicks()->first();

    expect($click)->not->toBeNull();
    expect($click->ip_hash)->toBeString()->not->toBeEmpty();
    expect($click->ip_hash)->not->toBe('203.0.113.42');
    expect(Hash::check('203.0.113.42', $click->ip_hash))->toBeTrue();
});

it('persists referrer, device, and browser parsed from the request', function () {
    $link = Link::factory()->active()->create([
        'short_code' => 'attrchk',
    ]);

    $this->withHeaders([
        'User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1',
        'Referer' => 'https://news.example.com/article',
    ])->get('/attrchk')->assertStatus(302);

    $click = $link->clicks()->first();

    expect($click)->not->toBeNull();
    expect($click->referrer)->toBe('https://news.example.com/article');

    expect($click->deviceType)->not->toBeNull();
    expect($click->deviceType->slug)->toBe('mobile');

    expect($click->browser)->not->toBeNull();
    expect($click->browser->slug)->toBe('safari');
});

it('does not record a click and does not increment for a disabled short code', function () {
    $link = Link::factory()->disabled()->create([
        'short_code' => 'disabledx',
        'click_count' => 0,
    ]);

    $this->get('/disabledx')->assertOk();

    $link->refresh();
    expect($link->click_count)->toBe(0);
    expect(Click::query()->where('link_id', $link->id)->count())->toBe(0);
});

it('does not record a click and does not increment for an unknown short code', function () {
    $this->get('/never-made')->assertStatus(404);

    expect(Click::query()->count())->toBe(0);
    expect(Link::query()->sum('click_count'))->toBe(0);
});

it('persists every click as a new row when the same link is visited multiple times', function () {
    $link = Link::factory()->active()->create([
        'short_code' => 'repeats',
        'click_count' => 0,
    ]);

    for ($i = 0; $i < 3; $i++) {
        $this->get('/repeats')->assertStatus(302);
    }

    $link->refresh();
    expect($link->click_count)->toBe(3);
    expect($link->clicks()->count())->toBe(3);
});

it('falls back to "other" browser and "unknown" device for empty user-agents', function () {
    $link = Link::factory()->active()->create([
        'short_code' => 'emptyua',
    ]);

    $this->withHeaders(['User-Agent' => ''])->get('/emptyua')->assertStatus(302);

    $click = $link->clicks()->first();

    expect($click)->not->toBeNull();
    expect($click->deviceType)->not->toBeNull();
    expect($click->deviceType->slug)->toBe('unknown');
    expect($click->browser)->not->toBeNull();
    expect($click->browser->slug)->toBe('other');
});
