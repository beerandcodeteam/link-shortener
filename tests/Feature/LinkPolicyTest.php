<?php

use App\Models\Click;
use App\Models\Link;
use App\Models\LinkStatus;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    // Ensure lookup statuses exist before factory creation (fresh DB).
    LinkStatus::firstOrCreate(
        ['slug' => 'active'],
        ['name' => 'Active', 'description' => 'The link is fully functional.', 'is_active' => true],
    );

    LinkStatus::firstOrCreate(
        ['slug' => 'disabled'],
        ['name' => 'Disabled', 'description' => 'The link has been disabled.', 'is_active' => false],
    );

    $this->owner = User::factory()->create();
    $this->nonOwner = User::factory()->create();
    $this->link = Link::factory()->for($this->owner)->create();
});

// ---- Owner passes view/update/delete ----

test('owner can view own link via authorize', function () {
    Gate::authorize('view', $this->link); // no exception = success
})->throws(AuthorizationException::class, false);

test('owner can update own link via authorize', function () {
    Gate::authorize('update', $this->link); // no exception = success
})->throws(AuthorizationException::class, false);

test('owner can delete own link via authorize', function () {
    Gate::authorize('delete', $this->link); // no exception = success
})->throws(AuthorizationException::class, false);

// ---- Non-owner is denied access ----

test('non-owner cannot view another user\'s link', function () {
    Gate::authorize('view', $this->link); // should throw for non-owner
})->throws(AuthorizationException::class);

test('non-owner cannot update another user\'s link', function () {
    Gate::authorize('update', $this->link); // should throw for non-owner
})->throws(AuthorizationException::class);

test('non-owner cannot delete another user\'s link', function () {
    Gate::authorize('delete', $this->link); // should throw for non-owner
})->throws(AuthorizationException::class);

// ---- Guest is denied ----

test('guest cannot view another user\'s link', function () {
    Gate::authorize('view', $this->link); // should throw for guest
})->throws(AuthorizationException::class);

test('guest cannot update another user\'s link', function () {
    Gate::authorize('update', $this->link); // should throw for guest
})->throws(AuthorizationException::class);

test('guest cannot delete another user\'s link', function () {
    Gate::authorize('delete', $this->link); // should throw for guest
})->throws(AuthorizationException::class);

// ---- Direct policy unit tests ----

test('LinkPolicy view() returns true for owner', function () {
    $policy = new \App\Policies\LinkPolicy();

    expect($policy->view($this->owner, $this->link))->toBeTrue();
});

test('LinkPolicy view() returns false for non-owner', function () {
    $policy = new \App\Policies\LinkPolicy();

    expect($policy->view($this->nonOwner, $this->link))->toBeFalse();
});

test('LinkPolicy update() returns true for owner', function () {
    $policy = new \App\Policies\LinkPolicy();

    expect($policy->update($this->owner, $this->link))->toBeTrue();
});

test('LinkPolicy update() returns false for non-owner', function () {
    $policy = new \App\Policies\LinkPolicy();

    expect($policy->update($this->nonOwner, $this->link))->toBeFalse();
});

test('LinkPolicy delete() returns true for owner', function () {
    $policy = new \App\Policies\LinkPolicy();

    expect($policy->delete($this->owner, $this->link))->toBeTrue();
});

test('LinkPolicy delete() returns false for non-owner', function () {
    $policy = new \App\Policies\LinkPolicy();

    expect($policy->delete($this->nonOwner, $this->link))->toBeFalse();
});
