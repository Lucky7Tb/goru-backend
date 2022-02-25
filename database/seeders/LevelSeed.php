<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LevelSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("levels")->insert([
            [
                "id" => Str::uuid(),
                "name" => "SD",
                "description" => "Level mengajar guru untuk anak Sekolah Dasar",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "id" => Str::uuid(),
                "name" => "SMP",
                "description" => "Level mengajar guru untuk anak Sekolah Menengah Pertama",
                "created_at" => now(),
                "updated_at" => now()
            ],
            [
                "id" => Str::uuid(),
                "name" => "SMA",
                "description" => "Level mengajar guru untuk anak Sekolah Menengah Atas",
                "created_at" => now(),
                "updated_at" => now()
            ]
        ]);
    }
}
