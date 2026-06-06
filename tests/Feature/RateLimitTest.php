<?php

use App\Livewire\Shorten;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('throttles excessive shorten submissions with a 429 after the configured limit', function () {
    // The component allows MAX_ATTEMPTS (10) submissions per IP per minute.
    for ($attempt = 1; $attempt <= 10; $attempt++) {
        Livewire::test(Shorten::class)
            ->set('form.original_url', 'https://example.com/page-'.$attempt)
            ->call('shorten')
            ->assertRedirect(route('register'));
    }

    // The next submission exceeds the limit and is rejected with HTTP 429.
    Livewire::test(Shorten::class)
        ->set('form.original_url', 'https://example.com/over-the-limit')
        ->call('shorten')
        ->assertStatus(429);
});

it('allows submissions that stay within the configured limit', function () {
    Livewire::test(Shorten::class)
        ->set('form.original_url', 'https://example.com/allowed')
        ->call('shorten')
        ->assertStatus(200)
        ->assertRedirect(route('register'));
});
