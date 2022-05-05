<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $teacherId = Str::uuid();

        DB::table("users")->insert([
            [
                "id" => Str::uuid(),
                "full_name" => "Admin goru",
                "phone_number" => "08993970968",
                "email" => "admin@mail.com",
                "role" => "admin",
                "password" => bcrypt("password123"),
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "id" => Str::uuid(),
                "full_name" => "student",
                "phone_number" => "081298129119",
                "email" => "student@mail.com",
                "role" => "student",
                "password" => bcrypt("password123"),
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "id" => $teacherId,
                "full_name" => "Teacher",
                "phone_number" => "128192891111",
                "email" => "teacher@mail.com",
                "role" => "teacher",
                "password" => bcrypt("password123"),
                "created_at" => now(),
                "updated_at" => now()
            ],
        ]);

        DB::table("teacher_packages")->insert([
            [
                'id' => Str::uuid(),
                "user_id" => $teacherId,
                "package" => "per_day",
                "price_per_hour" => 50000,
                "created_at" => now(),
                "updated_at" => now()
            ]
        ]);

        DB::table("teacher_lesson_subjects")->insert([
            [
                "id" => Str::uuid(),
                "lesson_subject_id" => DB::table('lesson_subjects')->select('id')->first()->id,
                "user_id" => $teacherId,
                "created_at" => now(),
                "updated_at" => now()
            ]
        ]);

        DB::table("teacher_levels")->insert([
            [
                "id" => Str::uuid(),
                "level_id" => DB::table('levels')->select('id')->first()->id,
                "user_id" => $teacherId,
                "created_at" => now(),
                "updated_at" => now()
            ]
        ]);
    }
}
