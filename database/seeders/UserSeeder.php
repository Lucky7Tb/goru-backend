<?php

namespace Database\Seeders;

use Illuminate\Container\Container;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Generator;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Container::getInstance()->make(Generator::class);

        DB::table("users")->insert([
            [
                "id" => Str::uuid(),
                "full_name" => $faker->name(),
                "phone_number" => trim($faker->numerify('##########')),
                "email" => "teacher@mail.com",
                "role" => "teacher",
                "password" => bcrypt("password123"),
                "created_at" => now("Asia/Jakarta"),
                "updated_at" => now("Asia/Jakarta")
            ],
            [
                "id" => Str::uuid(),
                "full_name" => $faker->name(),
                "phone_number" => trim($faker->numerify('##########')),
                "email" => "student@mail.com",
                "role" => "student",
                "password" => bcrypt("password123"),
                "created_at" => now("Asia/Jakarta"),
                "updated_at" => now("Asia/Jakarta")
            ],
            [
                "id" => Str::uuid(),
                "full_name" => "admin",
                "phone_number" => "088888888",
                "email" => "admin@mail.com",
                "role" => "admin",
                "password" => bcrypt("password123"),
                "created_at" => now("Asia/Jakarta"),
                "updated_at" => now("Asia/Jakarta")
            ]
        ]);
    }
}
