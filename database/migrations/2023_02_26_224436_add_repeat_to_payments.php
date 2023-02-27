<?php

use App\Enums\RepeatUnit;
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
            $table->string('repeat_unit')->default(RepeatUnit::NONE->value);
            $table->integer('repeat_interval')->default(1);
            $table->date('repeat_ends_on')->nullable();
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
            $table->dropColumn('repeat_unit');
            $table->dropColumn('repeat_interval');
            $table->dropColumn('repeat_ends_on');
        });
    }
};
