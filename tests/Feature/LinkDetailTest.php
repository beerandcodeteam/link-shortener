<?php

use App\Livewire\LinkDetail;
use App\Models\Click;
use App\Models\Link;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('shows the owner the detail with correct totals and click-log rows', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->create([
        'short_code' => 'detail9',
        'click_count' => 3,
    ]);

    Click::factory()->count(3)->for($link)->create([
        'referrer' => 'https://news.example.com',
    ]);

    Livewire::actingAs($user)
        ->test(LinkDetail::class, ['link' => $link])
        ->assertSee($link->short_code)
        ->assertSee($link->original_url)
        ->assertSee('3')
        ->assertSee('https://news.example.com')
        ->assertViewHas('clicks', fn ($clicks) => $clicks->count() === 3);
});

it('forbids a non-owner from viewing the detail', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $link = Link::factory()->forUser($owner)->create();

    Livewire::actingAs($other)
        ->test(LinkDetail::class, ['link' => $link])
        ->assertStatus(403);
});

it('redirects a guest away from the detail page', function () {
    $link = Link::factory()->create();

    $this->get(route('links.show', $link))->assertRedirect(route('login'));
});

it('lists only clicks tied to that link', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->create();
    $otherLink = Link::factory()->forUser($user)->create();

    Click::factory()->count(2)->for($link)->create();
    Click::factory()->count(5)->for($otherLink)->create();

    Livewire::actingAs($user)
        ->test(LinkDetail::class, ['link' => $link])
        ->assertViewHas('clicks', fn ($clicks) => $clicks->count() === 2
            && $clicks->every(fn ($click) => $click->link_id === $link->id));
});
