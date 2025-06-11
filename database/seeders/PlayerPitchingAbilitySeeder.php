<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\Position;
use App\Models\PlayerPitchingAbility;
use Illuminate\Support\Facades\DB;

class PlayerPitchingAbilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('選手投球能力データの生成を開始します...');

        PlayerPitchingAbility::truncate();

        $pitcherPosition = Position::where('name', '投手')->first();
        if (!$pitcherPosition) {
            $this->command->warn('ポジションデータに「投手」がありません。PositionSeederを確認してください。');
            return;
        }
        $pitcherPositionId = $pitcherPosition->id;

        $pitchers = Player::whereHas('positions', function ($query) use ($pitcherPositionId) {
            $query->where('position_id', $pitcherPositionId);
        })->get();

        if ($pitchers->isEmpty()) {
            $this->command->warn('「投手」ポジションを持つ選手データがありません。PlayerSeederまたはPlayerPositionSeederを確認してください。');
            return;
        }

        $faker = \Faker\Factory::create('ja_JP');

        // 変化球の種類候補 (ストレートは含めない)
        $pitchTypes = [
            'フォーク', 'カーブ', 'スライダー', 'チェンジアップ',
            'シュート', 'シンカー', 'カットボール', 'ツーシーム', 'SFF', 'ナックル'
        ];

        // 特殊能力の候補 (投手向け)
        $specialSkills = [
            'ノビB', 'キレB', 'クイックB', '牽制○', '打たれ強さB', '対ピンチA',
            '安定度B', '重い球', '軽い球', '勝ち運', '負け運', 'リリース○',
            '逃げ球', '奪三振', '低め○', '尻上がり', '根性○', 'クロスファイヤー',
            '対強打者○', '回復B', '鉄腕'
        ];

        $currentYear = 2024;

        foreach ($pitchers as $pitcher) {
            $pitchControl = $faker->numberBetween(50, 95); // 制球力
            $pitchStamina = $faker->numberBetween(50, 95); // スタミナ
            $averageVelocity = $faker->randomFloat(1, 142.0, 160.0); // 平均球速

            // 変化球の種類とレベル (1〜5個、レベルは1〜7、ストレートは含まない)
            $pitchTypeData = [];
            // ランダムに1〜5個の変化球を選択
            $numberOfPitchesToSelect = $faker->numberBetween(1, min(5, count($pitchTypes))); // 1〜5個、かつ実際の変化球の種類数を超えないように
            $selectedPitchTypes = $faker->randomElements($pitchTypes, $numberOfPitchesToSelect);

            foreach ($selectedPitchTypes as $index => $pitchName) {
                // pitch_type_1, pitch_type_2... に割り当てる
                $pitchTypeData['pitch_type_' . ($index + 1)] = $pitchName . ':' . $faker->numberBetween(1, 7);
            }

            // 残りのpitch_type_カラムをnullで埋める (最大5種類)
            for ($i = count($pitchTypeData); $i < 5; $i++) {
                $pitchTypeData['pitch_type_' . ($i + 1)] = null;
            }

            $selectedSkills = $faker->randomElements($specialSkills, $faker->numberBetween(2, 5));
            $skillsString = implode(', ', $selectedSkills);

            $overallRank = round(($pitchControl + $pitchStamina + ($averageVelocity / 1.6)) / 3);
            if ($overallRank > 100) $overallRank = 100;
            if ($overallRank < 1) $overallRank = 1;

            PlayerPitchingAbility::create(array_merge([
                'player_id'        => $pitcher->id,
                'year'             => $currentYear,
                'pitch_control'    => $pitchControl,
                'pitch_stamina'    => $pitchStamina,
                'average_velocity' => $averageVelocity,
                'overall_rank'     => $overallRank,
                'special_skills'   => $skillsString,
            ], $pitchTypeData)); // 変化球データも結合して保存
        }
        $this->command->info('選手投球能力データの生成が完了しました。');
    }
}