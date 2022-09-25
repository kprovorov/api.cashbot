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
        Schema::table('payments', function (Blueprint $table) {
            $table->bigInteger('original_amount')->nullable()->after('amount');
        });

        Payment::query()->update(['original_amount' => DB::raw('amount')]);

        Schema::table('payments', function (Blueprint $table) {
            $table->bigInteger('original_amount')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn('original_amount');
        });
    }
};
