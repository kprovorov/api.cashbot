<?php

namespace App\TransferModule\Tests\Controllers;

use App\AccountModule\Models\Account;
use App\Enums\Currency;
use App\PaymentModule\Models\Payment;
use App\TransferModule\Models\Transfer;
use App\UserModule\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class TransferControllerTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_lists_transfers(): void
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $accountFrom */
        $accountFrom = Account::factory()->create();

        /** @var Account $accountTo */
        $accountTo = Account::factory()->create();

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

        $res = $this->get('api/transfers');

        $res->assertSuccessful();
        $res->assertJson($transfers->sortByDesc('id')->values()->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_shows_transfer(): void
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $accountFrom */
        $accountFrom = Account::factory()->create();

        /** @var Account $accountTo */
        $accountTo = Account::factory()->create();

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

        $res = $this->get("api/transfers/{$transfer->id}");

        $res->assertSuccessful();
        $res->assertJson($transfer->toArray());
    }

    /**
     * @test
     */
    public function it_successfully_creates_transfer(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $accountFrom */
        $accountFrom = Account::factory()->create([
            'currency' => Currency::USD,
            'user_id' => $user->id,
        ]);

        /** @var Account $accountTo */
        $accountTo = Account::factory()->create([
            'currency' => Currency::USD,
            'user_id' => $user->id,
        ]);

        /** @var Payment $paymentData */
        $paymentData = Payment::factory()->make([
            'currency' => Currency::USD,
        ]);

        $payload = [
            ...$paymentData->only([
                'amount',
                'date',
                'description',
                'hidden',
                'auto_apply',
            ]),
            'repeat' => 'none',
            'currency' => $paymentData->currency->name,
            'account_from_id' => $accountFrom->id,
            'account_to_id' => $accountTo->id,
        ];

        $res = $this->post('api/transfers', $payload);

        $res->assertOk();
    }

    /**
     * @test
     */
    public function it_successfully_updates_transfer(): void
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $accountFrom */
        $accountFrom = Account::factory()->create();

        /** @var Account $accountTo */
        $accountTo = Account::factory()->create();

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

        $payload = $transferData->toArray();

        $res = $this->put("api/transfers/{$transfer->id}", $payload);

        $res->assertSuccessful();
        $res->assertJson($payload);
        $this->assertDatabaseHas('transfers', $payload);
    }

    /**
     * @test
     */
    public function it_successfully_deletes_transfer(): void
    {
        $this->markTestSkipped();
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $accountFrom */
        $accountFrom = Account::factory()->create();

        /** @var Account $accountTo */
        $accountTo = Account::factory()->create();

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

        $res = $this->delete("api/transfers/{$transfer->id}");

        $res->assertSuccessful();
        $this->assertDatabaseMissing('transfers', [
            'id' => $transfer->id,
        ]);
    }
}
