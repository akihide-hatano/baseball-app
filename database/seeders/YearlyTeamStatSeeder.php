<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\YearlyTeamStat;
use Illuminate\Support\Facades\DB;

class YearlyTeamStatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('チーム年度別成績データの生成を開始します...');

        YearlyTeamStat::truncate();

        $teams = Team::all();
        if ($teams->isEmpty()) {
            $this->command->warn('チームデータがありません。先にTeamSeederを実行してください。');
            return;
        }

        $faker = \Faker\Factory::create('ja_JP');

        $leagueResults = [
            'リーグ優勝', 'Aクラス(2位)', 'Aクラス(3位)', 'Bクラス(4位)', 'Bクラス(5位)', 'Bクラス(6位)'
        ];
        $postseasonResults = [
            '日本一', 'CS突破・日本シリーズ敗退', 'CSファイナルステージ敗退', 'CSファーストステージ敗退', 'CS出場なし'
        ];

        foreach ($teams as $team) {
            foreach ([2023, 2024] as $year) {
                $wins = $faker->numberBetween(50, 90);
                $losses = $faker->numberBetween(45, 80);
                $draws = $faker->numberBetween(0, 10);

                $totalGames = $wins + $losses + $draws;
                $winningPercentage = ($totalGames > 0) ? round($wins / ($wins + $losses), 3) : 0.000;

                $rank = $faker->numberBetween(1, 6);

                $chosenLeagueResult = $faker->randomElement($leagueResults);
                $chosenPostseasonResult = $faker->randomElement($postseasonResults);

                YearlyTeamStat::create([
                    'team_id'            => $team->id,
                    'year'               => $year,
                    'wins'               => $wins,
                    'losses'             => $losses,
                    'draws'              => $draws,
                    'winning_percentage' => $winningPercentage,
                    'games_behind'       => $faker->numberBetween(0, 30),
                    'rank'               => $rank,
                    'league_result'      => $chosenLeagueResult, // これはそのまま残す
                    'postseason_result'  => $chosenPostseasonResult, // これはそのまま残す
                ]);
            }
        }
        $this->command->info('チーム年度別成績データの生成が完了しました。');
    }
}