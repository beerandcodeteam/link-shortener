<?php

use App\Models\Link;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates a link with an owner and status via factory', function () {
    $link = Link::factory()->create();

    expect($link->user)->toBeInstanceOf(User::class);
    expect($link->linkStatus->slug)->toBe('active');
    expect($link->isActive)->toBeTrue();
});

it('returns only the owning user links via the links relation', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();

    Link::factory()->count(2)->forUser($owner)->create();
    Link::factory()->forUser($other)->create();

    expect($owner->links)->toHaveCount(2);
    expect($owner->links->pluck('user_id')->unique()->all())->toBe([$owner->id]);
});

it('filters by status with the active scope', function () {
    Link::factory()->count(2)->active()->create();
    Link::factory()->disabled()->create();

    expect(Link::active()->count())->toBe(2);
});

it('rejects duplicate short codes', function () {
    Link::factory()->create(['short_code' => 'dup123']);

    expect(fn () => Link::factory()->create(['short_code' => 'dup123']))
        ->toThrow(QueryException::class);
});
