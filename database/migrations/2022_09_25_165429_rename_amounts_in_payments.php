<?php

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
            $table->renameColumn('amount', 'amount_converted');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('original_amount', 'amount');
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
            $table->renameColumn('amount', 'original_amount');
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->renameColumn('amount_converted', 'amount');
        });
    }
};
