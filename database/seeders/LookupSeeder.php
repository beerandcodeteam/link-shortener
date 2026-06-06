<?php

namespace Database\Seeders;

use App\Models\Browser;
use App\Models\DeviceType;
use App\Models\LinkStatus;
use Illuminate\Database\Seeder;

class LookupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Idempotent: uses updateOrCreate keyed on the unique slug so re-running
     * never produces duplicates.
     */
    public function run(): void
    {
        $this->seedLinkStatuses();
        $this->seedDeviceTypes();
        $this->seedBrowsers();
    }

    protected function seedLinkStatuses(): void
    {
        $statuses = [
            ['name' => 'Active', 'slug' => 'active', 'description' => 'Link is active and redirects.', 'is_active' => true],
            ['name' => 'Disabled', 'slug' => 'disabled', 'description' => 'Link is disabled and does not redirect.', 'is_active' => false],
        ];

        foreach ($statuses as $status) {
            LinkStatus::updateOrCreate(['slug' => $status['slug']], $status);
        }
    }

    protected function seedDeviceTypes(): void
    {
        $devices = ['Desktop', 'Mobile', 'Tablet', 'Bot', 'Unknown'];

        foreach ($devices as $name) {
            DeviceType::updateOrCreate(['slug' => str($name)->slug()->value()], ['name' => $name]);
        }
    }

    protected function seedBrowsers(): void
    {
        $browsers = ['Chrome', 'Firefox', 'Safari', 'Edge', 'Other'];

        foreach ($browsers as $name) {
            Browser::updateOrCreate(['slug' => str($name)->slug()->value()], ['name' => $name]);
        }
    }
}
