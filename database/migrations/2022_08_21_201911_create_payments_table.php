<?php

use App\AccountModule\Models\Account;
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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignIdFor(Account::class, 'account_from_id')->nullable()->constrained('accounts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignIdFor(Account::class, 'account_to_id')->nullable()->constrained('accounts')->cascadeOnDelete()->cascadeOnUpdate();
            $table->uuid('group');
            $table->string('description');
            $table->date('date');
            $table->bigInteger('amount');
            $table->string('currency');
            $table->bigInteger('amount_from_converted')->nullable();
            $table->bigInteger('amount_to_converted')->nullable();
            $table->boolean('auto_apply')->default(false);
            $table->timestamp('applied_at')->nullable();
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
        Schema::dropIfExists('payments');
    }
};
