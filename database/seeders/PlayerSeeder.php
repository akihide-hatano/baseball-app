<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Player;
use App\Models\Position;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 選手データと共にrole（役割）を生成します。
     */
    public function run(): void
    {
        $this->command->info('選手データの生成を開始します...');

        Player::truncate(); // 既存の選手データをクリア
        DB::table('player_position')->truncate(); // 中間テーブルもクリア

        $teams = Team::all();
        $positions = Position::all();

        if ($teams->isEmpty() || $positions->isEmpty()) {
            $this->command->warn('チームまたはポジションデータがありません。TeamSeederとPositionSeederを実行してください。');
            return;
        }

        $faker = Faker::create('ja_JP');

        foreach ($teams as $team) {
            // 各チームに約25〜30人の選手を生成
            for ($i = 0; $i < $faker->numberBetween(25, 30); $i++) {
                $randRole = $faker->numberBetween(1, 100);
                $role = '控え野手'; // デフォルト
                if ($randRole <= 30) {
                    $role = '投手';
                } elseif ($randRole <= 80) {
                    $role = 'レギュラー野手';
                }

                $player = Player::create([
                    'team_id' => $team->id,
                    'name' => $faker->name('male'), // ★ここを 'male' に修正★
                    'jersey_number' => $faker->unique()->numberBetween(0, 99),
                    'role' => $role,
                    'date_of_birth' => $faker->dateTimeBetween('-30 years', '-18 years')->format('Y-m-d'),
                    'height' => $faker->numberBetween(170, 195),
                    'weight' => $faker->numberBetween(70, 100),
                    'specialty' => $faker->randomElement(['右投右打', '右投左打', '左投左打', '左投右打', '両打']),
                    'description' => $faker->optional(0.5)->sentence,
                    'hometown' => $faker->city,
                ]);

                if ($role === '投手') {
                    $pitcherPos = $positions->where('name', '投手')->first();
                    if ($pitcherPos) {
                        $player->positions()->attach($pitcherPos->id);
                    }
                } else {
                    $randomPositions = $positions->where('name', '!=', '投手')->random($faker->numberBetween(1, 3));
                    foreach ($randomPositions as $pos) {
                        $player->positions()->attach($pos->id);
                    }
                }
            }
            $faker->unique(true);
        }

        $this->command->info('選手データの生成が完了しました。');
    }
}