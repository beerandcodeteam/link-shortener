<?php

use App\Livewire\Dashboard;
use App\Models\Link;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('lets the owner toggle an active link to disabled', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->active()->create();

    $this->actingAs($user)
        ->post(route('links.toggle', $link))
        ->assertRedirect();

    $link->refresh();

    expect($link->linkStatus->slug)->toBe('disabled');
    expect($link->is_active)->toBeFalse();
});

it('lets the owner toggle a disabled link back to active', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->disabled()->create();

    $this->actingAs($user)
        ->post(route('links.toggle', $link))
        ->assertRedirect();

    $link->refresh();

    expect($link->linkStatus->slug)->toBe('active');
    expect($link->is_active)->toBeTrue();
});

it('flashes a toast on the dashboard after the toggle', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->active()->create();

    $this->actingAs($user)->post(route('links.toggle', $link));

    expect(session('toast'))->not->toBeNull();
});

it('denies a non-owner from toggling a link (403)', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $link = Link::factory()->forUser($owner)->active()->create();

    $this->actingAs($stranger)
        ->post(route('links.toggle', $link))
        ->assertForbidden();

    // The link status is unchanged.
    $link->refresh();
    expect($link->linkStatus->slug)->toBe('active');
});

it('redirects guests to login', function () {
    $owner = User::factory()->create();
    $link = Link::factory()->forUser($owner)->active()->create();

    $this->post(route('links.toggle', $link))->assertRedirect(route('login'));
});

it('disabled link does not redirect after being toggled off (integrates Phase 4.1)', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->active()->create([
        'short_code' => 'toggled1',
        'original_url' => 'https://example.com/landing',
    ]);

    // Sanity check: active link still 302s to the original URL.
    $this->get('/toggled1')->assertStatus(302);

    // Toggle off.
    $this->actingAs($user)->post(route('links.toggle', $link))->assertRedirect();

    // Now the public route renders the unavailable page (200, no redirect).
    $response = $this->get('/toggled1');
    $response->assertOk();
    expect($response->isRedirect())->toBeFalse();
});

it('reflects the new status in the rendered dashboard after toggle', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->active()->create([
        'short_code' => 'reflect1',
    ]);

    $this->actingAs($user)->post(route('links.toggle', $link))->assertRedirect();

    // The Link was disabled, so the dashboard should now show "Disabled".
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Disabled', false);
});
