<?php

namespace App\AccountModule\Tests\Controllers;

use App\AccountModule\Models\Account;
use App\UserModule\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class AccountControllerTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_lists_accounts(): void
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Collection $accounts */
        $accounts = Account::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $res = $this->get('accounts');

        $res->assertSuccessful();
        $res->assertJson($accounts->sortByDesc('id')->values()->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_shows_account(): void
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        $res = $this->get("accounts/{$account->id}");

        $res->assertSuccessful();
        $res->assertJson($account->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_creates_account(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $accountData */
        $accountData = Account::factory()->make();

        $res = $this->post('accounts', $accountData->toArray());

        $res->assertCreated();
        $res->assertJson([
            ...$accountData->toArray(),
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('accounts', [
            ...$accountData->toArray(),
            'user_id' => $user->id,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_creates_account_with_parent_id(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $parent */
        $parent = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $accountData */
        $accountData = Account::factory()->make([
            'parent_id' => $parent->id,
        ]);

        $res = $this->post('accounts', $accountData->toArray());

        $res->assertCreated();
        $res->assertJson([
            ...$accountData->toArray(),
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('accounts', [
            ...$accountData->toArray(),
            'user_id' => $user->id,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_updates_account(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $accountData */
        $accountData = Account::factory()->make();

        $payload = $accountData->toArray();

        $res = $this->put("accounts/{$account->id}", $payload);

        $res->assertSuccessful();
        $res->assertJson($payload);
        $this->assertDatabaseHas('accounts', $payload);
    }

    /**
     * @test
     */
    public function it_successfully_updates_account_with_parent_id(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $parent */
        $parent = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $accountData */
        $accountData = Account::factory()->make([
            'parent_id' => $parent->id,
        ]);

        $payload = $accountData->toArray();

        $res = $this->put("accounts/{$account->id}", $payload);

        $res->assertSuccessful();
        $res->assertJson($payload);
        $this->assertDatabaseHas('accounts', $payload);
    }

    /**
     * @test
     */
    public function it_doesnt_updates_account_with_same_parent_id_as_own(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $accountData */
        $accountData = Account::factory()->make([
            'parent_id' => $account->id,
        ]);

        $payload = $accountData->toArray();

        $res = $this->put("accounts/{$account->id}", $payload);

        $res->assertInvalid();
        $this->assertDatabaseHas('accounts', $account->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_deletes_account(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        $res = $this->delete("accounts/{$account->id}");

        $res->assertSuccessful();
        $this->assertDatabaseMissing('accounts', [
            'id' => $account->id,
        ]);
    }
}
