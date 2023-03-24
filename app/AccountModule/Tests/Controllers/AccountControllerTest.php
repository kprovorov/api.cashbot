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
    public function it_successfully_lists_user_accounts(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Collection $accounts */
        $accounts = Account::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $res = $this->actingAs($user)->getJson('accounts');

        $res->assertSuccessful();
        $res->assertJson($accounts->sortByDesc('id')->values()->toArray());
    }

    /**
     * @test
     */
    public function it_doesnt_lists_foreign_accounts(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Collection $foreignAccounts */
        $foreignAccounts = Account::factory()->count(3)->create([
            'user_id' => User::factory(),
        ]);

        $res = $this->actingAs($user)->getJson('accounts');

        $res->assertSuccessful();
        $res->assertExactJson([]);
    }

    /**
     * @test
     */
    public function it_successfully_shows_account(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        $res = $this->actingAs($user)->getJson("accounts/{$account->id}");

        $res->assertSuccessful();
        $res->assertJson($account->toArray());
    }

    /**
     * @test
     */
    public function it_doesnt_shows_foreign_account(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Account $foreignAccount */
        $foreignAccount = Account::factory()->create([
            'user_id' => User::factory(),
        ]);

        $res = $this->actingAs($user)->getJson("accounts/{$foreignAccount->id}");

        $res->assertForbidden();
    }

    /**
     * @test
     */
    public function it_successfully_creates_account(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Account $accountData */
        $accountData = Account::factory()->make();

        $res = $this->actingAs($user)->postJson('accounts', $accountData->toArray());

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

        /** @var Account $parent */
        $parent = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $accountData */
        $accountData = Account::factory()->make([
            'parent_id' => $parent->id,
        ]);

        $res = $this->actingAs($user)->postJson('accounts', $accountData->toArray());

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
    public function it_doesnt_creates_account_with_foreign_parent_id(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Account $parent */
        $parent = Account::factory()->create([
            'user_id' => User::factory(),
        ]);

        /** @var Account $accountData */
        $accountData = Account::factory()->make([
            'parent_id' => $parent->id,
        ]);

        $res = $this->actingAs($user)->postJson('accounts', $accountData->toArray());

        $res->assertUnprocessable();
        $this->assertDatabaseMissing('accounts', [
            'user_id' => $user->id,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_updates_account(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $accountData */
        $accountData = Account::factory()->make();

        $res = $this->actingAs($user)->putJson("accounts/{$account->id}", $accountData->toArray());

        $res->assertSuccessful();
        $res->assertJson($accountData->toArray());
        $this->assertDatabaseHas('accounts', $accountData->toArray());
    }

    /**
     * @test
     */
    public function it_doesnt_updates_foreign_account(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Account $foreignAccount */
        $foreignAccount = Account::factory()->create([
            'user_id' => User::factory(),
        ]);

        /** @var Account $accountData */
        $accountData = Account::factory()->make();

        $payload = $accountData->toArray();

        $res = $this->actingAs($user)->putJson("accounts/{$foreignAccount->id}", $payload);

        $res->assertForbidden();
        $this->assertDatabaseHas('accounts', $foreignAccount->toArray());
    }

    /**
     * @test
     */
    public function it_doesnt_updates_account_provider_data(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Account $account */
        $account = Account::factory()->withProvider()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $accountData */
        $accountData = Account::factory()->make();

        $res = $this->actingAs($user)->putJson("accounts/{$account->id}", $accountData->toArray());

        $res->assertSuccessful();
        $res->assertJson($accountData->toArray());
        $this->assertDatabaseHas('accounts', [
            ...$accountData->toArray(),
            'provider_id' => $account->provider_id,
            'provider' => $account->provider,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_updates_account_with_parent_id(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

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

        $res = $this->actingAs($user)->putJson("accounts/{$account->id}", $accountData->toArray());

        $res->assertSuccessful();
        $res->assertJson($accountData->toArray());
        $this->assertDatabaseHas('accounts', $accountData->toArray());
    }

    /**
     * @test
     */
    public function it_doesnt_updates_account_with_own_parent_id(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $accountData */
        $accountData = Account::factory()->make([
            'parent_id' => $account->id,
        ]);

        $res = $this->actingAs($user)->putJson("accounts/{$account->id}", $accountData->toArray());

        $res->assertUnprocessable();
        $this->assertDatabaseHas('accounts', $account->toArray());
    }

    /**
     * @test
     */
    public function it_doesnt_updates_account_with_foreign_parent_id(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $foreignAccount */
        $foreignAccount = Account::factory()->create([
            'user_id' => User::factory(),
        ]);

        /** @var Account $accountData */
        $accountData = Account::factory()->make([
            'parent_id' => $foreignAccount->id,
        ]);

        $res = $this->actingAs($user)->putJson("accounts/{$account->id}", $accountData->toArray());

        $res->assertUnprocessable();
        $this->assertDatabaseHas('accounts', $account->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_deletes_account(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        $res = $this->actingAs($user)->deleteJson("accounts/{$account->id}");

        $res->assertSuccessful();
        $this->assertDatabaseMissing('accounts', [
            'id' => $account->id,
        ]);
    }

    /**
     * @test
     */
    public function it_doesnt_deletes_foreign_account(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Account $foreignAccount */
        $foreignAccount = Account::factory()->create([
            'user_id' => User::factory(),
        ]);

        $res = $this->actingAs($user)->deleteJson("accounts/{$foreignAccount->id}");

        $res->assertForbidden();
        $this->assertDatabaseHas('accounts', [
            'id' => $foreignAccount->id,
        ]);
    }
}
