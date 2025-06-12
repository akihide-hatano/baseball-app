<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\Position;
use App\Models\PlayerPitchingAbility;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PlayerPitchingAbilitySeeder extends Seeder
{
    const MIN_VELOCITY = 100.0; // 仮の最小球速 (km/h)
    const MAX_VELOCITY = 160.0; // 仮の最大球速 (km/h)

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('選手投球能力データの生成を開始します...');

        PlayerPitchingAbility::truncate();

        $pitchers = Player::where('role', '投手')->get();

        if ($pitchers->isEmpty()) {
            $this->command->warn('「投手」ロールを持つ選手データがありません。PlayerSeederを確認してください。');
            return;
        }

        $faker = Faker::create('ja_JP');

        $allPitchTypes = [
            'カーブ', 'スライダー', 'フォーク', 'チェンジアップ',
            'シュート', 'カットボール', 'シンカー'
        ];

        $specialSkills = [
            'ノビB', 'キレB', 'クイックB', '牽制○', '打たれ強さB', '対ピンチA',
            '安定度B', '重い球', '軽い球', '勝ち運', '負け運', 'リリース○',
            '逃げ球', '奪三振', '低め○', '尻上がり', '根性○', 'クロスファイヤー',
            '対強打者○', '回復B', '鉄腕'
        ];

        $currentYear = 2024;

        foreach ($pitchers as $pitcher) {
            $pitchControl = $faker->numberBetween(50, 99); // 制球力
            $pitchStamina = $faker->numberBetween(50, 99); // スタミナ
            $averageVelocity = $faker->randomFloat(1, 130.0, 158.0); // 平均球速 (広めに設定)

            $pitchTypeData = []; // この変数は使わないので削除可能ですが、ここでは残します

            // ランダムに選択された球種にレベルを付与 (0〜7)
            // 選択する球種の数は、全種類からランダムに1〜5個
            $numberOfPitchesToSelect = $faker->numberBetween(1, min(count($allPitchTypes), 5)); 
            $selectedPitchTypes = $faker->randomElements($allPitchTypes, $numberOfPitchesToSelect);

            $assignedPitches = [];
            foreach ($selectedPitchTypes as $pitchName) {
                $assignedPitches[$pitchName] = $faker->numberBetween(0, 7);
            }

            // データベース保存用の最終的な pitchTypeData を構築
            // すべての $allPitchTypes に対応するカラムを埋めるように修正
            $finalPitchTypeData = [];
            // $allPitchTypes の数だけループを回す
            foreach ($allPitchTypes as $index => $pitchName) {
                // pitch_type_1 から始まるカラム名を作成
                $columnName = 'pitch_type_' . ($index + 1);
                // データベースのテーブルにこのカラムが存在するかどうか確認
                // (マイグレーションで pitch_type_7 まで定義されていることを前提とします)

                // 割り当てられた球種であればそのレベル、なければ0をセット
                $level = $assignedPitches[$pitchName] ?? 0;
                $finalPitchTypeData[$columnName] = $pitchName . ':' . $level;
            }

            // もしデータベースカラムが pitch_type_5 までしかない場合を考慮し、
            // $finalPitchTypeData から余分なエントリを削除するか、
            // マイグレーションを更新して pitch_type_7 まで追加してください。
            // ここでは、マイグレーションが適切に更新されていると仮定します。
            // 例: PlayerPitchingAbility モデルの $fillable に pitch_type_6, pitch_type_7 があるか、
            // テーブルに pitch_type_6, pitch_type_7 カラムが存在するか。

            $selectedSkills = $faker->randomElements($specialSkills, $faker->numberBetween(1, 3));
            $skillsString = implode(', ', array_unique($selectedSkills));
            if (empty($skillsString)) {
                $skillsString = null;
            }

            $overallRank = round(($pitchControl + $pitchStamina + (($averageVelocity - self::MIN_VELOCITY) / (self::MAX_VELOCITY - self::MIN_VELOCITY) * 100)) / 3);
            $overallRank = max(1, min(99, $overallRank));

            PlayerPitchingAbility::create(array_merge([
                'player_id'        => $pitcher->id,
                'year'             => $currentYear,
                'pitch_control'    => $pitchControl,
                'pitch_stamina'    => $pitchStamina,
                'average_velocity' => $averageVelocity,
                'overall_rank'     => $overallRank,
                'special_skills'   => $skillsString,
            ], $finalPitchTypeData)); // 修正後の$finalPitchTypeDataを使用
        }
        $this->command->info('選手投球能力データの生成が完了しました。');
    }
}