<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_levels', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("level_id")
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignUuid("user_id")
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();
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
        Schema::dropIfExists('teacher_levels');
    }
};
