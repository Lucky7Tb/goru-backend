<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LessonSubjectSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("lesson_subjects")->insert([
            [
                "id" => Str::uuid(),
                "name" => "Matematika",
                "description" => "Mata pelajaran matematika",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "id" => Str::uuid(),
                "name" => "Bahasa Inggris",
                "description" => "Mata pelajaran bahasa inggris",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "id" => Str::uuid(),
                "name" => "Ilmu Pengetahuan Alam",
                "description" => "Mata pelajaran Ilmu Pengetahuan Alam",
                "created_at" => now(),
                "updated_at" => now()
            ],
        ]);
    }
}
