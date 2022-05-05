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
                "name" => "IPS",
                "description" => "Mata pelajaran IPS",
                "thumbnail" => "Tj6dyCIdoESo6oBB.png",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "id" => Str::uuid(),
                "name" => "Bahasa Inggris",
                "description" => "Mata pelajaran bahasa inggris",
                "thumbnail" => "4PamZf0L1LBaGnsq.png",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "id" => Str::uuid(),
                "name" => "IPA",
                "description" => "Mata pelajaran IPA",
                "thumbnail" => "vrz4ztXMNP6vTz6E.png",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "id" => Str::uuid(),
                "name" => "Indonesia",
                "description" => "Mata pelajaran indonesia",
                "thumbnail" => "eG2reWwicdHyfdnj.png",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "id" => Str::uuid(),
                "name" => "Fisika",
                "description" => "Mata pelajaran fisika",
                "thumbnail" => "TCL8yIP6qUNSqQ6z.png",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "id" => Str::uuid(),
                "name" => "Matematika",
                "description" => "Mata pelajaran matematika",
                "thumbnail" => "plCDpUFvLPHR2qPs.png",
                "created_at" => now(),
                "updated_at" => now()
            ],
        ]);
    }
}
