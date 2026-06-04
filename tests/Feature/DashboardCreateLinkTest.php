<?php

use App\Livewire\Dashboard;
use App\Models\Link;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('creates a link owned by the authenticated user and it appears in the list', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('original_url', 'https://example.com/dashboard-create')
        ->call('createLink')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $link = Link::query()->where('user_id', $user->id)->first();

    expect($link)->not->toBeNull();
    expect($link->original_url)->toBe('https://example.com/dashboard-create');
    expect($link->linkStatus->slug)->toBe('active');
    expect($link->click_count)->toBe(0);
});

it('auto-generates a short code when the custom code is omitted', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('original_url', 'https://example.com/auto')
        ->set('custom_code', '')
        ->call('createLink')
        ->assertHasNoErrors();

    $link = Link::query()->where('user_id', $user->id)->first();

    expect($link)->not->toBeNull();
    expect($link->short_code)->not->toBe('');
    expect($link->short_code)->toMatch('/^[abcdefghijkmnpqrstuvwxyz23456789]+$/');
});

it('honors a valid custom code when supplied', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('original_url', 'https://example.com/custom')
        ->set('custom_code', 'mypick12')
        ->call('createLink')
        ->assertHasNoErrors();

    $link = Link::query()->where('user_id', $user->id)->first();

    expect($link)->not->toBeNull();
    expect($link->short_code)->toBe('mypick12');
});

it('surfaces a validation error for a duplicate custom code and does not create a link', function () {
    $user = User::factory()->create();
    Link::factory()->forUser($user)->create(['short_code' => 'already-taken']);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('original_url', 'https://example.com/valid')
        ->set('custom_code', 'already-taken')
        ->call('createLink')
        ->assertHasErrors(['custom_code'])
        ->assertNoRedirect();

    expect(Link::query()->where('user_id', $user->id)->count())->toBe(1);
});

it('surfaces a validation error for a reserved word custom code and does not create a link', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('original_url', 'https://example.com/valid')
        ->set('custom_code', 'login')
        ->call('createLink')
        ->assertHasErrors(['custom_code'])
        ->assertNoRedirect();

    expect(Link::query()->where('user_id', $user->id)->count())->toBe(0);
});

it('surfaces a validation error for an invalid URL and does not create a link', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('original_url', 'not-a-url')
        ->call('createLink')
        ->assertHasErrors(['original_url'])
        ->assertNoRedirect();

    expect(Link::query()->where('user_id', $user->id)->count())->toBe(0);
});

it('flashes the new link id so the just-created card appears after the redirect', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('original_url', 'https://example.com/visible')
        ->set('custom_code', 'visi1234')
        ->call('createLink')
        ->assertHasNoErrors();

    $flashId = session('shortened_link_id');

    expect($flashId)->not->toBeNull();

    $link = Link::query()->whereKey($flashId)->first();

    expect($link)->not->toBeNull();
    expect($link->user_id)->toBe($user->id);
    expect($link->short_code)->toBe('visi1234');
});

it('persists the new link with an active status by default', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('original_url', 'https://example.com/active')
        ->call('createLink')
        ->assertHasNoErrors();

    $link = Link::query()->where('user_id', $user->id)->first();

    expect($link->linkStatus->slug)->toBe('active');
});
