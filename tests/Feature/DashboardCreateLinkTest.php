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

it('creates a link owned by the user and surfaces it', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('form.original_url', 'https://example.com/page')
        ->set('form.custom_code', '')
        ->call('create')
        ->assertHasNoErrors()
        ->assertSet('showCreate', false);

    $link = Link::where('user_id', $user->id)->first();

    expect($link)->not->toBeNull();
    expect($link->original_url)->toBe('https://example.com/page');
    expect($link->short_code)->not->toBeEmpty();
});

it('auto-generates a short code when no custom code is supplied', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('form.original_url', 'https://example.com/auto')
        ->set('form.custom_code', '')
        ->call('create')
        ->assertHasNoErrors();

    $link = Link::where('user_id', $user->id)->firstOrFail();

    expect($link->short_code)->toMatch('/^[A-Za-z0-9_-]+$/');
});

it('honors a valid custom short code', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('form.original_url', 'https://example.com/custom')
        ->set('form.custom_code', 'my-custom-code')
        ->call('create')
        ->assertHasNoErrors();

    expect(Link::where('short_code', 'my-custom-code')->where('user_id', $user->id)->exists())->toBeTrue();
});

it('rejects a duplicate custom code without creating', function () {
    $user = User::factory()->create();
    Link::factory()->create(['short_code' => 'taken00']);

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('form.original_url', 'https://example.com/dup')
        ->set('form.custom_code', 'taken00')
        ->call('create')
        ->assertHasErrors('form.custom_code');

    expect(Link::where('user_id', $user->id)->count())->toBe(0);
});

it('rejects a reserved word code without creating', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('form.original_url', 'https://example.com/reserved')
        ->set('form.custom_code', 'dashboard')
        ->call('create')
        ->assertHasErrors('form.custom_code');

    expect(Link::where('user_id', $user->id)->count())->toBe(0);
});

it('rejects an invalid url without creating', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->set('form.original_url', 'not-a-url')
        ->call('create')
        ->assertHasErrors('form.original_url');

    expect(Link::where('user_id', $user->id)->count())->toBe(0);
});
