<?php

use App\Livewire\Dashboard;
use App\Models\Link;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('renders only the authenticated user links on the dashboard', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $mine = Link::factory()->forUser($user)->count(2)->create();
    Link::factory()->forUser($other)->count(3)->create();

    $component = Livewire::actingAs($user)->test(Dashboard::class);

    foreach ($mine as $link) {
        $component->assertSee($link->short_code, false);
    }
});

it('excludes other user links from the rendered dashboard', function () {
    $user = User::factory()->create();
    $other = User::factory()->create();

    $others = Link::factory()->forUser($other)->count(2)->create();

    $component = Livewire::actingAs($user)->test(Dashboard::class);

    foreach ($others as $link) {
        $component->assertDontSee($link->short_code, false);
    }
});

it('shows click count, status and creation date on each row', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->active()->create([
        'click_count' => 42,
        'created_at' => now()->subDay(),
    ]);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee((string) $link->click_count, false)
        ->assertSee('Active', false)
        ->assertSee($link->created_at->format('M j, Y'), false);
});

it('renders the empty state when the user has no links', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee('No links yet', false)
        ->assertSee('dashboard-empty-state', false);
});

it('paginates the list beyond the page size', function () {
    $user = User::factory()->create();

    // The dashboard page size is 10. Create 12 to span two pages.
    Link::factory()->forUser($user)->count(12)->create();

    $component = Livewire::actingAs($user)->test(Dashboard::class);

    // Total reflects the underlying query regardless of the current page.
    $instance = $component->instance();
    $paginator = $instance->links;

    expect($paginator->total())->toBe(12);
    expect($paginator->perPage())->toBe(10);
    expect($paginator->count())->toBe(10);
});

it('redirects a guest visitor away from the dashboard to login', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('renders the dashboard for the authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get(route('dashboard'))->assertOk();
});
