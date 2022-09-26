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
    public function it_successfully_lists_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Collection $users */
        $users = User::factory()->count(3)->create();

        $res = $this->get("api/users");

        $res->assertSuccessful();
        $res->assertJson($users->sortByDesc('id')->values()->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_shows_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var User $user */
        $user = User::factory()->create();

        $res = $this->get("api/users/{$user->id}");

        $res->assertSuccessful();
        $res->assertJson($user->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_creates_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var User $userData */
        $userData = User::factory()->make();

        $payload = [
            ...$userData->toArray(),
            'password' => 'secret',
        ];

        $res = $this->post("api/users", $payload);

        $res->assertCreated();
        $res->assertJson($userData->toArray());
        $this->assertDatabaseHas('users', $payload);
    }

    /**
     * @test
     */
    public function it_successfully_updates_user(): void
    {
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

        $res = $this->put("api/users/{$user->id}", $payload);

        $res->assertSuccessful();
        $res->assertJson($userData->toArray());
        $this->assertDatabaseHas('users', $payload);
    }

    /**
     * @test
     */
    public function it_successfully_deletes_user(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var User $user */
        $user = User::factory()->create();

        $res = $this->delete("api/users/{$user->id}");

        $res->assertSuccessful();
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }
}
