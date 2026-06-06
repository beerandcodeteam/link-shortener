<?php

use App\Livewire\Dashboard;
use App\Models\Link;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('lets the owner delete their link, removing it from the database and list', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->create(['short_code' => 'delete1']);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee($link->short_code)
        ->call('confirmDelete', $link)
        ->assertSet('deletingId', $link->id)
        ->call('delete')
        ->assertSet('deletingId', null)
        ->assertDontSee($link->short_code);

    expect(Link::find($link->id))->toBeNull();
});

it('makes the deleted short code 404', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->active()->create(['short_code' => 'gone123']);

    $this->get('/'.$link->short_code)->assertRedirect($link->original_url);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('confirmDelete', $link)
        ->call('delete');

    $this->get('/'.$link->short_code)->assertNotFound();
});

it('denies a non-owner from deleting a link', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $link = Link::factory()->forUser($owner)->create();

    Livewire::actingAs($other)
        ->test(Dashboard::class)
        ->call('confirmDelete', $link)
        ->assertStatus(403);

    expect(Link::find($link->id))->not->toBeNull();
});

it('does not delete without a confirmation step', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('delete');

    expect(Link::find($link->id))->not->toBeNull();
});
