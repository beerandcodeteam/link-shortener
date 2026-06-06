<?php

namespace Database\Seeders;

use App\Models\LinkStatus;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Link statuses must exist before links/factories are created.
        $this->createLinkStatuses();

        User::factory(10)->create();
    }

    /**
     * Ensure default link_statuses exist (active, disabled).
     */
    private function createLinkStatuses(): void
    {
        $statuses = [
            ['slug' => 'active', 'name' => 'Active', 'description' => 'The link is fully functional.', 'is_active' => true],
            ['slug' => 'disabled', 'name' => 'Disabled', 'description' => 'The link has been disabled by an admin.', 'is_active' => false],
        ];

        foreach ($statuses as $s) {
            LinkStatus::firstOrCreate(
                ['slug' => $s['slug']],
                $s,
            );
        }
    }
}
