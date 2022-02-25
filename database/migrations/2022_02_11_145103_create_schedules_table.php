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
        Schema::create('schedules', function (Blueprint $table) {
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
            $table->date("from_date");
            $table->date("to_date");
            $table->enum("status", ["in_review", "accepted", "rejected", "cancelled"])->default("in_review");
            $table->string("note", 100)->nullable();
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
        Schema::dropIfExists('schedules');
    }
};
