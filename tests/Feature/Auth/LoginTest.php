<?php

use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('authenticates and redirects to the dashboard with valid credentials', function () {
    $user = User::factory()->create(['email' => 'user@example.com']);

    Livewire::test(Login::class)
        ->set('email', 'user@example.com')
        ->set('password', 'password')
        ->call('login')
        ->assertHasNoErrors()
        ->assertRedirect(route('dashboard'));

    $this->assertAuthenticatedAs($user);
});

it('shows a generic error and does not authenticate with invalid credentials', function () {
    User::factory()->create(['email' => 'user@example.com']);

    Livewire::test(Login::class)
        ->set('email', 'user@example.com')
        ->set('password', 'wrong-password')
        ->call('login')
        ->assertHasErrors(['email']);

    $this->assertGuest();
});
