<?php

namespace App\PaymentModule\Tests\Controllers;

use App\Enums\Currency;
use App\Models\Account;
use App\Models\Jar;
use App\Models\User;
use App\PaymentModule\Models\Payment;
use App\Services\CurrencyConverter;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class PaymentControllerTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_lists_payments(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create();

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        /** @var Collection $payments */
        $payments = Payment::factory()->count(2)->create([
            'jar_id' => $jar->id,
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
        $account = Account::factory()->create();

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'jar_id' => $jar->id,
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

        $payload = $paymentData->toArray();

        $this->mock(CurrencyConverter::class)
             ->shouldReceive('getRate')
             ->once()
             ->andReturn(2);

        $res = $this->post('api/payments', $payload);

        $res->assertOk();
//        $res->assertJson($payload);
        $this->assertDatabaseHas('payments', [
            ...$payload,
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
        ]);

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'jar_id' => $jar->id,
            'currency' => Currency::USD,
        ]);

        /** @var Payment $paymentData */
        $paymentData = Payment::factory()->make([
            'jar_id' => $jar->id,
            'currency' => Currency::USD,
        ]);

        $payload = $paymentData->toArray();

        $this->mock(CurrencyConverter::class)
             ->shouldReceive('getRate')
             ->once()
             ->andReturn(2);

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
        $account = Account::factory()->create();

        /** @var Jar $jar */
        $jar = Jar::factory()->create([
            'account_id' => $account->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'jar_id' => $jar->id,
        ]);

        $res = $this->delete("api/payments/{$payment->id}");

        $res->assertSuccessful();
        $this->assertDatabaseMissing('payments', [
            'id' => $payment->id,
        ]);
    }
}
