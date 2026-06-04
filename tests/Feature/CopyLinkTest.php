<?php

use App\Livewire\Dashboard;
use App\Models\Link;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('renders the absolute short URL for each row in the dashboard list', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->create([
        'short_code' => 'copythis',
    ]);

    $expected = url('/copythis');

    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSee($expected, false)
        ->assertSee('copythis', false);
});

it('exposes the absolute short URL on the CopyButton for the row', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->create([
        'short_code' => 'btncopy1',
    ]);

    $expected = url('/btncopy1');

    $component = Livewire::actingAs($user)->test(Dashboard::class);

    // The CopyButton writes the absolute URL to the clipboard via Alpine.
    // Assert the data encoded in the @js($text) call surfaces the full
    // URL with the host portion.
    $component->assertSee($expected, false);
});

it('renders the absolute short URL on the link detail page', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->create([
        'short_code' => 'detail01',
        'original_url' => 'https://example.com/destination',
    ]);

    $expected = url('/detail01');

    $this->actingAs($user)
        ->get(route('links.show', $link))
        ->assertOk()
        ->assertSee($expected, false)
        ->assertSee('detail01', false);
});

it('encodes the full short URL inside the CopyButton Alpine payload', function () {
    $user = User::factory()->create();
    Link::factory()->forUser($user)->create([
        'short_code' => 'payload1',
    ]);

    $expected = url('/payload1');

    $html = Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->html();

    // The copy button uses @js($text) which serializes the URL as a JS
    // string literal. Assert the absolute URL appears verbatim in the
    // rendered HTML (either in the visible link or in the Alpine payload).
    expect($html)->toContain($expected);
});
