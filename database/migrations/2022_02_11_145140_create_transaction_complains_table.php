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
        Schema::create('transaction_complains', function (Blueprint $table) {
            $table->id();
            $table->foreignId("transaction_id")
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string("complain", 100);
            $table->enum("status", ["in_review", "open", "close"])->default("in_review");
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
        Schema::dropIfExists('transaction_complains');
    }
};
