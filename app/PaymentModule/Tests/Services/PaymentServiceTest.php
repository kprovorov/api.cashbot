<?php

namespace App\PaymentModule\Tests\Services;

use App\AccountModule\Models\Account;
use App\Enums\Currency;
use App\Enums\RepeatUnit;
use App\PaymentModule\DTO\CreatePaymentData;
use App\PaymentModule\DTO\UpdatePaymentData;
use App\PaymentModule\Models\Payment;
use App\PaymentModule\Services\PaymentService;
use App\Services\CurrencyConverter;
use App\UserModule\Models\User;
use Arr;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Database\Eloquent\Collection;
use Spatie\DataTransferObject\Exceptions\UnknownProperties;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_gets_all_payments(): void
    {
        $user = User::factory()->create();

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Collection $payments */
        $payments = Payment::factory()->count(3)->create([
            'account_to_id' => $account->id,
        ]);

        $service = $this->app->make(PaymentService::class);
        $res = $service->getAllPayments();

        $this->assertCount(3, $res);
        $payments->each(fn(Payment $payment) => $this->assertContains(
            $payment->id,
            $res->pluck('id')
        ));
    }

    /**
     * @test
     */
    public function it_successfully_gets_all_payments_paginated(): void
    {
        $user = User::factory()->create();

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Collection $payments */
        $payments = Payment::factory()->count(3)->create([
            'account_to_id' => $account->id,
        ]);

        $service = $this->app->make(PaymentService::class);
        $res = $service->getAllPaymentsPaginated();

        $this->assertCount(3, $res);
        $payments->each(fn(Payment $payment) => $this->assertContains(
            $payment->id,
            $res->pluck('id')
        ));
    }

    /**
     * @test
     */
    public function it_successfully_gets_payment(): void
    {
        $user = User::factory()->create();

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'account_to_id' => $account->id,
        ]);

        $service = $this->app->make(PaymentService::class);
        $res = $service->getPayment($payment->id);

        $this->assertEquals($payment->refresh()->toArray(), $res->toArray());
    }

    /**
     * @test
     *
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function it_successfully_updates_payment(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'account_to_id' => $account->id,
            'currency' => Currency::EUR,
        ]);

        /** @var Payment $paymentData */
        $paymentData = Payment::factory()->make([
            'account_to_id' => $account->id,
            'currency' => Currency::EUR,
        ]);

        $data = new UpdatePaymentData([
            ...$paymentData->toArray(),
            'date' => $paymentData->date,
            'currency' => $paymentData->currency,
            'repeat_unit' => $paymentData->repeat_unit,
        ]);

        $this->mock(CurrencyConverter::class)
            ->shouldReceive('convert')
            ->once()
            ->andReturn($paymentData->amount * 2);

        $service = $this->app->make(PaymentService::class);
        $res = $service->updatePayment($payment->id, $data);

        $this->assertTrue($res);
        $this->assertDatabaseHas('payments', [
            ...$data->toArray(),
            'amount' => $data->amount,
            'amount_to_converted' => $data->amount * 2,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_deletes_payment(): void
    {
        $user = User::factory()->create();

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'account_to_id' => $account->id,
        ]);

        $service = $this->app->make(PaymentService::class);
        $res = $service->deletePayment($payment->id);

        $this->assertTrue($res);
        $this->assertDatabaseMissing('payments', [
            'id' => $payment->id,
        ]);
    }

    /**
     * @test
     *
     * @throws Exception
     */
    public function it_successfully_updates_payment_currency_amount(): void
    {
        $user = User::factory()->create();

        $account = Account::factory()->create([
            'currency' => Currency::UAH,
            'user_id' => $user->id,
        ]);

        $payment = Payment::factory()->create([
            'account_to_id' => $account->id,
            'currency' => Currency::EUR,
            'amount' => 10,
            'amount_to_converted' => 100,
        ]);

        $this->mock(CurrencyConverter::class)
            ->shouldReceive('convert')
            ->once()
            ->andReturn($payment->amount * 2);

        $paymentService = $this->app->make(PaymentService::class);

        $paymentService->updateCurrencyAmount($payment);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'amount' => 10,
            'amount_to_converted' => 20,
        ]);
    }

    /**
     * @test
     *
     * @throws UnknownProperties
     */
    public function it_successfully_updates_reducing_payment(): void
    {
        $amount = 100;
        $daysLeft = 4;

        $user = User::factory()->create();

        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'account_to_id' => $account->id,
            'amount' => $amount,
            'currency' => Currency::EUR,
            'date' => today()->subDay(),
            'ends_on' => today()->addDays($daysLeft),
        ]);

        $this->mock(CurrencyConverter::class)
            ->shouldReceive('convert')
            ->once()
            ->andReturn(80 * 2);

        $paymentService = $this->app->make(PaymentService::class);
        $paymentService->updateReducingPayment($payment);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'amount' => 80,
            'amount_to_converted' => 160,
            'date' => today()->toDateString(),
        ]);
    }
}
