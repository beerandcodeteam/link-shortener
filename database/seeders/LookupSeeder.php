<?php

namespace Database\Seeders;

use App\Models\Browser;
use App\Models\DeviceType;
use App\Models\LinkStatus;
use Illuminate\Database\Seeder;

class LookupSeeder extends Seeder
{
    /**
     * Seed lookup tables used by UserAgentParser and link management.
     */
    public function run(): void
    {
        LinkStatus::firstOrCreate(['slug' => 'active'], ['name' => 'Active']);
        LinkStatus::firstOrCreate(['slug' => 'disabled'], ['name' => 'Disabled']);

        Browser::firstOrCreate(['slug' => 'chrome'], ['name' => 'Chrome']);
        Browser::firstOrCreate(['slug' => 'edge'], ['name' => 'Edge']);
        Browser::firstOrCreate(['slug' => 'safari'], ['name' => 'Safari']);
        Browser::firstOrCreate(['slug' => 'firefox'], ['name' => 'Firefox']);
        Browser::firstOrCreate(['slug' => 'other'], ['name' => 'Other']);

        DeviceType::firstOrCreate(['slug' => 'desktop'], ['name' => 'Desktop']);
        DeviceType::firstOrCreate(['slug' => 'mobile'], ['name' => 'Mobile']);
        DeviceType::firstOrCreate(['slug' => 'tablet'], ['name' => 'Tablet']);
        DeviceType::firstOrCreate(['slug' => 'bot'], ['name' => 'Bot']);
    }
}
