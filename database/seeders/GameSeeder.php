<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Team; // Teamモデルを使用
use App\Models\Game; // Gameモデルを使用
use Faker\Factory as Faker; // Faker をuseする

class GameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('試合データの生成を開始します...');

        Game::truncate(); // 既存のデータをクリア

        $teams = Team::all();
        if ($teams->isEmpty()) {
            $this->command->warn('チームデータがありません。先にTeamSeederを実行してください。');
            return;
        }

        $faker = Faker::create('ja_JP');

        // ★修正点：生成する試合数を増やす (1000〜1500試合)★
        $numberOfGamesToCreate = $faker->numberBetween(1000, 1500);


        // 試合が行われる年の範囲
        $startYear = 2023;
        $endYear = 2024;

        for ($i = 0; $i < $numberOfGamesToCreate; $i++) {
            // ランダムな2つの異なるチームを選択
            $homeTeam = $faker->randomElement($teams);
            $awayTeam = $faker->randomElement($teams->except($homeTeam->id));

            // ランダムな日付を生成（シーズン期間）
            $gameDate = $faker->dateTimeBetween($startYear . '-03-01', $endYear . '-10-31');
            // ★修正点：game_time を再生成★
            $gameTime = $faker->dateTimeBetween('17:00:00', '21:00:00')->format('H:i:s'); // 例: 17:00から21:00の間

            // ランダムなスコアを生成 (現実的な範囲で)
            $homeScore = $faker->numberBetween(0, 15);
            $awayScore = $faker->numberBetween(0, 15);

            // ゲーム結果 (シンプル化)
            $gameResult = ($homeScore > $awayScore) ? 'Home Win' : (($awayScore > $homeScore) ? 'Away Win' : 'Draw');

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
                'game_time'    => $gameTime, // ★ここを再度追加！★
                'stadium'      => $stadium,
                'home_score'   => $homeScore,
                'away_score'   => $awayScore,
                'game_result'  => $gameResult,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        $this->command->info($numberOfGamesToCreate . '件の試合データを作成しました。');
    }
}