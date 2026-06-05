<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->seedLookups();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Seed lookup tables for various systems.
     */
    private function seedLookups(): void
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
