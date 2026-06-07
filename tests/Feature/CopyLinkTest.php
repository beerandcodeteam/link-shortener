<?php

use App\Models\Browser;
use App\Models\DeviceType;
use App\Models\Link;
use App\Models\LinkStatus;
use App\Models\User;

beforeEach(function () {
    // Seed lookups (RefreshDatabase resets them on each test).
    if (! LinkStatus::where('slug', 'active')->exists()) {
        $now = now();
        LinkStatus::insert([
            [
                'name' => 'Active',
                'slug' => 'active',
                'description' => 'Link is active and redirecting',
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Disabled',
                'slug' => 'disabled',
                'description' => 'Link is disabled',
                'is_active' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        collect(['desktop', 'mobile', 'tablet', 'bot', 'unknown'])->each(fn ($slug) => DeviceType::create([
            'name'   => ucfirst($slug),
            'slug'   => $slug,
        ]));

        collect(['chrome', 'firefox', 'safari', 'edge', 'other'])->each(fn ($slug) => Browser::create([
            'name'   => ucfirst($slug),
            'slug'   => $slug,
        ]));
    }
});

describe('CopyLink', function () {
    it('renders the absolute short URL in the dashboard link list', function () {
        $user = User::factory()->create();
        $link = Link::factory()->forUser($user)->create();

        // The accessor returns an absolute URL (with scheme and host).

        expect($link->short_url)->toMatch('#^https?://.*' . preg_quote($link->short_code, '#') . '$#')
            ->not()->toBe('/' . $link->short_code);
    });

    it('renders the correct absolute short URL for different custom codes', function () {
        $user = User::factory()->create();
        $link1 = Link::factory()->forUser($user)->create(['short_code' => 'my-custom']);
        $link2 = Link::factory()->forUser($user)->create(['short_code' => 'another']);

        expect(str($link1->short_url))->toMatch('#^https?://.*my-custom$#');
        expect(str($link2->short_url))->toMatch('#^https?://.*another$#');
    });

    it('renders the absolute short URL for a newly created link', function () {
        $user = User::factory()->create();

        \Livewire\Livewire::test(\App\Livewire\Pages\Dashboard\Index::class)
            ->set('url', 'https://example.com/very/long/path')
            ->call('create');

        $createdLink = Link::where('user_id', $user->id)->first();

        expect($createdLink->short_url)->toMatch('#^https?://.*' . preg_quote($createdLink->short_code, '#') . '$#');
    });

    it('generates a valid absolute URL via the route helper', function () {
        $url = url(route('shorten.show', 'abc123', false));

        expect($url)->toMatch('#^https?://[^/]+/abc123$#');
    });
});
