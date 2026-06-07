<?php

use App\Livewire\Auth\Register;
use App\Models\User;

it('creates user + authenticates + redirects to dashboard on valid data', function () {
    $component = livewire(Register::class);

    $component
        ->set('name', 'Johnny Test')
        ->set('email', 'johnny@example.com')
        ->set('password', 'confirmedPassword123')
        ->set('password_confirmation', 'confirmedPassword123')
        ->call('register')
        ->assertRedirect(route('dashboard'));

    expect(auth()->check())->toBeTrue();
    $this->assertDatabaseHas(User::class, [
        'email' => strtolower('johnny@example.com'),
    ]);
});

it('rejects duplicate email', function () {
    User::factory()->create(['email' => 'johnny@example.com']);

    $component = livewire(Register::class);

    $component
        ->set('name', 'Johnny Test')
        ->set('email', 'johnny@example.com')
        ->set('password', 'confirmedPassword123')
        ->set('password_confirmation', 'confirmedPassword123')
        ->call('register')
        ->assertHasErrors(['email' => 'unique']);

    expect(auth()->check())->toBeFalse();
});

it('rejects password shorter than 8 characters', function () {
    $component = livewire(Register::class);

    $component
        ->set('name', 'Johnny Test')
        ->set('email', 'short-pass@example.com')
        ->set('password', '1234567')
        ->set('password_confirmation', '1234567')
        ->call('register')
        ->assertHasErrors(['password' => 'min']);

    expect(auth()->check())->toBeFalse();
});

it('rejects mismatched passwords', function () {
    $component = livewire(Register::class);

    $component
        ->set('name', 'Johnny Test')
        ->set('email', 'mismatch@example.com')
        ->set('password', 'confirmedPassword123')
        ->set('password_confirmation', 'differentPassword456')
        ->call('register')
        ->assertHasErrors(['password' => 'same']);

    expect(auth()->check())->toBeFalse();
});
