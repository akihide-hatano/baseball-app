<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\PlayerBattingAbility;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker; // Fakerのuse文が不足していたので追加

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

        $faker = Faker::create('ja_JP');

        // 打撃・走塁系の特殊能力
        $battingRunningSkills = [
            'アベレージヒッター', 'パワーヒッター', '広角打法', 'プルヒッター', '流し打ち',
            '満塁男', '固め打ち', '粘り打ち', '初球○', 'サヨナラ男', '逆境○', 'チャンスA',
            '窮地○', '対左投手A', '対右投手A', '対エース○', 'バント職人', '内野安打○', '走塁B', '盗塁B'
        ];

        // 守備・送球系の特殊能力
        $fieldingThrowingSkills = [
            '守備職人', '送球B', 'レーザービーム', 'キャッチャーA'
        ];

        // その他、汎用的な特殊能力（役割を問わず付与されうるもの）
        $generalSkills = [
            'ムード○', '代打○', '積極打法', '慎重打法' // 例を追加
        ];

        $currentYear = 2024;

        foreach ($players as $player) {
            $contactPower = 0;
            $power        = 0;
            $speed        = 0;
            $fielding     = 0;
            $throwing     = 0;
            $reaction     = 0;
            $selectedSkills = []; // 選択された特殊能力を格納する配列

            // 選手の役割に応じて能力値を設定
            if ($player->role === '投手') {
                // 投手の打撃能力は非常に低く設定
                $contactPower = $faker->numberBetween(5, 30);  // ミート
                $power        = $faker->numberBetween(5, 30);  // パワー
                $speed        = $faker->numberBetween(10, 40); // 走力
                $fielding     = $faker->numberBetween(30, 60); // 守備力 (投手もフィールディングは必要)
                $throwing     = $faker->numberBetween(40, 70); // 肩力 (投手としての肩力はあるが、野手としての送球能力とは異なる)
                $reaction     = $faker->numberBetween(30, 60); // 反応 (バント処理など)

                // 投手に打撃・走塁系スキルはほとんど割り当てない（稀に隠し玉的に）
                if ($faker->boolean(10)) { // 10%の確率で1つだけ
                    $selectedSkills = array_merge($selectedSkills, $faker->randomElements($battingRunningSkills, 1));
                }
                // 守備系スキルは少しだけ（自身の守備位置に対するもの）
                if ($faker->boolean(20)) { // 20%の確率で1つだけ
                    $selectedSkills = array_merge($selectedSkills, $faker->randomElements($fieldingThrowingSkills, 1));
                }
                // 汎用スキルは少しだけ
                if ($faker->boolean(20)) { // 20%の確率で1つだけ
                    $selectedSkills = array_merge($selectedSkills, $faker->randomElements($generalSkills, 1));
                }

            } elseif ($player->role === 'レギュラー野手') {
                // レギュラー野手の打撃能力は高く設定
                $contactPower = $faker->numberBetween(70, 99);
                $power        = $faker->numberBetween(70, 99);
                $speed        = $faker->numberBetween(60, 95);
                $fielding     = $faker->numberBetween(70, 99);
                $throwing     = $faker->numberBetween(70, 99);
                $reaction     = $faker->numberBetween(70, 99);

                // レギュラー野手には多くの特殊能力を割り当てる
                $selectedSkills = array_merge(
                    $faker->randomElements($battingRunningSkills, $faker->numberBetween(2, 4)),
                    $faker->randomElements($fieldingThrowingSkills, $faker->numberBetween(1, 2)),
                    $faker->randomElements($generalSkills, $faker->numberBetween(0, 1))
                );

            } else { // 控え野手
                // 控え野手の打撃能力は中程度に設定
                $contactPower = $faker->numberBetween(40, 80);
                $power        = $faker->numberBetween(40, 80);
                $speed        = $faker->numberBetween(40, 85);
                $fielding     = $faker->numberBetween(40, 85);
                $throwing     = $faker->numberBetween(40, 85);
                $reaction     = $faker->numberBetween(40, 85);

                // 控え野手には中程度の特殊能力を割り当てる
                $selectedSkills = array_merge(
                    $faker->randomElements($battingRunningSkills, $faker->numberBetween(1, 3)),
                    $faker->randomElements($fieldingThrowingSkills, $faker->numberBetween(0, 1)),
                    $faker->randomElements($generalSkills, $faker->numberBetween(0, 1))
                );
            }

            // ユニークなスキルだけを結合して文字列にする
            $skillsString = implode(', ', array_unique($selectedSkills));
            if (empty($skillsString)) {
                $skillsString = null; // スキルがない場合はnullにする
            }


            $averageAbility = ($contactPower + $power + $speed + $fielding + $throwing + $reaction) / 6;
            $overallRank = round($averageAbility);

            PlayerBattingAbility::create([
                'player_id'      => $player->id,
                'year'           => $currentYear,
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