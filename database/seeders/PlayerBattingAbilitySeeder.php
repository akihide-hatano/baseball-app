<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\PlayerBattingAbility;
use Illuminate\Support\Facades\DB;

class PlayerBattingAbilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('選手打撃能力データの生成を開始します...');

        PlayerBattingAbility::truncate();

        $players = Player::all();
        if ($players->isEmpty()) {
            $this->command->warn('選手データがありません。先にPlayerSeederを実行してください。');
            return;
        }

        $faker = \Faker\Factory::create('ja_JP');

        $specialSkills = [
            'アベレージヒッター', 'パワーヒッター', '広角打法', 'プルヒッター', '流し打ち',
            '走塁B', '盗塁B', '守備職人', '送球B', 'レーザービーム', 'キャッチャーA', '代打○',
            '満塁男', '固め打ち', '粘り打ち', '初球○', 'サヨナラ男', '逆境○', 'チャンスA',
            '窮地○', '対左投手A', '対右投手A', '対エース○', 'ムード○', 'バント職人', '内野安打○'
        ];

        // 2024年の能力のみを生成するように変更
        $currentYear = 2024;

        foreach ($players as $player) {
            $contactPower = $faker->numberBetween(50, 95);
            $power        = $faker->numberBetween(50, 95);
            $speed        = $faker->numberBetween(50, 95);
            $fielding     = $faker->numberBetween(50, 95);
            $throwing     = $faker->numberBetween(50, 95);
            $reaction     = $faker->numberBetween(50, 95);

            $selectedSkills = $faker->randomElements($specialSkills, $faker->numberBetween(2, 5));
            $skillsString = implode(', ', $selectedSkills);

            $averageAbility = ($contactPower + $power + $speed + $fielding + $throwing + $reaction) / 6;
            $overallRank = round($averageAbility);

            PlayerBattingAbility::create([
                'player_id'      => $player->id,
                'year'           => $currentYear, // ここを修正
                'contact_power'  => $contactPower,
                'power'          => $power,
                'speed'          => $speed,
                'fielding'       => $fielding,
                'throwing'       => $throwing,
                'reaction'       => $reaction,
                'overall_rank'   => $overallRank,
                'special_skills' => $skillsString,
            ]);
        }
        $this->command->info('選手打撃能力データの生成が完了しました。');
    }
}