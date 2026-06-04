<?php

use App\Models\Link;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('allows the owner to view, update and delete a link', function () {
    $owner = User::factory()->create();
    $link = Link::factory()->forUser($owner)->create();

    expect(Gate::forUser($owner)->allows('view', $link))->toBeTrue();
    expect(Gate::forUser($owner)->allows('update', $link))->toBeTrue();
    expect(Gate::forUser($owner)->allows('delete', $link))->toBeTrue();
});

it('denies a non-owner user from viewing, updating and deleting a link', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $link = Link::factory()->forUser($owner)->create();

    expect(Gate::forUser($stranger)->denies('view', $link))->toBeTrue();
    expect(Gate::forUser($stranger)->denies('update', $link))->toBeTrue();
    expect(Gate::forUser($stranger)->denies('delete', $link))->toBeTrue();
});

it('denies a guest (no authenticated user) for all dashboard actions', function () {
    $owner = User::factory()->create();
    $link = Link::factory()->forUser($owner)->create();

    // Guest visits to dashboard routes get redirected to login.
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});

it('does not authorize a non-owner to manage another user link via the policy', function () {
    $owner = User::factory()->create();
    $stranger = User::factory()->create();
    $link = Link::factory()->forUser($owner)->active()->create();

    // Verify the policy-level authorization is the source of truth.
    expect($stranger->cannot('view', $link))->toBeTrue();
    expect($stranger->cannot('update', $link))->toBeTrue();
    expect($stranger->cannot('delete', $link))->toBeTrue();
});
