<?php

namespace App\PaymentModule\Tests\Services;

use App\Enums\Currency;
use App\Models\Account;
use App\Models\Jar;
use App\PaymentModule\DTO\CreatePaymentData;
use App\PaymentModule\DTO\UpdatePaymentData;
use App\PaymentModule\Models\Payment;
use App\PaymentModule\Services\PaymentService;
use App\Services\CurrencyConverter;
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
        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        /** @var Collection $payments */
        $payments = Payment::factory()->count(3)->create([
            'jar_id' => $jar->id,
        ]);

        $service = $this->app->make(PaymentService::class);
        $res = $service->getAllPayments();

        $this->assertCount(3, $res);
        $payments->each(fn (Payment $payment) => $this->assertContains(
            $payment->id,
            $res->pluck('id')
        ));
    }

    /**
     * @test
     */
    public function it_successfully_gets_all_payments_paginated(): void
    {
        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        /** @var Collection $payments */
        $payments = Payment::factory()->count(3)->create([
            'jar_id' => $jar->id,
        ]);

        $service = $this->app->make(PaymentService::class);
        $res = $service->getAllPaymentsPaginated();

        $this->assertCount(3, $res);
        $payments->each(fn (Payment $payment) => $this->assertContains(
            $payment->id,
            $res->pluck('id')
        ));
    }

    /**
     * @test
     */
    public function it_successfully_gets_payment(): void
    {
        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'jar_id' => $jar->id,
        ]);

        $service = $this->app->make(PaymentService::class);
        $res = $service->getPayment($payment->id);

        $this->assertEquals($payment->refresh()->toArray(), $res->toArray());
    }

    /**
     * @test
     *
     * @throws UnknownProperties
     */
    public function it_successfully_creates_payment(): void
    {
        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
        ]);

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        /** @var Payment $paymentData */
        $paymentData = Payment::factory()->make([
            'jar_id' => $jar->id,
            'currency' => Currency::EUR,
        ]);

        $data = new CreatePaymentData([
            ...$paymentData->toArray(),
            'date' => $paymentData->date,
            'currency' => $paymentData->currency,
        ]);

        $mock = $this->mock(CurrencyConverter::class);
        $mock->shouldReceive('getRate')->once()->andReturn(2);

        $service = $this->app->make(PaymentService::class);
        $res = $service->createPayment($data);

        $this->assertEquals([
            ...$data->toArray(),
            'amount_converted' => $data->amount * 2,
            'date' => $data->date->toDateTimeString(),
            'currency' => $data->currency->name,
        ], [
            ...Arr::except($res->toArray(), [
                'id',
                'created_at',
                'updated_at',
            ]),
            'date' => $res->date->toDateTimeString(),
        ]);
        $this->assertDatabaseHas('payments', $data->toArray());
    }

    /**
     * @test
     *
     * @throws UnknownProperties
     * @throws GuzzleException
     */
    public function it_successfully_updates_payment(): void
    {
        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
        ]);

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'jar_id' => $jar->id,
            'currency' => Currency::EUR,
        ]);

        /** @var Payment $paymentData */
        $paymentData = Payment::factory()->make([
            'jar_id' => $jar->id,
            'currency' => Currency::EUR,
        ]);

        $data = new UpdatePaymentData([
            ...$paymentData->toArray(),
            'date' => $paymentData->date,
            'currency' => $paymentData->currency,
        ]);

        $mock = $this->mock(CurrencyConverter::class);
        $mock->shouldReceive('getRate')->once()->andReturn(2);

        $service = $this->app->make(PaymentService::class);
        $res = $service->updatePayment($payment->id, $data);

        $this->assertTrue($res);
        $this->assertDatabaseHas('payments', [
            ...$data->toArray(),
            'amount' => $data->amount,
            'amount_converted' => $data->amount * 2,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_deletes_payment(): void
    {
        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'jar_id' => $jar->id,
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
     * @return void
     *
     * @throws Exception
     */
    public function it_successfully_updates_payment_currency_amount(): void
    {
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
        ]);
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);
        $payment = Payment::factory()->create([
            'jar_id' => $jar->id,
            'currency' => Currency::EUR,
            'amount' => 10,
            'amount_converted' => 100,
        ]);

        $mock = $this->mock(CurrencyConverter::class);
        $mock->shouldReceive('getRate')
             ->once()
             ->andReturn(2);

        $paymentService = $this->app->make(PaymentService::class);

        $paymentService->updateCurrencyAmount($payment);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'amount' => 10,
            'amount_converted' => 20,
        ]);
    }

    /**
     * @test
     *
     * @return void
     *
     * @throws UnknownProperties
     */
    public function it_successfully_updates_reducing_payment(): void
    {
        $amount = 100;
        $daysLeft = 4;

        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
        ]);

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'jar_id' => $jar->id,
            'amount' => $amount,
            'currency' => Currency::EUR,
            'date' => today()->subDay(),
            'ends_on' => today()->addDays($daysLeft),
        ]);

        $mock = $this->mock(CurrencyConverter::class);
        $mock->shouldReceive('getRate')
             ->once()
             ->andReturn(2);

        $paymentService = $this->app->make(PaymentService::class);
        $paymentService->updateReducingPayment($payment);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'amount' => 80,
            'amount_converted' => 160,
            'date' => today()->toDateString(),
        ]);
    }
}
