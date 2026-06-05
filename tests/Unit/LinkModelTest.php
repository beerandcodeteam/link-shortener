<?php

namespace Tests\Unit;

use App\Models\Link;
use App\Models\LinkStatus;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Database\Eloquent\Model;

class LinkModelTest extends \Tests\TestCase
{
    use RefreshDatabase;

    /**
     * Test that the factory creates a link with owner and status.
     */
    public function test_factory_creates_link_with_owner_and_status(): void
    {
        $link = Link::factory()->create();

        $this->assertNotNull($link->user_id);
        $this->assertNotNull($link->link_status_id);
        $this->assertNotNull($link->original_url);
        $this->assertNotNull($link->short_code);
    }

    /**
     * Test that links() relation on User returns only that user's links.
     */
    public function test_user_links_relation_returns_only_user_links(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();

        Link::factory()->count(2)->forUser($user1)->create();
        Link::factory()->count(2)->forUser($user2)->create();

        $this->assertCount(2, $user1->links);
        $this->assertCount(2, $user2->links);
    }

    /**
     * Test that active() scope filters by status.
     */
    public function test_active_scope_filters_by_status(): void
    {
        $activeStatus = LinkStatus::factory()->create(['is_active' => true]);
        $inactiveStatus = LinkStatus::factory()->create(['is_active' => false]);

        Link::factory()->create(['link_status_id' => $activeStatus->id]);
        Link::factory()->create(['link_status_id' => $inactiveStatus->id]);

        $this->assertCount(1, Link::active()->get());
    }

    /**
     * Test that short_code unique constraint rejects duplicates.
    */
    public function test_short_code_unique_constraint_rejects_duplicates(): void
    {
        Link::factory()->create(['short_code' => 'duplicate']);

        $this->expectException(\Illuminate\Database\QueryException::class);

        Link::factory()->create(['short_code' => 'duplicate']);
    }
}
