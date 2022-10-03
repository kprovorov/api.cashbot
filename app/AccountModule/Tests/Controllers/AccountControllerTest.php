<?php

namespace App\AccountModule\Tests\Controllers;

use App\AccountModule\Models\Account;
use App\Services\CurrencyConverter;
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
        $accounts = Account::factory()->count(3)->create();

        $this->mock(CurrencyConverter::class)
             ->shouldReceive('getRate')
             ->times(3)
             ->andReturn(2);

        $res = $this->get('api/accounts');

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
        $account = Account::factory()->create();

        $res = $this->get("api/accounts/{$account->id}");

        $res->assertSuccessful();
        $res->assertJson($account->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_creates_account(): void
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $accountData */
        $accountData = Account::factory()->make();

        $payload = $accountData->toArray();

        $res = $this->post('api/accounts', $payload);

        $res->assertCreated();
        $res->assertJson($payload);
        $this->assertDatabaseHas('accounts', $payload);
    }

    /**
     * @test
     */
    public function it_successfully_updates_account(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Account $accountData */
        $accountData = Account::factory()->make();

        $payload = $accountData->toArray();

        $res = $this->put("api/accounts/{$account->id}", $payload);

        $res->assertSuccessful();
        $res->assertJson($payload);
        $this->assertDatabaseHas('accounts', $payload);
    }

    /**
     * @test
     */
    public function it_successfully_deletes_account(): void
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create();

        $res = $this->delete("api/accounts/{$account->id}");

        $res->assertSuccessful();
        $this->assertDatabaseMissing('accounts', [
            'id' => $account->id,
        ]);
    }
}
