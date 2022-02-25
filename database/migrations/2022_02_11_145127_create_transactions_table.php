<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("student_id")
                ->constrained("users")
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignUuid("teacher_id")
                ->constrained("users")
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignUuid("teacher_package_id")
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignId("schedule_id")
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignUuid("application_bank_account_id")
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->integer("price_per_hour");
            $table->integer("total_price");
            $table->string("bank_name", 80)->nullable();
            $table->string("bank_sender_alias", 80)->nullable();
            $table->enum("status", ["not_paid_yet", "paid", "trouble", "cancelled"])->default("not_paid_yet");
            $table->string("evidance", 100)->nullable();
            $table->string("note_evidance", 100)->nullable();
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
        Schema::dropIfExists('transactions');
    }
};
