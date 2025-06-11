<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\Team;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // まずは既存のチームが存在することを確認
        $teams = Team::all(); // 全チームを取得
        if ($teams->isEmpty()) {
            $this->command->warn('チームデータがありません。先にTeamSeederを実行してください。');
            return;
        }

        // 各チームの現在の背番号を追跡する配列を初期化
        // チームIDをキーとし、使用済みの背番号の配列を値とする
        $usedJerseyNumbers = [];
        foreach ($teams as $team) {
            $usedJerseyNumbers[$team->id] = Player::where('team_id', $team->id)
                                                    ->pluck('jersey_number')
                                                    ->filter(function ($number) { return $number !== null; }) // nullの背番号は除外
                                                    ->toArray();
        }

        // 野球選手らしい説明文の候補
        $baseballDescriptions = [
            '若きスラッガー、長打力と勝負強さが魅力。',
            '守備の要。堅実な守備と強肩でチームを支える。',
            'マウンドの支配者。切れ味鋭い変化球で打者を翻弄。',
            'チームを引っ張るキャプテン。攻守にわたる活躍で貢献。',
            '走攻守揃った万能プレイヤー。球界の未来を担う逸材。',
            '精密なコントロールで打者を抑える技巧派投手。',
            '力強いスイングでホームランを量産する大砲。',
            '俊足巧打のリードオフマン。出塁率の高さが光る。',
            '強気のピッチングが魅力のクローザー。セーブ王候補。',
            '泥臭いプレーもいとわない、チームのムードメーカー。',
            '正確な送球と抜群の判断力を持つ内野手。',
            'どんな球も打ち返す、バットコントロールの天才。',
            '三振の山を築く速球派。強打者も手玉に取る。',
            'ピンチに強いベテラン。経験でチームを救う。',
            '代打の切り札。ここぞの場面で結果を出す勝負師。',
            '伸び盛りの若手。無限の可能性を秘めた期待の星。',
            'ストレートに威力のある本格派右腕。奪三振も多い。',
            '変化球のコンビネーションで打者を打ち取る左腕。',
            '巧みなリードで投手を支える守備型捕手。',
            'チャンスに強く、一発で試合を決めるスラッガー。',
        ];

        // Fakerインスタンスを日本語ロケールで取得
        $faker = \Faker\Factory::create('ja_JP');

        // 150人の選手を生成
        $numberOfPlayersToCreate = 250;

        for ($i = 0; $i < $numberOfPlayersToCreate; $i++) {
            // ランダムなチームIDを選択
            $teamId = $faker->randomElement($teams->pluck('id')->toArray());

            // そのチームでまだ使われていないユニークな背番号を生成
            $jerseyNumber = null;
            $attempts = 0;
            do {
                $jerseyNumber = $faker->numberBetween(0, 99); // 00-99の範囲で背番号を生成
                $attempts++;
                if ($attempts > 250) { // 無限ループ回避のための安全装置
                    $jerseyNumber = null; // 背番号が見つからなかった場合はnullにする
                    break;
                }
            } while (in_array($jerseyNumber, $usedJerseyNumbers[$teamId]));

            // 生成した背番号を使用済みリストに追加
            if ($jerseyNumber !== null) {
                $usedJerseyNumbers[$teamId][] = $jerseyNumber;
            }

            // 選手データをファクトリで生成し、jersey_numberとdescriptionを上書きして保存
            Player::factory()->create([
                'team_id'       => $teamId,
                'jersey_number' => $jerseyNumber, // ここでユニークな背番号を割り当てる
                'name'          => $faker->name('male'), // Fakerインスタンスを直接使用
                'date_of_birth' => $faker->dateTimeBetween('-35 years', '-18 years')->format('Y-m-d'),
                'height'        => $faker->numberBetween(165, 195),
                'weight'        => $faker->numberBetween(65, 100),
                'specialty'     => $faker->randomElement(['速球派', '変化球派', '巧打者', '強打者', '俊足', '守備職人', 'オールラウンダー']),
                'description'   => $faker->randomElement($baseballDescriptions), // 野球選手らしい説明文を割り当てる
                'hometown'      => $faker->prefecture,
                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        $this->command->info($numberOfPlayersToCreate . '人の選手データを作成しました。');
    }
}