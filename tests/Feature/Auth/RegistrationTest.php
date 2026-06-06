<?php

use App\Livewire\Auth\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('creates a user, authenticates, and redirects to the dashboard with valid data', function () {
    Livewire::test(Register::class)
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    expect(User::where('email', 'jane@example.com')->exists())->toBeTrue();
    $this->assertAuthenticated();
});

it('rejects a duplicate email', function () {
    User::factory()->create(['email' => 'taken@example.com']);

    Livewire::test(Register::class)
        ->set('name', 'Jane Doe')
        ->set('email', 'taken@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'password123')
        ->call('register')
        ->assertHasErrors(['email']);

    $this->assertGuest();
});

it('rejects a password shorter than 8 characters', function () {
    Livewire::test(Register::class)
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('password', 'short')
        ->set('password_confirmation', 'short')
        ->call('register')
        ->assertHasErrors(['password']);

    $this->assertGuest();
});

it('rejects a password confirmation mismatch', function () {
    Livewire::test(Register::class)
        ->set('name', 'Jane Doe')
        ->set('email', 'jane@example.com')
        ->set('password', 'password123')
        ->set('password_confirmation', 'different123')
        ->call('register')
        ->assertHasErrors(['password']);

    $this->assertGuest();
});
