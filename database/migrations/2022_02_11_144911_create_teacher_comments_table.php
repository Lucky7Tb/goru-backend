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
        Schema::create('teacher_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignUuid('student_id')
                ->constrained("users")
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->foreignUuid('teacher_id')
                ->constrained("users")
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string("comment", 100);
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
        Schema::dropIfExists('teacher_comments');
    }
};
