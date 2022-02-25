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
        Schema::create('schedule_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId("schedule_id")
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->date("date");
            $table->time("from_time", 2);
            $table->time("to_time", 2);
            $table->enum("status", ["in_review", "accepted", "rejected", "cancelled"])->default("in_review");
            $table->string("note", 100);
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
        Schema::dropIfExists('schedule_details');
    }
};
