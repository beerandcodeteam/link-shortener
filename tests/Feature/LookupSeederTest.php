<?php

namespace Tests\Feature;

use App\Models\Browser;
use App\Models\DeviceType;
use App\Models\LinkStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LookupSeederTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that lookups are seeded correctly and are idempotent.
     */
    public function test_lookup_data_is_seeded_correctly_and_remains_idempotent(): void
    {
        // First run
        $this->seed();

        $this->assertDatabaseHas('link_statuses', ['slug' => 'active']);
        $this->assertDatabaseHas('link_statuses', ['slug' => 'disabled']);

        $this->assertDatabaseHas('device_types', ['slug' => 'desktop']);
        $this->assertDatabaseHas('device_types', ['slug' => 'mobile']);
        $this->assertDatabaseHas('device_types', ['slug' => 'tablet']);
        $this->assertDatabaseHas('device_types', ['slug' => 'bot']);
        $this->assertDatabaseHas('device_types', ['slug' => 'unknown']);

        $this->assertDatabaseHas('browsers', ['slug' => 'chrome']);
        $this->assertDatabaseHas('browsers', ['slug' => 'firefox']);
        $this->assertDatabaseHas('browsers', ['slug' => 'safari']);
        $this->assertDatabaseHas('browsers', ['slug' => 'edge']);
        $this->assertDatabaseHas('browsers', ['slug' => 'other']);

        // Run again to test idempotency
        $this->seed();

        $this->assertEquals(2, \App\Models\LinkStatus::count());
        $this->assertEquals(5, \App\Models\DeviceType::count());
        $this->assertEquals(5, \App\Models\Browser::count());
    }

    public function seed(): void
    {
        $this->artisan('db:seed');
    }
}
