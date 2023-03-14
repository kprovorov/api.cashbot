<?php

namespace App\PaymentModule\Tests\Controllers;

use App\AccountModule\Models\Account;
use App\Enums\Currency;
use App\Enums\RepeatUnit;
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

        $res = $this->get('payments');

        $res->assertSuccessful();
        $res->assertJson($payments->sortByDesc('id')->values()->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_shows_payment(): void
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'account_to_id' => $account->id,
        ]);

        $res = $this->get("payments/{$payment->id}");

        $res->assertSuccessful();
        $res->assertJson($payment->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_creates_income_payment(): void
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
            'account_to_id' => $account->id,
            'currency' => Currency::EUR,
        ]);

        $this->mock(CurrencyConverter::class)
            ->shouldReceive('convert')
            ->with($paymentData->amount, $account->currency, $paymentData->currency)
            ->once()
            ->andReturn($paymentData->amount * 2);

        $res = $this->post('payments', $paymentData->toArray());

        $res->assertCreated();
        $res->assertJson([
            ...Arr::except($paymentData->toArray(), ['group']),
            'amount_to_converted' => $paymentData->amount * 2,
        ]);
        $this->assertDatabaseHas('payments', [
            ...Arr::except($paymentData->toArray(), ['group']),
            'amount_to_converted' => $paymentData->amount * 2,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_creates_expense_payment(): void
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
            'account_from_id' => $account->id,
            'currency' => Currency::EUR,
        ]);

        $this->mock(CurrencyConverter::class)
            ->shouldReceive('convert')
            ->with(-$paymentData->amount, $account->currency, $paymentData->currency)
            ->once()
            ->andReturn(-$paymentData->amount * 2);

        $res = $this->post('payments', $paymentData->toArray());

        $res->assertCreated();
        $res->assertJson([
            ...Arr::except($paymentData->toArray(), ['group']),
            'amount_from_converted' => $paymentData->amount * 2,
        ]);
        $this->assertDatabaseHas('payments', [
            ...Arr::except($paymentData->toArray(), ['group']),
            'amount_from_converted' => $paymentData->amount * 2,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_creates_transfer_payment(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $accountTo = Account::factory()->create([
            'currency' => Currency::UAH,
            'user_id' => $user->id,
        ]);
        $accountFrom = Account::factory()->create([
            'currency' => Currency::EUR,
            'user_id' => $user->id,
        ]);

        /** @var Payment $paymentData */
        $paymentData = Payment::factory()->make([
            'account_to_id' => $accountTo->id,
            'account_from_id' => $accountFrom->id,
            'currency' => Currency::EUR,
        ]);

        $currencyConverter = $this->mock(CurrencyConverter::class);

        $currencyConverter->shouldReceive('convert')
            ->with($paymentData->amount, $accountTo->currency, $paymentData->currency)
            ->once()
            ->andReturn($paymentData->amount * 2);

        $currencyConverter->shouldReceive('convert')
            ->with(-$paymentData->amount, $accountFrom->currency, $paymentData->currency)
            ->once()
            ->andReturn(-$paymentData->amount * 2);

        $res = $this->post('payments', $paymentData->toArray());

        $res->assertCreated();
        $res->assertJson([
            ...Arr::except($paymentData->toArray(), ['group']),
            'amount_to_converted' => $paymentData->amount * 2,
            'amount_from_converted' => $paymentData->amount * 2,
        ]);
        $this->assertDatabaseHas('payments', [
            ...Arr::except($paymentData->toArray(), ['group']),
            'amount_to_converted' => $paymentData->amount * 2,
            'amount_from_converted' => $paymentData->amount * 2,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_updates_payment(): void
    {
        $this->markTestSkipped();
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

        $res = $this->put("payments/{$payment->id}", $payload);

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
    public function it_successfully_deletes_single_payment(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'account_to_id' => $account->id,
        ]);

        $res = $this->delete("payments/{$payment->id}");

        $res->assertSuccessful();
        $this->assertDatabaseMissing('payments', [
            'id' => $payment->id,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_deletes_first_payment_in_the_chain(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'account_to_id' => $account->id,
            'repeat_unit' => RepeatUnit::MONTH,
        ]);

        $res = $this->delete("payments/{$payment->id}", [
            'date' => $payment->date->format('Y-m-d'),
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id', 'date', 'created_at', 'updated_at']),
            'date' => $payment->date->addMonthNoOverflow()->format('Y-m-d'),
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_deletes_last_payment_in_the_chain(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'account_to_id' => $account->id,
            'repeat_unit' => RepeatUnit::MONTH,
            'repeat_ends_on' => now()->addMonthsNoOverflow(4),
        ]);

        $res = $this->delete("payments/{$payment->id}", [
            'date' => now()->addMonthsNoOverflow(4)->format('Y-m-d'),
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), []),
            'repeat_ends_on' => now()->addMonthsNoOverflow(4)->subDay()->format('Y-m-d'),
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_deletes_middle_payment_in_the_chain(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'account_to_id' => $account->id,
            'repeat_unit' => RepeatUnit::MONTH,
        ]);

        $res = $this->delete("payments/{$payment->id}", [
            'date' => now()->addMonthsNoOverflow(4)->format('Y-m-d'),
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), []),
            'repeat_ends_on' => now()->addMonthsNoOverflow(4)->subDay()->format('Y-m-d'),
        ]);
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id', 'date', 'created_at', 'updated_at']),
            'date' => now()->addMonthsNoOverflow(5)->format('Y-m-d'),
        ]);
    }
}
