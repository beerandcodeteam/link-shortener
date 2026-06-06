<?php

use App\Models\Link;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('lets the owner view, update and delete their link', function () {
    $owner = User::factory()->create();
    $link = Link::factory()->forUser($owner)->create();

    expect($owner->can('view', $link))->toBeTrue();
    expect($owner->can('update', $link))->toBeTrue();
    expect($owner->can('delete', $link))->toBeTrue();
});

it('denies a non-owner from viewing, updating or deleting a link', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $link = Link::factory()->forUser($owner)->create();

    expect($other->can('view', $link))->toBeFalse();
    expect($other->can('update', $link))->toBeFalse();
    expect($other->can('delete', $link))->toBeFalse();
});

it('redirects a guest away from dashboard actions', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));

    $link = Link::factory()->create();
    $this->get(route('links.show', $link))->assertRedirect(route('login'));
});

it('forbids a non-owner from reaching the link detail page', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $link = Link::factory()->forUser($owner)->create();

    $this->actingAs($other)
        ->get(route('links.show', $link))
        ->assertForbidden();
});
