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
        Schema::table('schedule_details', function (Blueprint $table){
            $table->addColumn('string', 'meet_evidance')
                ->length(100)
                ->nullable();
            $table->addColumn('string', 'meet_link')
                ->length(100)
                ->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('schedule_details', function (Blueprint $table) {
            $table->dropColumn('meet_evidance');
            $table->dropColumn('meet_link');
        });
    }
};
