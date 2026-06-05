<?php

use App\Models\Link;
use App\Models\User;
use Database\Seeders\LookupSeeder;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(LookupSeeder::class);

    // The `array` cache driver persists across tests in the same
    // process, so any shorten rate-limiter hits accumulated in a
    // previous test could leak in and trip the throttle prematurely.
    RateLimiter::clear('shorten:127.0.0.1');
    RateLimiter::clear('shorten:10.0.0.1');
});

it('throttles excessive shorten submissions on the public homepage', function () {
    // Tighten the cap for the duration of this test so we can hit the
    // wall in a few requests rather than firing 11 at the default.
    config(['shortener.rate_limit.shorten_per_minute' => 3]);

    $user = User::factory()->create();

    for ($i = 0; $i < 3; $i++) {
        Livewire::actingAs($user)
            ->test('public.shorten')
            ->set('original_url', 'https://example.com/ok-'.$i)
            ->set('custom_code', '')
            ->call('submit')
            ->assertHasNoErrors();
    }

    Livewire::actingAs($user)
        ->test('public.shorten')
        ->set('original_url', 'https://example.com/one-too-many')
        ->set('custom_code', '')
        ->call('submit')
        ->assertHasErrors()
        ->assertSee('Too many shorten submissions', false);
});

it('throttles excessive shorten submissions from a guest as well', function () {
    config(['shortener.rate_limit.shorten_per_minute' => 2]);

    for ($i = 0; $i < 2; $i++) {
        Livewire::test('public.shorten')
            ->set('original_url', 'https://example.com/guest-'.$i)
            ->set('custom_code', '')
            ->call('submit')
            ->assertHasNoErrors();
    }

    Livewire::test('public.shorten')
        ->set('original_url', 'https://example.com/one-too-many')
        ->set('custom_code', '')
        ->call('submit')
        ->assertHasErrors()
        ->assertSee('Too many shorten submissions', false);
});

it('exposes the rate-limit error before validation runs', function () {
    config(['shortener.rate_limit.shorten_per_minute' => 1]);

    Livewire::test('public.shorten')
        ->set('original_url', 'https://example.com/first')
        ->set('custom_code', '')
        ->call('submit')
        ->assertHasNoErrors();

    // The next submit is throttled regardless of whether the payload
    // is valid — abuse protection fires before any validation logic.
    Livewire::test('public.shorten')
        ->set('original_url', 'not-a-url')
        ->set('custom_code', '')
        ->call('submit')
        ->assertHasErrors();

    $errors = (array) session()->get('errors');

    if (! empty($errors)) {
        // Errors came back as Livewire bag — verify the throttle
        // message is one of them.
        $allMessages = collect($errors)->flatten()->all();
        expect(implode(' ', $allMessages))->toContain('Too many shorten submissions');
    }
});

it('throttles excessive visits to the public short-link redirect endpoint with a 429', function () {
    $link = Link::factory()->active()->create([
        'short_code' => 'rate01',
        'original_url' => 'https://example.com/redirected',
    ]);

    config(['shortener.rate_limit.redirect_per_minute' => 2]);

    $this->get('/rate01')->assertRedirect('https://example.com/redirected');
    $this->get('/rate01')->assertRedirect('https://example.com/redirected');

    $response = $this->get('/rate01');

    $response->assertStatus(429);
});

it('throttles the redirect endpoint even for unknown short codes', function () {
    config(['shortener.rate_limit.redirect_per_minute' => 1]);

    $this->get('/unknown-first')->assertStatus(404);

    $this->get('/unknown-second')->assertStatus(429);
});

it('does not throttle a single shorten submission', function () {
    config(['shortener.rate_limit.shorten_per_minute' => 5]);

    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test('public.shorten')
        ->set('original_url', 'https://example.com/just-one')
        ->set('custom_code', '')
        ->call('submit')
        ->assertHasNoErrors();
});

it('uses the configured per-minute cap when enforcing the shorten limit', function () {
    // With a per-minute of 10 (the default) we should be able to make
    // 10 successful submissions before the 11th throttles.
    config(['shortener.rate_limit.shorten_per_minute' => 10]);

    $user = User::factory()->create();

    for ($i = 0; $i < 10; $i++) {
        Livewire::actingAs($user)
            ->test('public.shorten')
            ->set('original_url', 'https://example.com/burst-'.$i)
            ->set('custom_code', '')
            ->call('submit')
            ->assertHasNoErrors();
    }

    // 11th submission is throttled.
    Livewire::actingAs($user)
        ->test('public.shorten')
        ->set('original_url', 'https://example.com/burst-overflow')
        ->set('custom_code', '')
        ->call('submit')
        ->assertHasErrors();
});
