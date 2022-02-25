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
        Schema::create('teacher_comment_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId("teacher_comment_id")
                ->constrained()
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
            $table->string("comment_reply", 100);
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
        Schema::dropIfExists('teacher_comment_replies');
    }
};
