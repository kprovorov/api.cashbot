<?php

namespace App\TransferModule\Tests\Controllers;

use App\AccountModule\Models\Account;
use App\AccountModule\Models\Jar;
use App\Enums\Currency;
use App\UserModule\Models\User;
use App\PaymentModule\Models\Payment;
use App\TransferModule\Models\Transfer;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class TransferControllerTest extends TestCase
{
    /**
     * @test
     */
    public function it_successfully_lists_transfers(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $accountFrom */
        $accountFrom = Account::factory()->create();

        /** @var Account $accountTo */
        $accountTo = Account::factory()->create();

        /** @var Jar $jarFrom */
        $jarFrom = Jar::factory()->create([
            'account_id' => $accountFrom->id,
        ]);

        /** @var Jar $jarTo */
        $jarTo = Jar::factory()->create([
            'account_id' => $accountTo->id,
        ]);

        /** @var Payment $paymentFrom */
        $paymentFrom = Payment::factory()->create([
            'jar_id' => $jarFrom->id,
        ]);

        /** @var Payment $paymentTo */
        $paymentTo = Payment::factory()->create([
            'jar_id' => $jarTo->id,
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
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $accountFrom */
        $accountFrom = Account::factory()->create();

        /** @var Account $accountTo */
        $accountTo = Account::factory()->create();

        /** @var Jar $jarFrom */
        $jarFrom = Jar::factory()->create([
            'account_id' => $accountFrom->id,
        ]);

        /** @var Jar $jarTo */
        $jarTo = Jar::factory()->create([
            'account_id' => $accountTo->id,
        ]);

        /** @var Payment $paymentFrom */
        $paymentFrom = Payment::factory()->create([
            'jar_id' => $jarFrom->id,
        ]);

        /** @var Payment $paymentTo */
        $paymentTo = Payment::factory()->create([
            'jar_id' => $jarTo->id,
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
        ]);

        /** @var Account $accountTo */
        $accountTo = Account::factory()->create([
            'currency' => Currency::USD,
        ]);

        /** @var Jar $jarFrom */
        $jarFrom = Jar::factory()->create([
            'account_id' => $accountFrom->id,
        ]);

        /** @var Jar $jarTo */
        $jarTo = Jar::factory()->create([
            'account_id' => $accountTo->id,
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
            ]),
            'currency' => $paymentData->currency->name,
            'jar_from_id' => $jarFrom->id,
            'jar_to_id' => $jarTo->id,
        ];

        $res = $this->post('api/transfers', $payload);

        $res->assertOk();
    }

    /**
     * @test
     */
    public function it_successfully_updates_transfer(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $accountFrom */
        $accountFrom = Account::factory()->create();

        /** @var Account $accountTo */
        $accountTo = Account::factory()->create();

        /** @var Jar $jarFrom */
        $jarFrom = Jar::factory()->create([
            'account_id' => $accountFrom->id,
        ]);

        /** @var Jar $jarTo */
        $jarTo = Jar::factory()->create([
            'account_id' => $accountTo->id,
        ]);

        /** @var Payment $paymentFrom */
        $paymentFrom = Payment::factory()->create([
            'jar_id' => $jarFrom->id,
        ]);

        /** @var Payment $paymentTo */
        $paymentTo = Payment::factory()->create([
            'jar_id' => $jarTo->id,
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
        $user = User::factory()->create();
        $this->actingAs($user);

        /** @var Account $accountFrom */
        $accountFrom = Account::factory()->create();

        /** @var Account $accountTo */
        $accountTo = Account::factory()->create();

        /** @var Jar $jarFrom */
        $jarFrom = Jar::factory()->create([
            'account_id' => $accountFrom->id,
        ]);

        /** @var Jar $jarTo */
        $jarTo = Jar::factory()->create([
            'account_id' => $accountTo->id,
        ]);

        /** @var Payment $paymentFrom */
        $paymentFrom = Payment::factory()->create([
            'jar_id' => $jarFrom->id,
        ]);

        /** @var Payment $paymentTo */
        $paymentTo = Payment::factory()->create([
            'jar_id' => $jarTo->id,
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
