<?php

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
        // Create new nullable column
        Schema::table('payments', function (Blueprint $table) {
            $table->uuid('group')->nullable();
        });

        // Fill for existing groups
        DB::table('groups')->get()->each(function ($group) {
            $uuid = Str::orderedUuid();

            Payment::where('group_id', $group->id)->update([
                'group' => $uuid,
            ]);
        });

        // Update for existing non-groupped payments
        Payment::whereNull('group')->get()->each(function (Payment $payment) {
            $uuid = Str::orderedUuid();

            $payment->forceFill([
                'group' => $uuid,
            ]);
            $payment->save();
        });

        // Make column not nullable
        Schema::table('payments', function (Blueprint $table) {
            $table->uuid('group')->nullable(false)->change();
        });

        // Drop group_id column
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign('payments_group_id_foreign');
            $table->dropColumn('group_id');
        });

        // Drop groups table
        Schema::drop('groups');
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
