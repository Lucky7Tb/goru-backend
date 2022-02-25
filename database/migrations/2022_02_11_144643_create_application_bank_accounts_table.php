<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_bank_accounts', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string("name", "50");
            $table->char("number", 20);
            $table->string("alias", 50);
            $table->string("bank_logo", 50);
            $table->boolean("is_active");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_bank_accounts');
    }
};
