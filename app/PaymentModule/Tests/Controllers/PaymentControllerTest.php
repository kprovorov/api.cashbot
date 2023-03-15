<?php

namespace App\PaymentModule\Tests\Controllers;

use App\AccountModule\Models\Account;
use App\Enums\Currency;
use App\Enums\PaymentUpdateMode;
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
    public function it_successfully_updates_first_payment_in_a_chain_in_single_mode(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->repeatable()->create([
            'account_to_id' => $account->id,
            'currency' => Currency::USD,
        ]);

        /** @var Payment $updateData */
        $updateData = Payment::factory()->make([
            'account_to_id' => $account->id,
            'currency' => Currency::EUR,
        ]);

        $this->mock(CurrencyConverter::class)
            ->shouldReceive('convert')
            ->once()
            ->andReturn($updateData->amount * 2);

        $res = $this->put("payments/$payment->id/general", [
            ...$updateData->toArray(),
            'from_date' => $payment->date,
            'mode' => PaymentUpdateMode::SINGLE->value,
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('payments', 2);
        // Assert first payment (the one which was updated)
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id']),
            ...Arr::only($updateData->toArray(), [
                'account_to_id',
                'account_from_id',
                'description',
                'amount',
                'currency',
            ]),
            'amount_to_converted' => $updateData->amount * 2,
            'date' => $payment->date,
            'repeat_ends_on' => $payment->date->clone()->add($payment->repeat_interval, $payment->repeat_unit->value, overflow: false)->subDay(),
        ]);
        // Assert rest of chain
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id']),
            'date' => $payment->date->clone()->add($payment->repeat_interval, $payment->repeat_unit->value, false),
            'repeat_ends_on' => null,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_updates_middle_payment_in_a_chain_in_single_mode(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->repeatable()->create([
            'account_to_id' => $account->id,
            'currency' => Currency::USD,
        ]);

        /** @var Payment $updateData */
        $updateData = Payment::factory()->make([
            'account_to_id' => $account->id,
            'currency' => Currency::EUR,
        ]);

        $this->mock(CurrencyConverter::class)
            ->shouldReceive('convert')
            ->once()
            ->andReturn($updateData->amount * 2);

        $fromDate = $payment->date->clone()->add(2, $payment->repeat_unit->value, false);

        $res = $this->put("payments/$payment->id/general", [
            ...$updateData->toArray(),
            'from_date' => $fromDate,
            'mode' => PaymentUpdateMode::SINGLE->value,
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('payments', 3);
        // Assert payment before updated one
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id']),
            'date' => $payment->date,
            'repeat_ends_on' => $fromDate->clone()->subDay(),
        ]);
        // Assert updated payment
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id']),
            ...Arr::only($updateData->toArray(), [
                'account_to_id',
                'account_from_id',
                'description',
                'amount',
                'currency',
            ]),
            'amount_to_converted' => $updateData->amount * 2,
            'date' => $fromDate,
            'repeat_ends_on' => $fromDate->clone()->add($payment->repeat_interval, $payment->repeat_unit->value, false)->subDay(),
        ]);
        // Assert rest of chain
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id']),
            'date' => $fromDate->clone()->add($payment->repeat_interval, $payment->repeat_unit->value),
            'repeat_ends_on' => null,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_updates_last_payment_in_a_chain_in_single_mode(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->repeatable()->create([
            'account_to_id' => $account->id,
            'currency' => Currency::USD,
        ]);
        $repeatEndsOn = $payment->date->clone()->add($payment->repeat_interval * 5, $payment->repeat_unit->value, false);
        $payment->update([
            'repeat_ends_on' => $repeatEndsOn,
        ]);

        /** @var Payment $updateData */
        $updateData = Payment::factory()->make([
            'account_to_id' => $account->id,
            'currency' => Currency::EUR,
        ]);

        $this->mock(CurrencyConverter::class)
            ->shouldReceive('convert')
            ->once()
            ->andReturn($updateData->amount * 2);

        $fromDate = $repeatEndsOn->clone();

        $res = $this->put("payments/$payment->id/general", [
            ...$updateData->toArray(),
            'from_date' => $fromDate,
            'mode' => PaymentUpdateMode::SINGLE->value,
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('payments', 2);
        // Assert payment before updated one
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id']),
            'date' => $payment->date,
            'repeat_ends_on' => $fromDate->clone()->subDay(),
        ]);
        // Assert updated payment
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id']),
            ...Arr::only($updateData->toArray(), [
                'account_to_id',
                'account_from_id',
                'description',
                'amount',
                'currency',
            ]),
            'amount_to_converted' => $updateData->amount * 2,
            'date' => $fromDate,
            'repeat_ends_on' => $fromDate,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_updates_first_payment_in_a_chain_in_future_mode(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->repeatable()->create([
            'account_to_id' => $account->id,
            'currency' => Currency::USD,
        ]);

        /** @var Payment $updateData */
        $updateData = Payment::factory()->make([
            'account_to_id' => $account->id,
            'currency' => Currency::EUR,
        ]);

        $this->mock(CurrencyConverter::class)
            ->shouldReceive('convert')
            ->once()
            ->andReturn($updateData->amount * 2);

        $res = $this->put("payments/$payment->id/general", [
            ...$updateData->toArray(),
            'from_date' => $payment->date,
            'mode' => PaymentUpdateMode::FUTURE->value,
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('payments', 1);
        // Assert first payment (the one which was updated)
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id']),
            ...Arr::only($updateData->toArray(), [
                'account_to_id',
                'account_from_id',
                'description',
                'amount',
                'currency',
            ]),
            'amount_to_converted' => $updateData->amount * 2,
            'date' => $payment->date,
            'repeat_ends_on' => null,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_updates_middle_payment_in_a_chain_in_future_mode(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->repeatable()->create([
            'account_to_id' => $account->id,
            'currency' => Currency::USD,
        ]);

        /** @var Payment $updateData */
        $updateData = Payment::factory()->make([
            'account_to_id' => $account->id,
            'currency' => Currency::EUR,
        ]);

        $this->mock(CurrencyConverter::class)
            ->shouldReceive('convert')
            ->once()
            ->andReturn($updateData->amount * 2);

        $fromDate = $payment->date->clone()->add(2, $payment->repeat_unit->value, false);

        $res = $this->put("payments/$payment->id/general", [
            ...$updateData->toArray(),
            'from_date' => $fromDate,
            'mode' => PaymentUpdateMode::FUTURE->value,
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('payments', 2);
        // Assert payment before updated one
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id']),
            'date' => $payment->date,
            'repeat_ends_on' => $fromDate->clone()->subDay(),
        ]);
        // Assert updated payment
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id']),
            ...Arr::only($updateData->toArray(), [
                'account_to_id',
                'account_from_id',
                'description',
                'amount',
                'currency',
            ]),
            'amount_to_converted' => $updateData->amount * 2,
            'date' => $fromDate,
            'repeat_ends_on' => null,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_updates_last_payment_in_a_chain_in_future_mode(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->repeatable()->create([
            'account_to_id' => $account->id,
            'currency' => Currency::USD,
        ]);
        $repeatEndsOn = $payment->date->clone()->add($payment->repeat_interval * 5, $payment->repeat_unit->value, false);
        $payment->update([
            'repeat_ends_on' => $repeatEndsOn,
        ]);

        /** @var Payment $updateData */
        $updateData = Payment::factory()->make([
            'account_to_id' => $account->id,
            'currency' => Currency::EUR,
        ]);

        $this->mock(CurrencyConverter::class)
            ->shouldReceive('convert')
            ->once()
            ->andReturn($updateData->amount * 2);

        $fromDate = $repeatEndsOn->clone();

        $res = $this->put("payments/$payment->id/general", [
            ...$updateData->toArray(),
            'from_date' => $fromDate,
            'mode' => PaymentUpdateMode::FUTURE->value,
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('payments', 2);
        // Assert payment before updated one
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id']),
            'date' => $payment->date,
            'repeat_ends_on' => $fromDate->clone()->subDay(),
        ]);
        // Assert updated payment
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id']),
            ...Arr::only($updateData->toArray(), [
                'account_to_id',
                'account_from_id',
                'description',
                'amount',
                'currency',
            ]),
            'amount_to_converted' => $updateData->amount * 2,
            'date' => $fromDate,
            'repeat_ends_on' => $fromDate,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_updates_first_payment_in_a_chain_in_all_mode(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->repeatable()->create([
            'account_to_id' => $account->id,
            'currency' => Currency::USD,
        ]);

        /** @var Payment $updateData */
        $updateData = Payment::factory()->make([
            'account_to_id' => $account->id,
            'currency' => Currency::EUR,
        ]);

        $this->mock(CurrencyConverter::class)
            ->shouldReceive('convert')
            ->once()
            ->andReturn($updateData->amount * 2);

        $res = $this->put("payments/$payment->id/general", [
            ...$updateData->toArray(),
            'from_date' => $payment->date,
            'mode' => PaymentUpdateMode::ALL->value,
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('payments', 1);
        // Assert first payment (the one which was updated)
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id']),
            ...Arr::only($updateData->toArray(), [
                'account_to_id',
                'account_from_id',
                'description',
                'amount',
                'currency',
            ]),
            'amount_to_converted' => $updateData->amount * 2,
            'date' => $payment->date,
            'repeat_ends_on' => null,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_updates_middle_payment_in_a_chain_in_all_mode(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->repeatable()->create([
            'account_to_id' => $account->id,
            'currency' => Currency::USD,
        ]);

        /** @var Payment $updateData */
        $updateData = Payment::factory()->make([
            'account_to_id' => $account->id,
            'currency' => Currency::EUR,
        ]);

        $this->mock(CurrencyConverter::class)
            ->shouldReceive('convert')
            ->once()
            ->andReturn($updateData->amount * 2);

        $fromDate = $payment->date->clone()->add(2, $payment->repeat_unit->value, false);

        $res = $this->put("payments/$payment->id/general", [
            ...$updateData->toArray(),
            'from_date' => $fromDate,
            'mode' => PaymentUpdateMode::ALL->value,
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('payments', 1);
        // Assert updated payment
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id']),
            ...Arr::only($updateData->toArray(), [
                'account_to_id',
                'account_from_id',
                'description',
                'amount',
                'currency',
            ]),
            'amount_to_converted' => $updateData->amount * 2,
            'date' => $payment->date,
            'repeat_ends_on' => null,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_updates_last_payment_in_a_chain_in_all_mode(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->repeatable()->create([
            'account_to_id' => $account->id,
            'currency' => Currency::USD,
        ]);
        $repeatEndsOn = $payment->date->clone()->add($payment->repeat_interval * 5, $payment->repeat_unit->value, false);
        $payment->update([
            'repeat_ends_on' => $repeatEndsOn,
        ]);

        /** @var Payment $updateData */
        $updateData = Payment::factory()->make([
            'account_to_id' => $account->id,
            'currency' => Currency::EUR,
        ]);

        $this->mock(CurrencyConverter::class)
            ->shouldReceive('convert')
            ->once()
            ->andReturn($updateData->amount * 2);

        $fromDate = $repeatEndsOn->clone();

        $res = $this->put("payments/$payment->id/general", [
            ...$updateData->toArray(),
            'from_date' => $fromDate,
            'mode' => PaymentUpdateMode::ALL->value,
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('payments', 1);
        // Assert updated payment
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id']),
            ...Arr::only($updateData->toArray(), [
                'account_to_id',
                'account_from_id',
                'description',
                'amount',
                'currency',
            ]),
            'amount_to_converted' => $updateData->amount * 2,
            'date' => $payment->date,
            'repeat_ends_on' => $payment->repeat_ends_on,
        ]);
    }

    /**
     * @test
     */
    public function it_successfully_updates_non_repeatable_payment_in_a_chain(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $account */
        $account = Account::factory()->create([
            'currency' => Currency::UAH,
            'user_id' => $user->id,
        ]);

        /** @var Payment $payment */
        $payment = Payment::factory()->create([
            'account_to_id' => $account->id,
            'currency' => Currency::USD,
        ]);

        /** @var Payment $updateData */
        $updateData = Payment::factory()->make([
            'account_to_id' => $account->id,
            'currency' => Currency::EUR,
        ]);

        $this->mock(CurrencyConverter::class)
            ->shouldReceive('convert')
            ->once()
            ->andReturn($updateData->amount * 2);

        $res = $this->put("payments/$payment->id/general", [
            ...$updateData->toArray(),
            'from_date' => $payment->date,
            'mode' => PaymentUpdateMode::SINGLE->value,
        ]);

        $res->assertSuccessful();
        $this->assertDatabaseCount('payments', 1);
        // Assert updated payment
        $this->assertDatabaseHas('payments', [
            ...Arr::except($payment->toArray(), ['id']),
            ...Arr::only($updateData->toArray(), [
                'account_to_id',
                'account_from_id',
                'description',
                'amount',
                'currency',
            ]),
            'amount_to_converted' => $updateData->amount * 2,
            'date' => $payment->date,
            'repeat_ends_on' => $payment->repeat_ends_on,
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
