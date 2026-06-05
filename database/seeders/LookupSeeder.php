<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\LinkStatus;
use App\Models\DeviceType;
use App\Models\Browser;

class LookupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        // Link Statuses
        $statusData = [
            ['name' => 'active', 'slug' => 'active', 'description' => 'Active link', 'is_active' => true],
            ['name' => 'disabled', 'slug' => 'disabled', 'description' => 'Disabled link', 'is_active' => false],
        ];

        foreach ($statusData as $data) {
            LinkStatus::updateOrCreate(['slug' => $data['slug']], $data);
        }

        // Device Types
        $deviceData = [
            ['name' => 'Desktop', 'slug' => 'desktop'],
            ['name' => 'Mobile', 'slug' => 'mobile'],
            ['name' => 'Tablet', 'slug' => 'tablet'],
            ['name' => 'Bot', 'slug' => 'bot'],
            ['name' => 'Unknown', 'slug' => 'unknown'],
        ];

        foreach ($deviceData as $data) {
            DeviceType::updateOrCreate(['slug' => $data['slug']], $data);
        }

        // Browsers
        $browserData = [
            ['name' => 'Chrome', 'slug' => 'chrome'],
            ['name' => 'Firefox', 'slug' => 'firefox'],
            ['name' => 'Safari', 'slug' => 'safari'],
            ['name' => 'Edge', 'slug' => 'edge'],
            ['name' => 'Other', 'slug' => 'other'],
        ];

        foreach ($browserData as $data) {
            Browser::updateOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
