<?php

use App\AccountModule\Models\Account;
use App\PaymentModule\Models\Payment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignIdFor(Account::class, 'account_from_id')->nullable()->after('updated_at');
            $table->foreignIdFor(Account::class, 'account_to_id')->nullable()->after('updated_at');
            $table->integer('amount_from_converted')->nullable()->after('amount_converted');
            $table->integer('amount_to_converted')->nullable()->after('amount_converted');
        });

        // Refactor Transfer payments
        DB::table('transfers')->get()->each(function (stdClass $transfer) {
            $paymentFrom = Payment::find($transfer->from_payment_id);
            $paymentTo = Payment::find($transfer->to_payment_id);

            $paymentFrom->update([
                'account_from_id' => $paymentFrom->account_id,
                'account_to_id' => $paymentTo->account_id,
                'amount' => -$paymentFrom->amount,
                'amount_from_converted' => -$paymentFrom->amount_converted,
                'amount_to_converted' => $paymentTo->amount_converted,
            ]);

            Payment::where('id', $transfer->to_payment_id)->delete();
        });

        // Refactor income payments
        Payment::where('amount', '>', 0)
            ->whereNull('account_from_id')
            ->whereNull('account_to_id')
            ->get()
            ->each(function (Payment $payment) {
                $payment->update([
                    'account_to_id' => $payment->account_id,
                    'amount_to_converted' => $payment->amount_converted,
                ]);
            });

        // Refactor expense payments
        Payment::where('amount', '<', 0)
            ->whereNull('account_from_id')
            ->whereNull('account_to_id')
            ->get()
            ->each(function (Payment $payment) {
                $payment->update([
                    'account_from_id' => $payment->account_id,
                    'amount' => -$payment->amount,
                    'amount_from_converted' => -$payment->amount_converted,
                ]);
            });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('account_id');
            $table->dropColumn('amount_converted');
        });

        Schema::drop('transfers');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
};
