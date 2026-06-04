<?php

use App\Livewire\Dashboard;
use App\Models\Link;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('removes the link from the database when the owner confirms the delete', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->create([
        'short_code' => 'gone1234',
    ]);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('confirmDelete', $link->id)
        ->assertSet('confirmingDelete', $link->id)
        ->call('deleteConfirmed')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    expect(Link::query()->whereKey($link->id)->exists())->toBeFalse();
});

it('removes the link from the rendered list after delete', function () {
    $user = User::factory()->create();
    $keep = Link::factory()->forUser($user)->create(['short_code' => 'staying']);
    $drop = Link::factory()->forUser($user)->create(['short_code' => 'leaving']);

    $component = Livewire::actingAs($user)->test(Dashboard::class);
    $component->assertSee('leaving', false);

    $component
        ->call('confirmDelete', $drop->id)
        ->call('deleteConfirmed');

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('staying', false)
        ->assertDontSee('leaving', false);
});

it('flashes a toast after a successful delete', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('confirmDelete', $link->id)
        ->call('deleteConfirmed');

    expect(session('toast'))->not->toBeNull();
});

it('returns 404 for the deleted short code (integrates Phase 4.1)', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->active()->create([
        'short_code' => 'vanished',
        'original_url' => 'https://example.com/nowhere',
    ]);

    // Sanity: short code works before delete.
    $this->get('/vanished')->assertStatus(302);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('confirmDelete', $link->id)
        ->call('deleteConfirmed');

    // After delete, the short code returns 404.
    $this->get('/vanished')->assertStatus(404);
});

it('denies a non-owner from deleting a link (403)', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $link = Link::factory()->forUser($owner)->create();

    $this->actingAs($stranger)
        ->delete(route('links.destroy', $link))
        ->assertForbidden();

    // Link still exists.
    expect(Link::query()->whereKey($link->id)->exists())->toBeTrue();
});

it('does not delete anything when cancelDelete is called after confirmDelete', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('confirmDelete', $link->id)
        ->assertSet('confirmingDelete', $link->id)
        ->call('cancelDelete')
        ->assertSet('confirmingDelete', null);

    expect(Link::query()->whereKey($link->id)->exists())->toBeTrue();
});

it('does not delete anything when deleteConfirmed is called without a prior confirm', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('deleteConfirmed');

    // No confirmingDelete id was set, so no link is removed.
    expect(Link::query()->whereKey($link->id)->exists())->toBeTrue();
});

it('redirects guests to login when attempting to delete', function () {
    $owner = User::factory()->create();
    $link = Link::factory()->forUser($owner)->create();

    $this->delete(route('links.destroy', $link))->assertRedirect(route('login'));
});
