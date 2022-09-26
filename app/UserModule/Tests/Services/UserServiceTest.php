<?php

namespace App\UserModule\Tests\Services;

use App\UserModule\DTO\CreateUserData;
use App\UserModule\DTO\UpdateUserData;
use App\UserModule\Models\User;
use App\UserModule\Services\UserService;
use Arr;
use Hash;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_gets_all_users(): void
    {
        /** @var Collection $users */
        $users = User::factory()->count(3)->create();

        $service = $this->app->make(UserService::class);
        $res = $service->getAllUsers();

        $this->assertCount(3, $res);
        $users->each(fn (User $user) => $this->assertContains(
            $user->id,
            $res->pluck('id')
        ));
    }

    /**
     * @test
     */
    public function it_successfully_gets_all_users_paginated(): void
    {
        /** @var Collection $users */
        $users = User::factory()->count(3)->create();

        $service = $this->app->make(UserService::class);
        $res = $service->getAllUsersPaginated();

        $this->assertCount(3, $res);
        $users->each(fn (User $user) => $this->assertContains(
            $user->id,
            $res->pluck('id')
        ));
    }

    /**
     * @test
     */
    public function it_successfully_gets_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $service = $this->app->make(UserService::class);
        $res = $service->getUser($user->id);

        $this->assertEquals($user->refresh()->toArray(), $res->toArray());
    }

    /**
     * @test
     *
     * @throws UnknownProperties
     */
    public function it_successfully_creates_user(): void
    {
        /** @var User $userData */
        $userData = User::factory()->make();

        $data = new CreateUserData([
            ...$userData->toArray(),
            'password' => Hash::make('secret'),
        ]);

        $service = $this->app->make(UserService::class);
        $res = $service->createUser($data);

        $this->assertEquals(
            $data->except('password')->toArray(),
            Arr::except($res->toArray(), [
                'id',
                'created_at',
                'updated_at',
            ])
        );
        $this->assertDatabaseHas('users', $data->toArray());
    }

    /**
     * @test
     *
     * @throws UnknownProperties
     */
    public function it_successfully_updates_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var User $userData */
        $userData = User::factory()->make();

        $data = new UpdateUserData([
            ...$userData->toArray(),
            'password' => Hash::make('secret'),
        ]);

        $service = $this->app->make(UserService::class);
        $res = $service->updateUser($user->id, $data);

        $this->assertTrue($res);
        $this->assertDatabaseHas('users', $data->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_deletes_user(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $service = $this->app->make(UserService::class);
        $res = $service->deleteUser($user->id);

        $this->assertTrue($res);
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
