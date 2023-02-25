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
        // Create parent_id column
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignIdFor(Account::class, 'parent_id')->nullable()->after('id');
        });

        // Create account_id column on Payments
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignIdFor(Account::class)->nullable()->after('id');
        });

        // Create sub-accounts for each Jar
        DB::table('jars')->where('default', false)->get()->each(function ($jar) {
            $mainAccount = Account::find($jar->account_id);

            $account = Account::create([
                'parent_id' => $mainAccount->id,
                'name' => $jar->name,
                'balance' => 0,
                'currency' => $mainAccount->currency,
                'user_id' => $mainAccount->user_id,
            ]);

            // Re-assign Jar to sub-account
            DB::table('jars')->where('id', $jar->id)->update(['account_id' => $account->id, 'default' => true]);
        });

        // Assign payments to sub-accounts
        Payment::whereNull('payments.account_id')
            ->join('jars', 'jars.id', '=', 'payments.jar_id')
            ->join('accounts', 'accounts.id', '=', 'jars.account_id')
            ->update(['payments.account_id' => DB::raw('jars.account_id')]);

        // Drop jar_id column
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('jar_id');
        });

        // Make account_id column on Payments not nullable
        Schema::table('payments', function (Blueprint $table) {
            $table->foreignIdFor(Account::class)->nullable(false)->change();
        });

        // Drop jars table
        Schema::drop('jars');
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
