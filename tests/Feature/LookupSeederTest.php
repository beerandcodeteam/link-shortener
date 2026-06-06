<?php

use App\Models\Browser;
use App\Models\DeviceType;
use App\Models\LinkStatus;
use Database\Seeders\LookupSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('seeds the expected lookup rows', function () {
    $this->seed(LookupSeeder::class);

    expect(LinkStatus::pluck('slug')->all())->toEqualCanonicalizing(['active', 'disabled']);
    expect(DeviceType::pluck('slug')->all())->toEqualCanonicalizing(['desktop', 'mobile', 'tablet', 'bot', 'unknown']);
    expect(Browser::pluck('slug')->all())->toEqualCanonicalizing(['chrome', 'firefox', 'safari', 'edge', 'other']);
});

it('keeps slugs unique across each lookup table', function () {
    $this->seed(LookupSeeder::class);

    expect(LinkStatus::distinct('slug')->count('slug'))->toBe(LinkStatus::count());
    expect(DeviceType::distinct('slug')->count('slug'))->toBe(DeviceType::count());
    expect(Browser::distinct('slug')->count('slug'))->toBe(Browser::count());
});

it('is idempotent when re-run', function () {
    $this->seed(LookupSeeder::class);
    $this->seed(LookupSeeder::class);

    expect(LinkStatus::count())->toBe(2);
    expect(DeviceType::count())->toBe(5);
    expect(Browser::count())->toBe(5);
});
