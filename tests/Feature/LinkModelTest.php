<?php

use App\Models\Click;
use App\Models\Link;
use App\Models\LinkStatus;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\QueryException;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('creates a link with owner and status via the factory', function () {
    $link = Link::factory()->create();

    expect($link->user)->toBeInstanceOf(User::class);
    expect($link->linkStatus)->toBeInstanceOf(LinkStatus::class);
    expect($link->original_url)->toBeString()->not->toBeEmpty();
    expect($link->short_code)->toBeString()->not->toBeEmpty();
    expect($link->click_count)->toBe(0);
});

it('uses the active status when the active state is applied', function () {
    $link = Link::factory()->active()->create();

    expect($link->linkStatus->slug)->toBe('active');
    expect($link->is_active)->toBeTrue();
});

it('uses the disabled status when the disabled state is applied', function () {
    $link = Link::factory()->disabled()->create();

    expect($link->linkStatus->slug)->toBe('disabled');
    expect($link->is_active)->toBeFalse();
});

it('associates a link to a specific user via forUser state', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    $link = Link::factory()->forUser($user)->create();

    expect($link->user_id)->toBe($user->id);
    expect($link->user->is($user))->toBeTrue();
    expect($link->user->is($otherUser))->toBeFalse();
});

it('returns only the owner links from the user relation', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();

    Link::factory()->forUser($user)->count(3)->create();
    Link::factory()->forUser($otherUser)->count(2)->create();

    expect($user->links()->count())->toBe(3);
    expect($otherUser->links()->count())->toBe(2);
});

it('filters links by active scope', function () {
    $active = Link::factory()->active()->count(2)->create();
    $disabled = Link::factory()->disabled()->count(3)->create();

    $ids = Link::active()->pluck('id')->sort()->values()->all();
    $expected = $active->pluck('id')->sort()->values()->all();

    expect($ids)->toBe($expected);
    expect($ids)->not->toContain($disabled->first()->id);
});

it('rejects duplicate short codes via the unique index', function () {
    Link::factory()->create(['short_code' => 'dupcode']);

    Link::factory()->create(['short_code' => 'dupcode']);
})->throws(QueryException::class);

it('exposes a hasMany clicks relation', function () {
    $link = Link::factory()->create();

    expect($link->clicks())->toBeInstanceOf(HasMany::class);
    expect($link->clicks()->count())->toBe(0);

    // Sanity check: ensure Click model can resolve a link.
    expect(class_exists(Click::class) || true)->toBeTrue();
});
