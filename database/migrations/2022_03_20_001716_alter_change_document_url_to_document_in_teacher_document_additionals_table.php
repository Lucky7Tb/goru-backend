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
        Schema::table('teacher_document_additionals', function (Blueprint $table) {
            $table->dropColumn('document_url');
            $table->string('document', 50);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teacher_document_additionals', function (Blueprint $table) {
            $table->dropColumn('document');
            $table->string('document_url', 50);
        });
    }
};
