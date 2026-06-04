<?php

namespace Database\Seeders;

use App\Models\Browser;
use App\Models\DeviceType;
use App\Models\LinkStatus;
use Illuminate\Database\Seeder;

class LookupSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        $linkStatuses = [
            ['name' => 'Active', 'slug' => 'active', 'description' => 'Link is live and clickable.', 'is_active' => true],
            ['name' => 'Disabled', 'slug' => 'disabled', 'description' => 'Link is disabled and returns 404.', 'is_active' => false],
        ];

        foreach ($linkStatuses as $status) {
            LinkStatus::updateOrCreate(
                ['slug' => $status['slug']],
                $status,
            );
        }

        $deviceTypes = [
            ['name' => 'Desktop', 'slug' => 'desktop'],
            ['name' => 'Mobile', 'slug' => 'mobile'],
            ['name' => 'Tablet', 'slug' => 'tablet'],
            ['name' => 'Bot', 'slug' => 'bot'],
            ['name' => 'Unknown', 'slug' => 'unknown'],
        ];

        foreach ($deviceTypes as $deviceType) {
            DeviceType::updateOrCreate(
                ['slug' => $deviceType['slug']],
                $deviceType,
            );
        }

        $browsers = [
            ['name' => 'Chrome', 'slug' => 'chrome'],
            ['name' => 'Firefox', 'slug' => 'firefox'],
            ['name' => 'Safari', 'slug' => 'safari'],
            ['name' => 'Edge', 'slug' => 'edge'],
            ['name' => 'Other', 'slug' => 'other'],
        ];

        foreach ($browsers as $browser) {
            Browser::updateOrCreate(
                ['slug' => $browser['slug']],
                $browser,
            );
        }
    }
}
