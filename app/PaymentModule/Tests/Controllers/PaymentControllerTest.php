<?php

namespace App\PaymentModule\Tests\Controllers;

use App\AccountModule\Models\Account;
use App\Enums\Currency;
use App\PaymentModule\Models\Payment;
use App\Services\CurrencyConverter;
use App\UserModule\Models\User;
use Arr;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_lists_payments(): void
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Collection $payments */
        $payments = Payment::factory()->count(2)->create([
            'account_id' => $account->id,
        ]);

        $res = $this->get('api/payments');

        $res->assertSuccessful();
        $res->assertJson($payments->sortByDesc('id')->values()->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_shows_payment(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'account_id' => $account->id,
        ]);

        $res = $this->get("api/payments/{$payment->id}");

        $res->assertSuccessful();
        $res->assertJson($payment->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_creates_payment(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
            'user_id' => $user->id,
        ]);

        /** @var Payment $paymentData */
        $paymentData = Payment::factory()->make([
            'account_id' => $account->id,
            'currency' => Currency::EUR,
        ]);

        $payload = [
            ...$paymentData->toArray(),
            'repeat' => 'none',
        ];

        $this->mock(CurrencyConverter::class)
             ->shouldReceive('convert')
             ->once()
             ->andReturn($paymentData->amount * 2);

        $res = $this->post('api/payments', $payload);

        $res->assertOk();
//        $res->assertJson($payload);
        $this->assertDatabaseHas('payments', [
            ...Arr::except($paymentData->toArray(), ['group']),
            'amount_converted' => $payload['amount'] * 2,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_updates_payment(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'account_id' => $account->id,
            'currency' => Currency::USD,
        ]);

        /** @var Payment $paymentData */
        $paymentData = Payment::factory()->make([
            'account_id' => $account->id,
            'currency' => Currency::USD,
        ]);

        $payload = Arr::except($paymentData->toArray(), ['group']);

        $this->mock(CurrencyConverter::class)
            ->shouldReceive('convert')
            ->once()
            ->andReturn($paymentData->amount * 2);

        $res = $this->put("api/payments/{$payment->id}", $payload);

        $res->assertSuccessful();
//        $res->assertJson($payload);
        $this->assertDatabaseHas('payments', [
            ...$payload,
            'amount_converted' => $payload['amount'] * 2,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_deletes_payment(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'account_id' => $account->id,
        ]);

        $res = $this->delete("api/payments/{$payment->id}");

        $res->assertSuccessful();
        $this->assertDatabaseMissing('payments', [
            'id' => $payment->id,
        ]);
    }
}
