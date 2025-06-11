<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\GamePlayerStat;
use App\Models\YearlyPitchingStat; // 新しく作成するモデル
use Illuminate\Support\Facades\DB;

class YearlyPitchingStatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('年度別投球成績データの生成を開始します...');

        // 既存のデータをクリア
        YearlyPitchingStat::truncate();

        // 試合データが存在するか確認
        if (GamePlayerStat::count() === 0) {
            $this->command->warn('GamePlayerStatデータがありません。先にGamePlayerStatSeederを実行してください。');
            return;
        }

        // 年度と選手ごとに投球成績を集計
        $pitchingStats = GamePlayerStat::select(
                DB::raw('EXTRACT(YEAR FROM games.game_date) as year'), // 試合の日付から年を抽出 (PostgreSQL互換)
                'game_player_stats.player_id',
                'players.team_id', // 選手の現在のチームID (厳密にはその年の所属チームだが、シーダーでは現在のチームを使う)
                DB::raw('COUNT(DISTINCT game_player_stats.game_id) as games'), // 登板試合数
                DB::raw('SUM(CASE WHEN game_player_stats.innings_pitched > 0 AND game_player_stats.is_starter = TRUE THEN 1 ELSE 0 END) as starts'), // 先発登板数
                DB::raw('SUM(CASE WHEN game_player_stats.is_winner_pitcher = TRUE THEN 1 ELSE 0 END) as wins'), // 勝利数
                DB::raw('SUM(CASE WHEN game_player_stats.is_loser_pitcher = TRUE THEN 1 ELSE 0 END) as losses'), // 敗北数
                DB::raw('SUM(CASE WHEN game_player_stats.is_save_pitcher = TRUE THEN 1 ELSE 0 END) as saves'), // セーブ数
                DB::raw('SUM(CASE WHEN game_player_stats.is_hold_pitcher = TRUE THEN 1 ELSE 0 END) as holds'), // ホールド数
                DB::raw('SUM(game_player_stats.innings_pitched) as innings_pitched_sum'), // 合計投球回 (計算用)
                DB::raw('SUM(game_player_stats.hits_allowed) as hits_allowed'),
                DB::raw('SUM(game_player_stats.home_runs_allowed) as home_runs_allowed'),
                DB::raw('SUM(game_player_stats.walks_allowed) as walks_allowed'),
                DB::raw('SUM(game_player_stats.hit_by_pitch) as hit_by_pitch_allowed'), // game_player_statsのhit_by_pitchは打者成績なので注意
                DB::raw('SUM(game_player_stats.strikeouts_pitched) as strikeouts_pitched'),
                DB::raw('SUM(game_player_stats.runs_allowed) as runs_allowed'),
                DB::raw('SUM(game_player_stats.earned_runs) as earned_runs'),
                DB::raw('SUM(game_player_stats.pitches_thrown) as pitches_thrown')
            )
            ->join('games', 'game_player_stats.game_id', '=', 'games.id')
            ->join('players', 'game_player_stats.player_id', '=', 'players.id') // 選手のteam_idを取得するため
            ->whereNotNull('game_player_stats.innings_pitched') // 投球記録がある選手のみを対象
            ->groupBy(DB::raw('EXTRACT(YEAR FROM games.game_date)'), 'game_player_stats.player_id', 'players.team_id')
            ->get();

        foreach ($pitchingStats as $stat) {
            // 防御率の計算 (自責点 * 9 / 投球回)
            // 投球回は小数部があるので、正確な計算のために整数とアウトカウントに分解する
            $fullInnings = floor($stat->innings_pitched_sum);
            $thirds = ($stat->innings_pitched_sum - $fullInnings) * 10; // 0.1 -> 1, 0.2 -> 2
            $totalOuts = ($fullInnings * 3) + $thirds;

            $earnedRunAverage = ($totalOuts > 0) ? round(($stat->earned_runs * 9) / $totalOuts, 2) : 0.00;

            // WHIPの計算 (与四球 + 被安打) / 投球回
            $whip = ($totalOuts > 0) ? round(($stat->walks_allowed + $stat->hits_allowed) / ($totalOuts / 3), 2) : 0.00;

            // K/BB (奪三振 / 与四球)
            $strikeoutWalkRatio = ($stat->walks_allowed > 0) ? round($stat->strikeouts_pitched / $stat->walks_allowed, 2) : ($stat->strikeouts_pitched > 0 ? $stat->strikeouts_pitched : 0.00); // 四球0で奪三振があれば奪三振数をそのまま、両方0なら0.00

            YearlyPitchingStat::create([
                'player_id'             => $stat->player_id,
                'year'                  => $stat->year,
                'team_id'               => $stat->team_id,
                'games'                 => $stat->games,
                'starts'                => $stat->starts,
                'wins'                  => $stat->wins,
                'losses'                => $stat->losses,
                'saves'                 => $stat->saves,
                'holds'                 => $stat->holds,
                'innings_pitched'       => $stat->innings_pitched_sum,
                'hits_allowed'          => $stat->hits_allowed,
                'home_runs_allowed'     => $stat->home_runs_allowed,
                'walks_allowed'         => $stat->walks_allowed,
                'hit_by_pitch_allowed'  => $stat->hit_by_pitch_allowed,
                'strikeouts_pitched'    => $stat->strikeouts_pitched,
                'runs_allowed'          => $stat->runs_allowed,
                'earned_runs'           => $stat->earned_runs,
                'pitches_thrown'        => $stat->pitches_thrown,
                'earned_run_average'    => $earnedRunAverage,
                'whip'                  => $whip,
                'strikeout_walk_ratio'  => $strikeoutWalkRatio,
            ]);
        }
        $this->command->info('年度別投球成績データの生成が完了しました。');
    }
}