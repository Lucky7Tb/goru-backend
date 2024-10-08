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
        Schema::table('teacher_packages', function(Blueprint $table){
            $table->addColumn('TINYINTEGER', 'is_active')->after('encounter')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('teacher_packages', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};
