<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // DBファサードを使用

class LeagueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('leagues')->insert([
            ['name' => 'セントラル・リーグ', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'パシフィック・リーグ', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}