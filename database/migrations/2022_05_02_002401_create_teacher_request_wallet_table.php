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
        Schema::create('teacher_request_wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("user_id")
                ->constrained("users")
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string('bank_name');
            $table->text('bank_account_number');
            $table->bigInteger('request_ammount');
            $table->string('evidance', 100)->nullable();
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
        Schema::dropIfExists('teacher_request_wallets');
    }
};
