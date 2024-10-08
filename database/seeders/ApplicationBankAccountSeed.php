<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ApplicationBankAccountSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table("application_bank_accounts")->insert([
            [
                "id" => Str::uuid(),
                "name" => "BRI",
                "number" => Crypt::encryptString("08808993970968"),
                "alias" => "goru",
                "bank_logo" => "BRI.png",
                "is_active" => true,
                "created_at" => now(),
                "updated_at" => now()
            ]
        ]);
    }
}
