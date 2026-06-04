<?php

use App\Models\Link;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('redirects a guest to registration and stashes the pending payload in the session', function () {
    Livewire::test('public.shorten')
        ->set('original_url', 'https://example.com/long-url')
        ->set('custom_code', 'pickme12')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('register'));

    $payload = session('pending_shorten');

    expect($payload)->toBe([
        'original_url' => 'https://example.com/long-url',
        'custom_code' => 'pickme12',
    ]);
});

it('redirects a guest to registration even without a custom code', function () {
    Livewire::test('public.shorten')
        ->set('original_url', 'https://example.com/long-url')
        ->set('custom_code', '')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('register'));

    $payload = session('pending_shorten');

    expect($payload)->toBe([
        'original_url' => 'https://example.com/long-url',
        'custom_code' => null,
    ]);
});

it('creates the link owned by the new user when registration completes the bridge', function () {
    session()->put('pending_shorten', [
        'original_url' => 'https://example.com/from-guest',
        'custom_code' => 'bridged',
    ]);

    Livewire::test('auth.register')
        ->set('name', 'Alex Rivera')
        ->set('email', 'newbie@example.com')
        ->set('password', 'super-secret')
        ->set('password_confirmation', 'super-secret')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $user = User::query()->where('email', 'newbie@example.com')->first();
    $link = Link::query()->where('user_id', $user->id)->first();

    expect($link)->not->toBeNull();
    expect($link->original_url)->toBe('https://example.com/from-guest');
    expect($link->short_code)->toBe('bridged');
    expect($link->linkStatus->slug)->toBe('active');

    // The pending payload is cleared from the session so a subsequent
    // unrelated redirect (e.g. by an attacker) does not materialise a
    // second link for the same user.
    expect(session()->has('pending_shorten'))->toBeFalse();
});

it('creates the link owned by the existing user when login completes the bridge', function () {
    $user = User::factory()->create([
        'email' => 'returning@example.com',
        'password' => bcrypt('super-secret'),
    ]);

    session()->put('pending_shorten', [
        'original_url' => 'https://example.com/from-guest',
        'custom_code' => 'loginpick',
    ]);

    Livewire::test('auth.login')
        ->set('email', 'returning@example.com')
        ->set('password', 'super-secret')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $link = Link::query()->where('user_id', $user->id)->first();

    expect($link)->not->toBeNull();
    expect($link->original_url)->toBe('https://example.com/from-guest');
    expect($link->short_code)->toBe('loginpick');

    expect(session()->has('pending_shorten'))->toBeFalse();
});

it('does not create a link when no pending payload is in the session after registration', function () {
    Livewire::test('auth.register')
        ->set('name', 'Alex Rivera')
        ->set('email', 'no-bridge@example.com')
        ->set('password', 'super-secret')
        ->set('password_confirmation', 'super-secret')
        ->call('register')
        ->assertHasNoErrors();

    $user = User::query()->where('email', 'no-bridge@example.com')->first();

    expect(Link::query()->where('user_id', $user->id)->count())->toBe(0);
});

it('lets an authenticated visitor create the link directly without the auth detour', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('public.shorten')
        ->set('original_url', 'https://example.com/already-in')
        ->set('custom_code', 'direct12')
        ->call('submit')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $link = Link::query()->where('user_id', $user->id)->first();

    expect($link)->not->toBeNull();
    expect($link->original_url)->toBe('https://example.com/already-in');
    expect($link->short_code)->toBe('direct12');

    // No pending payload was ever written for an authenticated user.
    expect(session()->has('pending_shorten'))->toBeFalse();
});

it('auto-generates a unique short code for the authenticated visitor who omits a custom one', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('public.shorten')
        ->set('original_url', 'https://example.com/already-in')
        ->set('custom_code', '')
        ->call('submit')
        ->assertHasNoErrors();

    $link = Link::query()->where('user_id', $user->id)->first();

    expect($link)->not->toBeNull();
    expect($link->original_url)->toBe('https://example.com/already-in');
    expect($link->short_code)->not->toBe('');
    expect($link->short_code)->toMatch('/^[abcdefghijkmnpqrstuvwxyz23456789]+$/');
});

it('rejects an invalid URL on the homepage shorten form', function () {
    Livewire::test('public.shorten')
        ->set('original_url', 'not-a-url')
        ->set('custom_code', '')
        ->call('submit')
        ->assertHasErrors(['original_url'])
        ->assertNoRedirect();

    expect(session()->has('pending_shorten'))->toBeFalse();
});

it('rejects a non-http(s) scheme on the homepage shorten form', function () {
    Livewire::test('public.shorten')
        ->set('original_url', 'ftp://example.com/file.zip')
        ->set('custom_code', '')
        ->call('submit')
        ->assertHasErrors(['original_url'])
        ->assertNoRedirect();

    expect(session()->has('pending_shorten'))->toBeFalse();
});

it('rejects a reserved custom code on the homepage shorten form for authenticated visitors', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('public.shorten')
        ->set('original_url', 'https://example.com/valid')
        ->set('custom_code', 'login')
        ->call('submit')
        ->assertHasErrors(['custom_code'])
        ->assertNoRedirect();

    expect(Link::query()->where('user_id', $user->id)->count())->toBe(0);
});

it('rejects a duplicate custom code on the homepage shorten form for authenticated visitors', function () {
    $user = User::factory()->create();

    Link::factory()->forUser($user)->create(['short_code' => 'taken12']);

    $this->actingAs($user);

    Livewire::test('public.shorten')
        ->set('original_url', 'https://example.com/valid')
        ->set('custom_code', 'taken12')
        ->call('submit')
        ->assertHasErrors(['custom_code'])
        ->assertNoRedirect();

    expect(Link::query()->where('user_id', $user->id)->count())->toBe(1);
});

it('surfaces the just-created link on the dashboard with a copy action', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Livewire::test('public.shorten')
        ->set('original_url', 'https://example.com/already-in')
        ->set('custom_code', 'shown12')
        ->call('submit')
        ->assertHasNoErrors();

    $link = Link::query()->where('user_id', $user->id)->where('short_code', 'shown12')->first();

    expect($link)->not->toBeNull();

    // Follow the redirect to the dashboard and verify the just-created
    // card is rendered with the full short URL.
    $page = $this->get(route('dashboard'));
    $shortUrl = url('/shown12');

    $page->assertOk();
    $page->assertSee('just-created-short-url', false);
    $page->assertSee($shortUrl, false);
});
