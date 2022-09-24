<?php

namespace Tests\Unit;

use App\Enums\Currency;
use App\Models\Account;
use App\Models\Jar;
use App\Models\Payment;
use App\Services\CurrencyConverter;
use App\Services\PaymentService;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    /**
     * @test
     * @return void
     * @throws \Exception
     */
    public function it_successfully_updates_payment_currency_amount(): void
    {
        $accountCurrency = Currency::UAH;
        $paymentCurrency = Currency::EUR;

        $account = Account::factory()->create([
            'currency' => $accountCurrency,
        ]);
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);
        $payment = Payment::factory()->create([
            'jar_id'          => $jar->id,
            'currency'        => $paymentCurrency,
            'amount'          => 10,
            'original_amount' => 100,
        ]);

        $mock = $this->mock(CurrencyConverter::class);
        $mock->shouldReceive('getRate')
             ->once()
             ->with($paymentCurrency, $accountCurrency)
             ->andReturn(2);

        $paymentService = $this->app->make(PaymentService::class);

        $paymentService->updateCurrencyAmount($payment);

        $this->assertDatabaseHas('payments', [
            'id'              => $payment->id,
            'amount'          => 200,
            'original_amount' => 100,
        ]);
    }

    /**
     * @test
     * @return void
     */
    public function it_successfully_updates_reducing_payment(): void
    {
        $originalAmount = 100;
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
            'jar_id'          => $jar->id,
            'original_amount' => $originalAmount,
            'currency'        => Currency::USD,
            'date'            => today()->subDay(),
            'ends_on'         => today()->addDays($daysLeft),
        ]);

        $mock = $this->mock(CurrencyConverter::class);
        $mock->shouldReceive('getRate')
             ->once()
             ->with($payment->currency, $account->currency)
             ->andReturn(2);

        $paymentService = $this->app->make(PaymentService::class);

        $paymentService->updateReducingPayment($payment);

        $this->assertDatabaseHas('payments', [
            'id'              => $payment->id,
            'original_amount' => 80,
            'amount'          => 160,
            'date'            => today()->toDateString(),
        ]);
    }
}
