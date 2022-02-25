<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeacherPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teacher_packages', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("user_id")
                ->constrained()
                ->restrictOnDelete()
                ->cascadeOnUpdate();
            $table->enum("package", ["per_day", "per_week", "per_month"]);
            $table->integer("price_per_hour");
            $table->tinyInteger("encounter")->default(1);
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
        Schema::dropIfExists('teacher_packages');
    }
};
