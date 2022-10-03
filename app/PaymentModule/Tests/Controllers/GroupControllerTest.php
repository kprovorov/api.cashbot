<?php

namespace App\PaymentModule\Tests\Controllers;

use App\PaymentModule\Models\Group;
use App\UserModule\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_lists_groups(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Collection $groups */
        $groups = Group::factory()->count(3)->create();

        $res = $this->get("api/groups");

        $res->assertSuccessful();
        $res->assertJson($groups->sortByDesc('id')->values()->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_shows_group(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Group $group */
        $group = Group::factory()->create();

        $res = $this->get("api/groups/{$group->id}");

        $res->assertSuccessful();
        $res->assertJson($group->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_creates_group(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Group $groupData */
        $groupData = Group::factory()->make();

        $payload = $groupData->toArray();

        $res = $this->post("api/groups", $payload);

        $res->assertCreated();
        $res->assertJson($payload);
        $this->assertDatabaseHas('groups', $payload);
    }

    /**
     * @test
     */
    public function it_successfully_updates_group(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Group $group */
        $group = Group::factory()->create();

        /** @var Group $groupData */
        $groupData = Group::factory()->make();

        $payload = $groupData->toArray();

        $res = $this->put("api/groups/{$group->id}", $payload);

        $res->assertSuccessful();
        $res->assertJson($payload);
        $this->assertDatabaseHas('groups', $payload);
    }

    /**
     * @test
     */
    public function it_successfully_deletes_group(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Group $group */
        $group = Group::factory()->create();

        $res = $this->delete("api/groups/{$group->id}");

        $res->assertSuccessful();
        $this->assertDatabaseMissing('groups', [
            'id' => $group->id,
        ]);
    }
}
