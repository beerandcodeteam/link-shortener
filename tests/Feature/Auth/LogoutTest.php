<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('logs out an authenticated user, invalidates the session, and redirects home', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post('/logout')
        ->assertRedirect('/');

    $this->assertGuest();
});

it('does not allow a guest to reach the logout action', function () {
    $this->post('/logout')->assertRedirect(route('login'));
});
