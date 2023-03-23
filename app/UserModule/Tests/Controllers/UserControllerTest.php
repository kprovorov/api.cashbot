<?php

namespace App\UserModule\Tests\Controllers;

use App\UserModule\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_lists_users_as_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        /** @var Collection $users */
        $users = User::factory()->count(3)->create();

        $res = $this->get('users');

        $res->assertSuccessful();
        $res->assertJson($users->sortByDesc('id')->values()->toArray());
    }

    /**
     * @test
     */
    public function it_doesnt_lists_users_as_non_admin(): void
    {
        $customer = User::factory()->create();
        $this->actingAs($customer);

        /** @var Collection $users */
        $users = User::factory()->count(3)->create();

        $res = $this->get('users');

        $res->assertUnauthorized();
    }

    /**
     * @test
     */
    public function it_successfully_shows_user(): void
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var User $user */
        $user = User::factory()->create();

        $res = $this->get("users/{$user->id}");

        $res->assertSuccessful();
        $res->assertJson($user->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_creates_user_as_admin(): void
    {
        $admin = User::factory()->admin()->create();
        $this->actingAs($admin);

        /** @var User $userData */
        $userData = User::factory()->make();

        $res = $this->post('users', $userData->toArray());

        $res->assertCreated();
        $res->assertJson($userData->toArray());
        $this->assertDatabaseHas('users', $userData->toArray());
    }

    /**
     * @test
     */
    public function it_doesnt_creates_user_as_non_admin(): void
    {
        $customer = User::factory()->create();
        $this->actingAs($customer);

        /** @var User $userData */
        $userData = User::factory()->make();

        $res = $this->post('users', $userData->toArray());

        $res->assertUnauthorized();
        $this->assertDatabaseMissing('users', [
            'email' => $userData->email,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_updates_user(): void
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var User $user */
        $user = User::factory()->create();

        /** @var User $userData */
        $userData = User::factory()->make();

        $payload = [
            ...$userData->toArray(),
            'password' => 'secret',
        ];

        $res = $this->put("users/{$user->id}", $payload);

        $res->assertSuccessful();
        $res->assertJson($userData->toArray());
        $this->assertDatabaseHas('users', $payload);
    }

    /**
     * @test
     */
    public function it_successfully_deletes_user(): void
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var User $user */
        $user = User::factory()->create();

        $res = $this->delete("users/{$user->id}");

        $res->assertSuccessful();
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
