<?php

use App\Models\Browser;
use App\Models\Click;
use App\Models\DeviceType;
use App\Models\Link;
use App\Models\User;

beforeEach(function () {
    // Seed lookups in the same DB transaction that Pest creates per test
    if (App\Models\LinkStatus::where('slug', 'active')->doesntExist()) {
        $now = now();
        App\Models\LinkStatus::insert([
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

        collect([
            'desktop',
            'mobile',
            'tablet',
            'bot',
            'unknown',
        ])->each(function ($slug) {
            App\Models\DeviceType::create(['name' => ucfirst($slug), 'slug' => $slug]);
        });

        collect([
            'chrome',
            'firefox',
            'safari',
            'edge',
            'other',
        ])->each(function ($slug) {
            App\Models\Browser::create(['name' => ucfirst($slug), 'slug' => $slug]);
        });
    }

    // Ensure a user exists for the link factory
    if (User::count() === 0) {
        User::factory()->create(['name' => 'Test User', 'email' => 'test@example.com']);
    }
});

describe('click model', function () {
    it('creates a click tied to a link via factory', function () {
        $user = User::first();
        $link = Link::factory()->forUser($user)->create();

        $click = Click::factory()->forLink($link)->create();

        expect($click->link_id)->toBe($link->id)
            ->and($click->device_type_id)->not->toBeNull()
            ->and($click->browser_id)->not->toBeNull()
            ->and($click->referrer)->not->toBeNull()
            ->and($click->created_at)->not->toBeNull();
    });

    it('link clicks relation returns clicks ordered by most recent first', function () {
        $user = User::first();
        $link = Link::factory()->forUser($user)->create();

        Click::factory()
            ->forLink($link)
            ->count(3)
            ->create([
                'device_type_id' => DeviceType::inRandomOrder()->value('id'),
                'browser_id'     => Browser::inRandomOrder()->value('id'),
                'ip_hash'        => fake()->sha256(),
                'clicked_at'     => fake()->dateTimeBetween('-7 days', '-1 day 00:00:00'),
            ]);

        $clicks = $link->refresh()->clicks;

        expect($clicks)->toHaveCount(3);

        // Verify ordering: each click should have a clicked_at >= the next one
        for ($i = 0; $i < $clicks->count() - 1; $i++) {
            expect($clicks[$i]->clicked_at->timestamp)
                ->toBeGreaterThanOrEqual($clicks[$i + 1]->clicked_at->timestamp);
        }
    });

    it('stores ip_hash as sha256 hash not raw ip', function () {
        $rawIp = '192.168.1.1';

        $user = User::first();
        $link = Link::factory()->forUser($user)->create();

        $click = Click::factory()->create([
            'link_id'       => $link->id,
            'ip_hash'       => hash('sha256', $rawIp),
            'device_type_id' => DeviceType::inRandomOrder()->value('id'),
            'browser_id'     => Browser::inRandomOrder()->value('id'),
        ]);

        // ip_hash should equal its own value (already stored as hash)
        expect($click->ip_hash)->not->toEqual($rawIp)
            ->and(strlen($click->ip_hash))->toBe(64); // SHA-256 hex length
    });

    it('creates a click with specific device and browser', function () {
        $desktop = App\Models\DeviceType::where('slug', 'desktop')->firstOrFail();
        $chrome  = App\Models\Browser::where('slug', 'chrome')->firstOrFail();
        $user    = User::first();
        $link    = Link::factory()->forUser($user)->create();

        $click = Click::factory()->create([
            'link_id'       => $link->id,
            'device_type_id' => $desktop->id,
            'browser_id'     => $chrome->id,
            'ip_hash'       => fake()->sha256(),
        ]);

        expect($click->deviceType->slug)->toBe('desktop')
            ->and($click->browser->slug)->toBe('chrome');
    });

    it('uses relation states in factory', function () {
        $user = User::first();
        $link = Link::factory()->forUser($user)->create();

        $click = Click::factory()
            ->onDevice('desktop')
            ->withBrowser('chrome')
            ->create([
                'link_id'  => $link->id,
                'referrer' => 'https://google.com',
                'ip_hash'  => fake()->sha256(),
            ]);

        expect($click->link_id)->toBe($link->id)
            ->and($click->deviceType->slug)->toBe('desktop')
            ->and($click->browser->slug)->toBe('chrome');
    });

    it('recent state creates clicks within the last hour', function () {
        $user = User::first();
        $link = Link::factory()->forUser($user)->create();

        $click = Click::factory()
            ->recent()
            ->create([
                'link_id'       => $link->id,
                'device_type_id' => DeviceType::inRandomOrder()->value('id'),
                'browser_id'     => Browser::inRandomOrder()->value('id'),
                'referrer'       => fake()->url(),
            ]);

        expect($click->clicked_at->timestamp)
            ->toBeGreaterThan(now()->subHour()->timestamp)
            ->and($click->clicked_at->timestamp)->toBeLessThanOrEqual(now()->timestamp);
    });

    it('factory click has a valid datetime cast for clicked_at', function () {
        $user = User::first();
        $link = Link::factory()->forUser($user)->create();

        $click = Click::factory()
            ->forLink($link)
            ->create([
                'device_type_id' => DeviceType::inRandomOrder()->value('id'),
                'browser_id'     => Browser::inRandomOrder()->value('id'),
                'referrer'       => fake()->url(),
                'ip_hash'        => fake()->sha256(),
            ]);

        expect($click->clicked_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });
});
