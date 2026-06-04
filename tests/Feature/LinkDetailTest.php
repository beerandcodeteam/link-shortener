<?php

use App\Models\Browser;
use App\Models\Click;
use App\Models\DeviceType;
use App\Models\Link;
use App\Models\LinkStatus;
use App\Models\User;
use Database\Seeders\LookupSeeder;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('renders the detail page for the owner', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->active()->create([
        'short_code' => 'myown',
        'original_url' => 'https://example.com/destination',
        'click_count' => 12,
    ]);

    $this->actingAs($user)
        ->get(route('links.show', $link))
        ->assertOk()
        ->assertSee('myown', false)
        ->assertSee('https://example.com/destination', false)
        ->assertSee('12', false);
});

it('shows the click log entries tied to the link', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->active()->create();

    $chrome = Browser::firstOrCreate(['slug' => 'chrome'], ['name' => 'Chrome']);
    $desktop = DeviceType::firstOrCreate(['slug' => 'desktop'], ['name' => 'Desktop']);

    Click::factory()->for($link, 'link')->create([
        'referrer' => 'https://news.example.com/article',
        'browser_id' => $chrome->id,
        'device_type_id' => $desktop->id,
        'clicked_at' => now()->subHour(),
    ]);
    Click::factory()->for($link, 'link')->create([
        'referrer' => 'https://blog.example.com/post',
        'browser_id' => $chrome->id,
        'device_type_id' => $desktop->id,
        'clicked_at' => now()->subMinutes(5),
    ]);

    $this->actingAs($user)
        ->get(route('links.show', $link))
        ->assertOk()
        ->assertSee('news.example.com', false)
        ->assertSee('blog.example.com', false)
        ->assertSee('Chrome', false)
        ->assertSee('Desktop', false);
});

it('displays the total clicks equal to click_count', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->active()->create([
        'click_count' => 7,
    ]);

    // Generate 7 click rows so the totals match.
    Click::factory()->for($link, 'link')->count(7)->create();

    $this->actingAs($user)
        ->get(route('links.show', $link))
        ->assertOk()
        ->assertSee('detail-total-clicks', false)
        ->assertSee('7', false);
});

it('only lists clicks belonging to this link', function () {
    $user = User::factory()->create();
    $myLink = Link::factory()->forUser($user)->active()->create();
    $otherLink = Link::factory()->active()->create();

    Click::factory()->for($myLink, 'link')->count(3)->create();
    Click::factory()->for($otherLink, 'link')->count(5)->create();

    // Render the page and verify only 3 click-log rows are emitted.
    $page = $this->actingAs($user)->get(route('links.show', $myLink));

    $rowCount = substr_count((string) $page->getContent(), 'data-testid="click-log-row"');

    expect($rowCount)->toBe(3);
});

it('denies a non-owner from viewing the link detail (403)', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $link = Link::factory()->forUser($owner)->active()->create();

    $this->actingAs($stranger)
        ->get(route('links.show', $link))
        ->assertForbidden();
});

it('redirects a guest to login', function () {
    $owner = User::factory()->create();
    $link = Link::factory()->forUser($owner)->active()->create();

    $this->get(route('links.show', $link))->assertRedirect(route('login'));
});

it('renders the 14-day chart with at least one bar', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->active()->create();

    Click::factory()->for($link, 'link')->create([
        'clicked_at' => now()->subHours(2),
    ]);

    $this->actingAs($user)
        ->get(route('links.show', $link))
        ->assertOk()
        ->assertSee('link-chart', false)
        ->assertSee('chart-bar', false);
});

it('shows status badge reflecting the link status', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->active()->create();

    $this->actingAs($user)
        ->get(route('links.show', $link))
        ->assertOk()
        ->assertSee('Active', false);

    $link->forceFill([
        'link_status_id' => LinkStatus::where('slug', 'disabled')->value('id'),
    ])->save();

    $this->actingAs($user)
        ->get(route('links.show', $link))
        ->assertOk()
        ->assertSee('Disabled', false);
});

it('shows an empty-state message when there are no clicks', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->active()->create();

    $this->actingAs($user)
        ->get(route('links.show', $link))
        ->assertOk()
        ->assertSee('No clicks yet', false)
        ->assertSee('click-log-empty', false);
});
