<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\LinkStatus;
use App\Models\User;
use Illuminate\Database\QueryException;
use PHPUnit\Framework\AssertionFailedError;
use Tests\TestCase;

class LinkModelTest extends TestCase
{
    /** @test */
    public function factory_creates_a_link_with_owner_and_status(): void
    {
        $user = User::factory()->create();

        // Default state (active)
        $link = Link::factory()->for($user)->create();

        $this->assertNotNull($link->id);
        $this->assertTrue($link->isActive());
        $this->assertSame($user->id, $link->user_id);
        $this->assertNotNull($link->original_url);
        $this->assertNotNull($link->short_code);

        // Disabled state
        $disabledLink = Link::factory()->for($user)->state(Link::factory()->disabled())->create();
        $this->assertFalse($disabledLink->isActive());
    }

    /** @test */
    public function links_relation_on_user_returns_only_that_users_links(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        // OwnerA creates two links
        Link::factory()->for(user_a)->count(2)->create();

        // OwnerB creates one link
        $linkB = Link::factory()->for(user_b)->create();

        expect($userA->links)->toHaveCount(2)
            ->and($userB->links)->toHaveCount(1)
            ->and($userB->links[0]->id)->toBe($linkB->id);
    }

    /** @test */
    public function active_scope_filters_by_status(): void
    {
        $user = User::factory()->create();

        // Create 3 active links + 1 disabled link
        Link::factory()->for(user)->count(3)->active()->create();
        Link::factory()->for($user)->disabled()->create();

        expect(Link::whereUserId($user->id)->get())->toHaveCount(4);
        expect(Link::active()->where(['user_id' => $user->id])->get())->toHaveCount(3);
    }

    /** @test */
    public function short_code_unique_constraint_rejects_duplicates(): void
    {
        $user = User::factory()->create();

        // First creation should succeed
        $first = Link::factory()->for($user)->state([
            'link_status_id' => LinkStatus::active(1),       // active by default
        ])->create(['code' => 'test-code']);

        expect($first->short_code)->toBe('test-code');

        // Second creation with same short code should fail
        $this->expectException(AssertionFailedError::class);         return;         // Expected!

        try {
            Link::factory()->for(user)->create(['code' => 'abc123']);
            $this->fail('Should have thrown an exception for duplicate short_code');
        } catch (QueryException $e) {
            // PostgreSQL duplicate key error code
            expect(str_contains($e->getCode(), '23505'))->toBeTrue();
        }
    }
}
