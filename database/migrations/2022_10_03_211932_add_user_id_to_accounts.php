<?php

use App\AccountModule\Models\Account;
use App\UserModule\Models\User;
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
        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->after('updated_at')->nullable()->constrained();
        });

        Account::whereNull('user_id')->update([
            'user_id' => 1,
        ]);

        Schema::table('accounts', function (Blueprint $table) {
            $table->foreignIdFor(User::class)->after('updated_at')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('accounts', function (Blueprint $table) {
            $table->dropColumn(['user_id']);
        });
    }
};
