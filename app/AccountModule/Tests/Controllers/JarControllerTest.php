<?php

namespace App\AccountModule\Tests\Controllers;

use App\AccountModule\Models\Account;
use App\AccountModule\Models\Jar;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class JarControllerTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_lists_jars(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Collection $jars */
        $jars = Jar::factory()->count(3)->create([
            'account_id' => $account->id,
        ]);

        $res = $this->get("api/jars");

        $res->assertSuccessful();
        $res->assertJson($jars->sortByDesc('id')->values()->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_shows_jar(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        $res = $this->get("api/jars/{$jar->id}");

        $res->assertSuccessful();
        $res->assertJson($jar->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_creates_jar(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Jar $jarData */
        $jarData = Jar::factory()->make([
            'account_id' => $account->id,
        ]);

        $payload = $jarData->toArray();

        $res = $this->post("api/jars", $payload);

        $res->assertCreated();
        $res->assertJson($payload);
        $this->assertDatabaseHas('jars', $payload);
    }

    /**
     * @test
     */
    public function it_successfully_updates_jar(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        /** @var Jar $jarData */
        $jarData = Jar::factory()->make([
            'account_id' => $account->id,
        ]);

        $payload = $jarData->toArray();

        $res = $this->put("api/jars/{$jar->id}", $payload);

        $res->assertSuccessful();
        $res->assertJson($payload);
        $this->assertDatabaseHas('jars', $payload);
    }

    /**
     * @test
     */
    public function it_successfully_deletes_jar(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        $res = $this->delete("api/jars/{$jar->id}");

        $res->assertSuccessful();
        $this->assertDatabaseMissing('jars', [
            'id' => $jar->id,
        ]);
    }
}
