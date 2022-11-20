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
        Schema::table('transactions', function (Blueprint $table) {
            $table->text("sender_account_number")->after("note_evidance")->nullable();
            $table->string("sender_account_name")->after("sender_account_number")->length(100)->nullable();
            $table->string("sender_bank_name")->after("sender_account_name")->length(80)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn(['sender_account_number', 'sender_account_name', 'sender_bank_name']);
        });
    }
};
