<?php

namespace App\PaymentModule\Tests\Services;

use App\PaymentModule\DTO\CreateGroupData;
use App\PaymentModule\DTO\UpdateGroupData;
use App\PaymentModule\Models\Group;
use App\PaymentModule\Services\GroupService;
use Arr;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Spatie\DataTransferObject\Exceptions\ValidationException;
use Tests\TestCase;

class GroupServiceTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_gets_all_groups(): void
    {
        /** @var Collection $groups */
        $groups = Group::factory()->count(3)->create();

        $service = $this->app->make(GroupService::class);
        $res = $service->getAllGroups();

        $this->assertCount(3, $res);
        $groups->each(fn (Group $group) => $this->assertContains(
            $group->id,
            $res->pluck('id')
        ));
    }

    /**
     * @test
     */
    public function it_successfully_gets_all_groups_paginated(): void
    {
        /** @var Collection $groups */
        $groups = Group::factory()->count(3)->create();

        $service = $this->app->make(GroupService::class);
        $res = $service->getAllGroupsPaginated();

        $this->assertCount(3, $res);
        $groups->each(fn (Group $group) => $this->assertContains(
            $group->id,
            $res->pluck('id')
        ));
    }

    /**
     * @test
     */
    public function it_successfully_gets_group(): void
    {
        /** @var Group $group */
        $group = Group::factory()->create();

        $service = $this->app->make(GroupService::class);
        $res = $service->getGroup($group->id);

        $this->assertEquals($group->refresh()->toArray(), $res->toArray());
    }

    /**
     * @test
     * @throws UnknownProperties
     * @throws ValidationException
     */
    public function it_successfully_creates_group(): void
    {
        /** @var Group $groupData */
        $groupData = Group::factory()->make();

        $data = new CreateGroupData($groupData->toArray());

        $service = $this->app->make(GroupService::class);
        $res = $service->createGroup($data);

        $this->assertEquals($data->toArray(), Arr::except($res->toArray(), [
            'id',
            'created_at',
            'updated_at',
        ]));
        $this->assertDatabaseHas('groups', $data->toArray());
    }

    /**
     * @test
     * @throws UnknownProperties
     * @throws ValidationException
     */
    public function it_successfully_updates_group(): void
    {
        /** @var Group $group */
        $group = Group::factory()->create();

        /** @var Group $groupData */
        $groupData = Group::factory()->make();

        $data = new UpdateGroupData($groupData->toArray());

        $service = $this->app->make(GroupService::class);
        $res = $service->updateGroup($group->id, $data);

        $this->assertTrue($res);
        $this->assertDatabaseHas('groups', $data->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_deletes_group(): void
    {
        /** @var Group $group */
        $group = Group::factory()->create();

        $service = $this->app->make(GroupService::class);
        $res = $service->deleteGroup($group->id);

        $this->assertTrue($res);
        $this->assertDatabaseMissing('groups', [
            'id' => $group->id,
        ]);
    }
}
