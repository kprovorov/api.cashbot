<?php

namespace App\AccountModule\Tests\Services;

use App\AccountModule\DTO\CreateAccountData;
use App\AccountModule\DTO\UpdateAccountData;
use App\AccountModule\Models\Account;
use App\AccountModule\Services\AccountService;
use App\PaymentModule\Models\Payment;
use App\UserModule\Models\User;
use Arr;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Tests\TestCase;

class AccountServiceTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_gets_all_accounts(): void
    {
        $user = User::factory()->create();

        /** @var Collection $accounts */
        $accounts = Account::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $service = $this->app->make(AccountService::class);
        $res = $service->getAllAccounts();

        $this->assertCount(3, $res);
        $accounts->each(fn (Account $account) => $this->assertContains(
            $account->id,
            $res->pluck('id')
        ));
    }

    /**
     * @test
     */
    public function it_successfully_gets_all_accounts_paginated(): void
    {
        $user = User::factory()->create();

        /** @var Collection $accounts */
        $accounts = Account::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        $service = $this->app->make(AccountService::class);
        $res = $service->getAllAccountsPaginated();

        $this->assertCount(3, $res);
        $accounts->each(fn (Account $account) => $this->assertContains(
            $account->id,
            $res->pluck('id')
        ));
    }

    /**
     * @test
     */
    public function it_successfully_gets_account(): void
    {
        $user = User::factory()->create();

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        $service = $this->app->make(AccountService::class);
        $res = $service->getAccount($account->id);

        $this->assertEquals($account->refresh()->toArray(), $res->toArray());
    }

    /**
     * @test
     *
     * @throws UnknownProperties
     */
    public function it_successfully_creates_account(): void
    {
        $user = User::factory()->create();

        /** @var Account $accountData */
        $accountData = Account::factory()->make([
            'user_id' => $user->id,
        ]);

        $data = new CreateAccountData([
            ...$accountData->toArray(),
            'currency' => $accountData->currency,
        ]);

        $service = $this->app->make(AccountService::class);
        $res = $service->createAccount($data);

        $this->assertEquals([
            ...$data->toArray(),
            'currency' => $data->currency->value,
        ], Arr::except($res->toArray(), [
            'id',
            'created_at',
            'updated_at',
        ]));
        $this->assertDatabaseHas('accounts', $data->toArray());
    }

    /**
     * @test
     *
     * @throws UnknownProperties
     */
    public function it_successfully_updates_account(): void
    {
        $user = User::factory()->create();

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $accountData */
        $accountData = Account::factory()->make();

        $data = new UpdateAccountData([
            ...$accountData->toArray(),
            'currency' => $accountData->currency,
        ]);

        $service = $this->app->make(AccountService::class);
        $res = $service->updateAccount($account->id, $data);

        $this->assertTrue($res);
        $this->assertDatabaseHas('accounts', $data->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_deletes_account(): void
    {
        $user = User::factory()->create();

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        // Create test payments
        Payment::factory()->create([
            'account_from_id' => $account->id,
        ]);
        Payment::factory()->create([
            'account_to_id' => $account->id,
        ]);

        // Create test jars
        $jar = Account::factory()->create([
            'user_id' => $user->id,
            'parent_id' => $account->id,
        ]);
        Payment::factory()->create([
            'account_from_id' => $jar->id,
        ]);
        Payment::factory()->create([
            'account_to_id' => $jar->id,
        ]);

        $service = $this->app->make(AccountService::class);
        $res = $service->deleteAccount($account->id);

        $this->assertTrue($res);
        $this->assertDatabaseMissing('accounts', [
            'id' => $account->id,
        ]);
        $this->assertDatabaseMissing('accounts', [
            'parent_id' => $account->id,
        ]);
        $this->assertDatabaseMissing('payments', [
            'account_from_id' => $account->id,
        ]);
        $this->assertDatabaseMissing('payments', [
            'account_to_id' => $account->id,
        ]);
        $this->assertDatabaseMissing('payments', [
            'account_from_id' => $jar->id,
        ]);
        $this->assertDatabaseMissing('payments', [
            'account_to_id' => $jar->id,
        ]);
    }
}
