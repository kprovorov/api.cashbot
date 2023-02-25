<?php

namespace App\TransferModule\Tests\Services;

use App\AccountModule\Models\Account;
use App\PaymentModule\Models\Payment;
use App\TransferModule\DTO\CreateTransferData;
use App\TransferModule\DTO\UpdateTransferData;
use App\TransferModule\Models\Transfer;
use App\TransferModule\Services\TransferService;
use App\UserModule\Models\User;
use Arr;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Tests\TestCase;

class TransferServiceTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_gets_all_transfers(): void
    {
        $user = User::factory()->create();

        /** @var Account $accountFrom */
        $accountFrom = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $accountTo */
        $accountTo = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Payment $paymentFrom */
        $paymentFrom = Payment::factory()->create([
            'account_id' => $accountFrom->id,
        ]);

        /** @var Payment $paymentTo */
        $paymentTo = Payment::factory()->create([
            'account_id' => $accountTo->id,
        ]);

        /** @var Collection $transfers */
        $transfers = Transfer::factory()->count(3)->create([
            'from_payment_id' => $paymentFrom->id,
            'to_payment_id' => $paymentTo->id,
        ]);

        $service = $this->app->make(TransferService::class);
        $res = $service->getAllTransfers();

        $this->assertCount(3, $res);
        $transfers->each(fn (Transfer $transfer) => $this->assertContains(
            $transfer->id,
            $res->pluck('id')
        ));
    }

    /**
     * @test
     */
    public function it_successfully_gets_all_transfers_paginated(): void
    {
        $user = User::factory()->create();

        /** @var Account $accountFrom */
        $accountFrom = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $accountTo */
        $accountTo = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Payment $paymentFrom */
        $paymentFrom = Payment::factory()->create([
            'account_id' => $accountFrom->id,
        ]);

        /** @var Payment $paymentTo */
        $paymentTo = Payment::factory()->create([
            'account_id' => $accountTo->id,
        ]);

        /** @var Collection $transfers */
        $transfers = Transfer::factory()->count(3)->create([
            'from_payment_id' => $paymentFrom->id,
            'to_payment_id' => $paymentTo->id,
        ]);

        $service = $this->app->make(TransferService::class);
        $res = $service->getAllTransfersPaginated();

        $this->assertCount(3, $res);
        $transfers->each(fn (Transfer $transfer) => $this->assertContains(
            $transfer->id,
            $res->pluck('id')
        ));
    }

    /**
     * @test
     */
    public function it_successfully_gets_transfer(): void
    {
        $user = User::factory()->create();

        /** @var Account $accountFrom */
        $accountFrom = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $accountTo */
        $accountTo = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Payment $paymentFrom */
        $paymentFrom = Payment::factory()->create([
            'account_id' => $accountFrom->id,
        ]);

        /** @var Payment $paymentTo */
        $paymentTo = Payment::factory()->create([
            'account_id' => $accountTo->id,
        ]);

        /** @var Transfer $transfer */
        $transfer = Transfer::factory()->create([
            'from_payment_id' => $paymentFrom->id,
            'to_payment_id' => $paymentTo->id,
        ]);

        $service = $this->app->make(TransferService::class);
        $res = $service->getTransfer($transfer->id);

        $this->assertEquals($transfer->refresh()->toArray(), $res->toArray());
    }

    /**
     * @test
     *
     * @throws UnknownProperties
     */
    public function it_successfully_creates_transfer(): void
    {
        $user = User::factory()->create();

        /** @var Account $accountFrom */
        $accountFrom = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $accountTo */
        $accountTo = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Payment $paymentFrom */
        $paymentFrom = Payment::factory()->create([
            'account_id' => $accountFrom->id,
        ]);

        /** @var Payment $paymentTo */
        $paymentTo = Payment::factory()->create([
            'account_id' => $accountTo->id,
        ]);

        /** @var Transfer $transferData */
        $transferData = Transfer::factory()->make([
            'from_payment_id' => $paymentFrom->id,
            'to_payment_id' => $paymentTo->id,
        ]);

        $data = new CreateTransferData($transferData->toArray());

        $service = $this->app->make(TransferService::class);
        $res = $service->createTransfer($data);

        $this->assertEquals(
            $data->toArray(),
            Arr::except($res->toArray(), [
                'id',
                'created_at',
                'updated_at',
            ])
        );
        $this->assertDatabaseHas('transfers', $data->toArray());
    }

    /**
     * @test
     *
     * @throws UnknownProperties
     */
    public function it_successfully_updates_transfer(): void
    {
        $user = User::factory()->create();

        /** @var Account $accountFrom */
        $accountFrom = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $accountTo */
        $accountTo = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Payment $paymentFrom */
        $paymentFrom = Payment::factory()->create([
            'account_id' => $accountFrom->id,
        ]);

        /** @var Payment $paymentTo */
        $paymentTo = Payment::factory()->create([
            'account_id' => $accountTo->id,
        ]);

        /** @var Transfer $transfer */
        $transfer = Transfer::factory()->create([
            'from_payment_id' => $paymentFrom->id,
            'to_payment_id' => $paymentTo->id,
        ]);

        /** @var Transfer $transferData */
        $transferData = Transfer::factory()->make([
            'from_payment_id' => $paymentTo->id,
            'to_payment_id' => $paymentFrom->id,
        ]);

        $data = new UpdateTransferData($transferData->toArray());

        $service = $this->app->make(TransferService::class);
        $res = $service->updateTransfer($transfer->id, $data);

        $this->assertTrue($res);
        $this->assertDatabaseHas('transfers', $data->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_deletes_transfer(): void
    {
        $user = User::factory()->create();

        /** @var Account $accountFrom */
        $accountFrom = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Account $accountTo */
        $accountTo = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Payment $paymentFrom */
        $paymentFrom = Payment::factory()->create([
            'account_id' => $accountFrom->id,
        ]);

        /** @var Payment $paymentTo */
        $paymentTo = Payment::factory()->create([
            'account_id' => $accountTo->id,
        ]);

        /** @var Transfer $transfer */
        $transfer = Transfer::factory()->create([
            'from_payment_id' => $paymentFrom->id,
            'to_payment_id' => $paymentTo->id,
        ]);

        $service = $this->app->make(TransferService::class);
        $res = $service->deleteTransfer($transfer->id);

        $this->assertTrue($res);
        $this->assertDatabaseMissing('transfers', [
            'id' => $transfer->id,
        ]);
    }
}
