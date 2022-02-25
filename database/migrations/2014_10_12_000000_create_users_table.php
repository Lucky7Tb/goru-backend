<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->string('full_name', 100);
            $table->string('email', 100)->unique();
            $table->string('phone_number', 15)->unique();
            $table->enum("role", ["teacher", "student", "admin"]);
            $table->string('photo_profile', 50)->nullable();
            $table->string('identity_photo', 50)->nullable();
            $table->tinyInteger('is_ban')->default(0);
            $table->tinyInteger('is_recommended')->default(0);
            $table->string('password');
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
