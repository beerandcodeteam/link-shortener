<?php

use App\Models\Browser;
use App\Models\DeviceType;
use App\Models\LinkStatus;
use Database\Seeders\LookupSeeder;

beforeEach(function () {
    $this->seed(LookupSeeder::class);
});

it('seeds link_statuses with expected slugs', function () {
    expect(LinkStatus::query()->pluck('slug')->sort()->values()->all())
        ->toBe(['active', 'disabled']);

    expect(LinkStatus::where('slug', 'active')->value('is_active'))->toBeTrue();
    expect(LinkStatus::where('slug', 'disabled')->value('is_active'))->toBeFalse();
});

it('seeds device_types with expected slugs', function () {
    expect(DeviceType::query()->pluck('slug')->sort()->values()->all())
        ->toBe(['bot', 'desktop', 'mobile', 'tablet', 'unknown']);
});

it('seeds browsers with expected slugs', function () {
    expect(Browser::query()->pluck('slug')->sort()->values()->all())
        ->toBe(['chrome', 'edge', 'firefox', 'other', 'safari']);
});

it('keeps slugs unique across lookup tables', function () {
    $count = LinkStatus::query()->count()
        + DeviceType::query()->count()
        + Browser::query()->count();

    $slugs = collect([
        LinkStatus::query()->pluck('slug'),
        DeviceType::query()->pluck('slug'),
        Browser::query()->pluck('slug'),
    ])->flatten();

    expect($slugs->count())->toBe($count);
    expect($slugs->unique()->count())->toBe($count);
});

it('is idempotent when re-running the seeder', function () {
    $before = [
        'link_statuses' => LinkStatus::query()->count(),
        'device_types' => DeviceType::query()->count(),
        'browsers' => Browser::query()->count(),
    ];

    $this->seed(LookupSeeder::class);

    expect(LinkStatus::query()->count())->toBe($before['link_statuses']);
    expect(DeviceType::query()->count())->toBe($before['device_types']);
    expect(Browser::query()->count())->toBe($before['browsers']);
});
