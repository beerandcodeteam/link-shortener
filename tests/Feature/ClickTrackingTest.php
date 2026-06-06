<?php

use App\Models\Click;
use App\Models\Link;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('increments the counter and records exactly one click on a successful redirect', function () {
    $link = Link::factory()->active()->create([
        'short_code' => 'click01',
        'click_count' => 0,
    ]);

    $this->get('/'.$link->short_code)->assertStatus(302);

    expect($link->fresh()->click_count)->toBe(1);
    expect(Click::count())->toBe(1);
    expect(Click::first()->link_id)->toBe($link->id);
});

it('stores a hashed ip rather than the raw address', function () {
    $link = Link::factory()->active()->create(['short_code' => 'click02']);

    $this->get('/'.$link->short_code, ['REMOTE_ADDR' => '203.0.113.7']);

    $click = Click::firstOrFail();

    expect($click->ip_hash)->not->toBeNull();
    expect($click->ip_hash)->not->toBe('203.0.113.7');
    expect($click->ip_hash)->toBe(hash('sha256', '203.0.113.7'));
});

it('persists referrer, device type and browser parsed from the request', function () {
    $link = Link::factory()->active()->create(['short_code' => 'click03']);

    $userAgent = 'Mozilla/5.0 (iPhone; CPU iPhone OS 17_0 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.0 Mobile/15E148 Safari/604.1';

    $this->withHeaders([
        'User-Agent' => $userAgent,
        'Referer' => 'https://news.ycombinator.com/',
    ])->get('/'.$link->short_code);

    $click = Click::with(['deviceType', 'browser'])->firstOrFail();

    expect($click->referrer)->toBe('https://news.ycombinator.com/');
    expect($click->deviceType->slug)->toBe('mobile');
    expect($click->browser->slug)->toBe('safari');
});

it('records no click and does not increment for a disabled code', function () {
    $link = Link::factory()->disabled()->create([
        'short_code' => 'click04',
        'click_count' => 0,
    ]);

    $this->get('/'.$link->short_code)->assertStatus(410);

    expect($link->fresh()->click_count)->toBe(0);
    expect(Click::count())->toBe(0);
});

it('records no click and does not increment for a missing code', function () {
    $this->get('/missing9')->assertStatus(404);

    expect(Click::count())->toBe(0);
});
