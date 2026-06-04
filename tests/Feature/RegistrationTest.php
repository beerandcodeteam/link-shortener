<?php

use App\Models\Link;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('renders the registration page for guests', function () {
    $this->get('/register')->assertOk();
});

it('redirects authenticated users away from the registration page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/register')->assertRedirect(route('dashboard'));
});

it('creates the user, authenticates, and redirects to the dashboard on valid data', function () {
    Livewire::test('auth.register')
        ->set('name', 'Alex Rivera')
        ->set('email', 'alex@example.com')
        ->set('password', 'super-secret')
        ->set('password_confirmation', 'super-secret')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $user = User::query()->where('email', 'alex@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->name)->toBe('Alex Rivera');
    expect(auth()->check())->toBeTrue();
    expect(auth()->id())->toBe($user->id);
});

it('rejects a duplicate email', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    Livewire::test('auth.register')
        ->set('name', 'Alex Rivera')
        ->set('email', 'taken@example.com')
        ->set('password', 'super-secret')
        ->set('password_confirmation', 'super-secret')
        ->call('register')
        ->assertHasErrors(['email' => 'unique'])
        ->assertNoRedirect();

    expect(User::query()->where('email', 'taken@example.com')->count())->toBe(1);
    expect(auth()->check())->toBeFalse();
});

it('rejects a password shorter than 8 characters', function () {
    Livewire::test('auth.register')
        ->set('name', 'Alex Rivera')
        ->set('email', 'short@example.com')
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('register')
        ->assertHasErrors(['password' => 'min'])
        ->assertNoRedirect();

    expect(User::query()->where('email', 'short@example.com')->exists())->toBeFalse();
    expect(auth()->check())->toBeFalse();
});

it('rejects a password confirmation that does not match', function () {
    Livewire::test('auth.register')
        ->set('name', 'Alex Rivera')
        ->set('email', 'mismatch@example.com')
        ->set('password', 'super-secret')
        ->set('password_confirmation', 'totally-different')
        ->call('register')
        ->assertHasErrors(['password' => 'confirmed'])
        ->assertNoRedirect();

    expect(User::query()->where('email', 'mismatch@example.com')->exists())->toBeFalse();
    expect(auth()->check())->toBeFalse();
});

it('rejects a missing name', function () {
    Livewire::test('auth.register')
        ->set('name', '')
        ->set('email', 'noname@example.com')
        ->set('password', 'super-secret')
        ->set('password_confirmation', 'super-secret')
        ->call('register')
        ->assertHasErrors(['name' => 'required'])
        ->assertNoRedirect();

    expect(User::query()->where('email', 'noname@example.com')->exists())->toBeFalse();
});

it('rejects an invalid email', function () {
    Livewire::test('auth.register')
        ->set('name', 'Alex Rivera')
        ->set('email', 'not-an-email')
        ->set('password', 'super-secret')
        ->set('password_confirmation', 'super-secret')
        ->call('register')
        ->assertHasErrors(['email' => 'email'])
        ->assertNoRedirect();
});

it('hashes the stored password so the plaintext is never persisted', function () {
    Livewire::test('auth.register')
        ->set('name', 'Alex Rivera')
        ->set('email', 'hashed@example.com')
        ->set('password', 'super-secret')
        ->set('password_confirmation', 'super-secret')
        ->call('register');

    $user = User::query()->where('email', 'hashed@example.com')->first();

    expect($user)->not->toBeNull();
    expect($user->password)->not->toBe('super-secret');
    expect(password_verify('super-secret', $user->password))->toBeTrue();
});

it('materialises any pending shorten payload on the session after registration', function () {
    session()->put('pending_shorten', [
        'original_url' => 'https://example.com/from-guest',
        'custom_code' => 'guestpick',
    ]);

    Livewire::test('auth.register')
        ->set('name', 'Alex Rivera')
        ->set('email', 'bridged@example.com')
        ->set('password', 'super-secret')
        ->set('password_confirmation', 'super-secret')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $user = User::query()->where('email', 'bridged@example.com')->first();

    $link = Link::query()->where('user_id', $user->id)->first();

    expect($link)->not->toBeNull();
    expect($link->original_url)->toBe('https://example.com/from-guest');
    expect($link->short_code)->toBe('guestpick');
    expect(session()->has('pending_shorten'))->toBeFalse();
});
