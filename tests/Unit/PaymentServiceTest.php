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
            'currency' => $accountCurrency->value,
        ]);
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);
        $payment = Payment::factory()->create([
            'jar_id'          => $jar->id,
            'currency'        => $paymentCurrency->value,
            'amount'          => 10,
            'original_amount' => 100,
        ]);

        $mock = $this->mock(CurrencyConverter::class);

        $mock->shouldReceive('getRate')
             ->once()
             ->with($paymentCurrency->value, $accountCurrency->value)
             ->andReturn([
                 'buy'  => 1,
                 'sell' => 2,
             ]);

        $paymentService = app(PaymentService::class);

        $paymentService->updateCurrencyAmount($payment);

        $this->assertDatabaseHas('payments', [
            'id'              => $payment->id,
            'amount'          => 200,
            'original_amount' => 100,
        ]);
    }
}
