<?php

use App\Models\Link;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('renders the login page for guests', function () {
    $this->get('/login')->assertOk();
});

it('redirects authenticated users away from the login page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)->get('/login')->assertRedirect(route('dashboard'));
});

it('authenticates the user and redirects to the dashboard on valid credentials', function () {
    $user = User::factory()->create([
        'email' => 'alex@example.com',
        'password' => Hash::make('super-secret'),
    ]);

    Livewire::test('auth.login')
        ->set('email', 'alex@example.com')
        ->set('password', 'super-secret')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    expect(auth()->check())->toBeTrue();
    expect(auth()->id())->toBe($user->id);
});

it('shows a generic error and does not authenticate on wrong password', function () {
    User::factory()->create([
        'email' => 'alex@example.com',
        'password' => Hash::make('super-secret'),
    ]);

    Livewire::test('auth.login')
        ->set('email', 'alex@example.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['email'])
        ->assertNoRedirect();

    expect(auth()->check())->toBeFalse();
});

it('shows the same generic error and does not authenticate when the email is unknown', function () {
    Livewire::test('auth.login')
        ->set('email', 'unknown@example.com')
        ->set('password', 'doesnt-matter')
        ->call('login')
        ->assertHasErrors(['email'])
        ->assertNoRedirect();

    expect(auth()->check())->toBeFalse();
});

it('does not leak which field was wrong (email vs password) in the error message', function () {
    User::factory()->create([
        'email' => 'alex@example.com',
        'password' => Hash::make('super-secret'),
    ]);

    $wrongPassword = Livewire::test('auth.login')
        ->set('email', 'alex@example.com')
        ->set('password', 'wrong')
        ->call('login')
        ->errors()
        ->toArray();

    $wrongEmail = Livewire::test('auth.login')
        ->set('email', 'nobody@example.com')
        ->set('password', 'wrong')
        ->call('login')
        ->errors()
        ->toArray();

    expect($wrongPassword)->toHaveKey('email');
    expect($wrongEmail)->toHaveKey('email');

    expect($wrongPassword['email'][0])->toBe($wrongEmail['email'][0]);
});

it('requires both email and password', function () {
    Livewire::test('auth.login')
        ->set('email', '')
        ->set('password', '')
        ->call('login')
        ->assertHasErrors(['email', 'password'])
        ->assertNoRedirect();
});

it('materialises any pending shorten payload on the session after login', function () {
    $user = User::factory()->create([
        'email' => 'alex@example.com',
        'password' => Hash::make('super-secret'),
    ]);

    session()->put('pending_shorten', [
        'original_url' => 'https://example.com/from-guest',
        'custom_code' => 'loginpick',
    ]);

    Livewire::test('auth.login')
        ->set('email', 'alex@example.com')
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

it('links to the password-reset page', function () {
    $page = $this->get('/login');

    $page->assertSee('Forgot your password?', false);
    $page->assertSee(route('password.request'), false);
});
