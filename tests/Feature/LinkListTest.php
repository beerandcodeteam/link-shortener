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

it('renders only the authenticated user links', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $mine = Link::factory()->forUser($user)->create(['short_code' => 'mine123']);
    $theirs = Link::factory()->forUser($other)->create(['short_code' => 'their99']);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee($mine->short_code)
        ->assertDontSee($theirs->short_code);
});

it('shows click count, status badge and creation date', function () {
    $user = User::factory()->create();

    $link = Link::factory()->forUser($user)->disabled()->create([
        'short_code' => 'stat123',
        'click_count' => 42,
    ]);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('42 clicks')
        ->assertSee('Disabled')
        ->assertSee($link->created_at->format('M j, Y'));
});

it('paginates beyond the page size', function () {
    $user = User::factory()->create();
    Link::factory()->count(12)->forUser($user)->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertViewHas('links', fn ($links) => $links->count() === 10 && $links->total() === 12);
});

it('shows an empty state when the user has no links', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('No links yet');
});
