<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherDocumentAdditionalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_document_additionals', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("user_id")
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->string("document_url", 50);
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
        Schema::dropIfExists('teacher_document_additionals');
    }
};
