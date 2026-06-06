<?php

use App\Livewire\Dashboard;
use App\Livewire\LinkDetail;
use App\Models\Link;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Js;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('renders the absolute short url in the dashboard list', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->create(['short_code' => 'abc1234']);

    $absolute = route('redirect', $link->short_code);

    expect($absolute)->toStartWith('http');

    // The CopyButton carries the full absolute URL as its Alpine copy payload.
    Livewire::actingAs($user)
        ->test(Dashboard::class)
        ->assertSeeHtml((string) Js::from($absolute));
});

it('renders the absolute short url on the detail page', function () {
    $user = User::factory()->create();
    $link = Link::factory()->forUser($user)->create(['short_code' => 'detail7']);

    Livewire::actingAs($user)
        ->test(LinkDetail::class, ['link' => $link])
        ->assertSee(route('redirect', $link->short_code));
});
