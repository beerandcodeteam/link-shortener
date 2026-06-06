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

it('lets the owner toggle a link active to disabled and back', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->active()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('toggleStatus', $link);

    expect($link->fresh()->isActive)->toBeFalse();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('toggleStatus', $link->fresh());

    expect($link->fresh()->isActive)->toBeTrue();
});

it('makes a disabled link stop redirecting', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->active()->create(['short_code' => 'toggle1']);

    $this->get('/'.$link->short_code)->assertRedirect($link->original_url);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->call('toggleStatus', $link);

    $this->get('/'.$link->short_code)
        ->assertStatus(410)
        ->assertHeaderMissing('Location');
});

it('denies a non-owner from toggling status', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $link = Link::factory()->forUser($owner)->active()->create();

    Livewire::actingAs($other)
        ->test(Dashboard::class)
        ->call('toggleStatus', $link)
        ->assertStatus(403);

    expect($link->fresh()->isActive)->toBeTrue();
});

it('reflects the status change in the component', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->active()->create(['short_code' => 'reflect1']);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('Active')
        ->call('toggleStatus', $link)
        ->assertSee('Disabled');
});
