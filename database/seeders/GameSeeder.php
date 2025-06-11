<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Team; // Teamモデルを使用
use App\Models\Game; // Gameモデルを使用

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $teams = Team::all();
        if ($teams->isEmpty()) {
            $this->command->warn('チームデータがありません。先にTeamSeederを実行してください。');
            return;
        }

        $faker = \Faker\Factory::create('ja_JP');

        // 試合数を指定 (例: 300試合)
        $numberOfGamesToCreate = 300;

        // 試合が行われる年の範囲
        $startYear = 2023;
        $endYear = 2024;

        for ($i = 0; $i < $numberOfGamesToCreate; $i++) {
            // ランダムな2つの異なるチームを選択
            $homeTeam = $faker->randomElement($teams);
            $awayTeam = $faker->randomElement($teams->except($homeTeam->id)); // ホームチーム以外のチームを選択

            // 同じリーグのチーム同士で対戦するように調整することも可能ですが、
            // 今回はシンプルに全てのチームからランダムに選択します。
            // 必要であれば、ここで $homeTeam->league_id と $awayTeam->league_id を比較するロジックを追加できます。

            // ランダムな日付と時刻を生成
            $gameDate = $faker->dateTimeBetween($startYear . '-03-01', $endYear . '-10-31'); // シーズン期間
            $gameTime = $faker->dateTimeBetween('17:00:00', '21:00:00')->format('H:i:s');
            $fullDateTime = $gameDate->format('Y-m-d') . ' ' . $gameTime;

            // ランダムなスコアを生成 (現実的な範囲で)
            $homeScore = $faker->numberBetween(0, 15);
            $awayScore = $faker->numberBetween(0, 15);

            // ゲーム結果
            $gameResult = ($homeScore > $awayScore) ? 'Home Win' : (($awayScore > $homeScore) ? 'Away Win' : 'Draw');
            if ($homeScore == $awayScore && $gameDate->format('m-d') >= '07-01' && $gameDate->format('m-d') <= '09-30') {
                // 交流戦以外の中盤戦以降は引き分けが多いため、引き分けも考慮
                // プロ野球の規定に基づき延長戦なしで引き分けになることもあるため
            }


            // 架空のスタジアム名
            $stadiums = [
                '東京ドーム', '明治神宮野球場', '横浜スタジアム', 'バンテリンドーム ナゴヤ',
                '京セラドーム大阪', '阪神甲子園球場', '福岡PayPayドーム', 'エスコンフィールドHOKKAIDO',
                '楽天モバイルパーク宮城', 'ベルーナドーム', 'ZOZOマリンスタジアム', 'ほっともっとフィールド神戸',
            ];
            $stadium = $faker->randomElement($stadiums);

            Game::create([
                'home_team_id' => $homeTeam->id,
                'away_team_id' => $awayTeam->id,
                'game_date'    => $gameDate->format('Y-m-d'),
                'game_time'    => $gameTime,
                'stadium'      => $stadium,
                'home_score'   => $homeScore,
                'away_score'   => $awayScore,
                'game_result'  => $gameResult, // 'Home Win', 'Away Win', 'Draw'
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        $this->command->info($numberOfGamesToCreate . '件の試合データを作成しました。');
    }
}