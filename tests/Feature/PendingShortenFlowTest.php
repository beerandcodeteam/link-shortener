<?php

use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\Shorten;
use App\Models\User;
use App\Services\PendingShortenBridge;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('redirects a guest to register and stashes the payload', function () {
    Livewire::test(Shorten::class)
        ->set('form.original_url', 'https://example.com/long')
        ->set('form.custom_code', 'my-code')
        ->call('shorten')
        ->assertRedirect(route('register'));

    expect(session()->get(PendingShortenBridge::SESSION_KEY))->toBe([
        'original_url' => 'https://example.com/long',
        'custom_code' => 'my-code',
    ]);
});

it('creates the stashed link for a newly registered user and clears the payload', function () {
    session()->put(PendingShortenBridge::SESSION_KEY, [
        'original_url' => 'https://example.com/long',
        'custom_code' => 'my-code',
    ]);

    Livewire::test(Register::class)
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertRedirect(route('dashboard'));

    $user = User::where('email', 'jane@example.com')->first();
    $link = $user->links()->first();

    expect($link)->not->toBeNull();
    expect($link->original_url)->toBe('https://example.com/long');
    expect($link->short_code)->toBe('my-code');
    expect(session()->has(PendingShortenBridge::SESSION_KEY))->toBeFalse();
});

it('creates the stashed link for an existing user logging in and clears the payload', function () {
    $user = User::factory()->create(['email' => 'user@example.com']);

    session()->put(PendingShortenBridge::SESSION_KEY, [
        'original_url' => 'https://example.com/long',
        'custom_code' => 'my-code',
    ]);

    Livewire::test(Login::class)
        ->set('email', 'user@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertRedirect(route('dashboard'));

    $link = $user->links()->first();

    expect($link)->not->toBeNull();
    expect($link->original_url)->toBe('https://example.com/long');
    expect($link->short_code)->toBe('my-code');
    expect(session()->has(PendingShortenBridge::SESSION_KEY))->toBeFalse();
});

it('creates the link directly for an authenticated user without the auth detour', function () {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(Shorten::class)
        ->set('form.original_url', 'https://direct.example.com')
        ->call('shorten')
        ->assertRedirect(route('dashboard'));

    expect($user->links()->where('original_url', 'https://direct.example.com')->exists())->toBeTrue();
    expect(session()->has(PendingShortenBridge::SESSION_KEY))->toBeFalse();
});
