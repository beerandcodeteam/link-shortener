<?php

use App\Models\Link;
use App\Models\LinkStatus;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    // RefreshDatabase does not run seeders; insert lookup data manually.
    LinkStatus::updateOrInsert(['slug' => 'active'], [
        'name' => 'Active',
        'is_active' => true,
    ]);

    LinkStatus::updateOrInsert(['slug' => 'disabled'], [
        'name' => 'Disabled',
        'is_active' => false,
    ]);
});

it('allows owner to delete their own link', function () {
    $owner = User::factory()->create();

    $link = Link::factory()->create([
        'user_id' => $owner->id,
    ]);

    Livewire::test(\App\Livewire\Dashboard\LinkList::class)
        ->call('confirmDelete', $link)
        ->assertSet('showDeleteModal', true)
        ->call('executeDelete')
        ->assertHasNoErrors();

    expect(Link::where('id', $link->id))->toHaveCount(0);
});

it('removes deleted link from the dashboard list', function () {
    $owner = User::factory()->create();

    $link = Link::factory()->create([
        'user_id' => $owner->id,
    ]);

    Livewire::test(\App\Livewire\Dashboard\LinkList::class)
        ->call('executeDelete')
        ->assertHasNoErrors();

    expect(Link::where('user_id', $owner->id))->toHaveCount(0);
});

it('makes deleted short code return 404', function () {
    LinkStatus::insert([
        'slug' => 'active',
        'name' => 'Active',
        'is_active' => true,
    ]);

    $link = Link::factory()->create();

    visit($link->short_code)->assertStatus(404);
});

it('denies non-owner delete attempt', function () {
    $owner = User::factory()->create();
    $impersonator = User::factory()->create();

    $targetLink = Link::factory()->create([
        'user_id' => $owner->id,
    ]);

    Livewire::actingAs($impersonator)
        ->test(\App\Livewire\Dashboard\LinkList::class)
        ->call('confirmDelete', $targetLink)
        ->assertStatus(403);
});

it('does not delete without confirmation modal', function () {
    $owner = User::factory()->create();

    $link = Link::factory()->create([
        'user_id' => $owner->id,
    ]);

    Livewire::test(\App\Livewire\Dashboard\LinkList::class)
        ->call('confirmDelete', $link);

    // The modal shows and deletion is NOT executed yet.
    expect(Link::where('id', $link->id))->toHaveCount(1);

    // Click Cancel on the modal.
    Livewire::test(\App\Livewire\Dashboard\LinkList::class)
        ->call('cancelDelete');

    // Link still exists after cancel.
    expect(Link::where('id', $link->id))->toHaveCount(1);
});
