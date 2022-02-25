<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid("student_id")
                ->constrained("users")
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->foreignUuid("teacher_id")
                ->constrained("users")
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->tinyInteger("rating")->default(1);
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
        Schema::dropIfExists('teacher_ratings');
    }
};
