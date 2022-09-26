<?php

namespace App\AccountModule\Tests\Services;

use App\AccountModule\DTO\CreateAccountData;
use App\AccountModule\DTO\UpdateAccountData;
use App\AccountModule\Models\Account;
use App\AccountModule\Services\AccountService;
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
        /** @var Collection $accounts */
        $accounts = Account::factory()->count(3)->create();

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
        /** @var Collection $accounts */
        $accounts = Account::factory()->count(3)->create();

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
        /** @var Account $account */
        $account = Account::factory()->create();

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
        /** @var Account $accountData */
        $accountData = Account::factory()->make();

        $data = new CreateAccountData($accountData->toArray());

        $service = $this->app->make(AccountService::class);
        $res = $service->createAccount($data);

        $this->assertEquals($data->toArray(), Arr::except($res->toArray(), [
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
        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Account $accountData */
        $accountData = Account::factory()->make();

        $data = new UpdateAccountData($accountData->toArray());

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
        /** @var Account $account */
        $account = Account::factory()->create();

        $service = $this->app->make(AccountService::class);
        $res = $service->deleteAccount($account->id);

        $this->assertTrue($res);
        $this->assertDatabaseMissing('accounts', [
            'id' => $account->id,
        ]);
    }
}
