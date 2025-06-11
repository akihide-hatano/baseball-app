<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('positions')->insert([
            ['name' => '投手', 'is_pitcher' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '捕手', 'is_pitcher' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '一塁手', 'is_pitcher' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '二塁手', 'is_pitcher' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '三塁手', 'is_pitcher' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '遊撃手', 'is_pitcher' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '左翼手', 'is_pitcher' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '中堅手', 'is_pitcher' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '右翼手', 'is_pitcher' => false, 'created_at' => now(), 'updated_at' => now()],
            ['name' => '指名打者', 'is_pitcher' => false, 'created_at' => now(), 'updated_at' => now()], // パ・リーグ用
        ]);
    }
}