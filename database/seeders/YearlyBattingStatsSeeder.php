<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\GamePlayerStat;
use App\Models\YearlyBattingStat; // 新しく作成するモデル
use Illuminate\Support\Facades\DB;

class YearlyBattingStatsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('年度別打撃成績データの生成を開始します...');

        // 既存のデータをクリア
        YearlyBattingStat::truncate();

        // 試合データが存在するか確認
        if (GamePlayerStat::count() === 0) {
            $this->command->warn('GamePlayerStatデータがありません。先にGamePlayerStatSeederを実行してください。');
            return;
        }

        // 年度と選手ごとに打撃成績を集計
        $battingStats = GamePlayerStat::select(
                // PostgreSQL向けに YEAR() から EXTRACT(YEAR FROM ...) に修正
                DB::raw('EXTRACT(YEAR FROM games.game_date) as year'), // 試合の日付から年を抽出
                'game_player_stats.player_id',
                'players.team_id', // 選手の現在のチームID (厳密にはその年の所属チームだが、シーダーでは現在のチームを使う)
                DB::raw('COUNT(DISTINCT game_player_stats.game_id) as games'), // 出場試合数
                DB::raw('SUM(game_player_stats.plate_appearances) as plate_appearances'),
                DB::raw('SUM(game_player_stats.at_bats) as at_bats'),
                DB::raw('SUM(game_player_stats.hits) as hits'),
                DB::raw('SUM(game_player_stats.doubles) as doubles'),
                DB::raw('SUM(game_player_stats.triples) as triples'),
                DB::raw('SUM(game_player_stats.home_runs) as home_runs'),
                DB::raw('SUM(game_player_stats.rbi) as rbi'),
                DB::raw('SUM(game_player_stats.stolen_bases) as stolen_bases'),
                DB::raw('SUM(game_player_stats.caught_stealing) as caught_stealing'),
                DB::raw('SUM(game_player_stats.strikeouts) as strikeouts'),
                DB::raw('SUM(game_player_stats.walks) as walks'),
                DB::raw('SUM(game_player_stats.hit_by_pitch) as hit_by_pitch'),
                DB::raw('SUM(game_player_stats.sac_bunts) as sac_bunts'),
                DB::raw('SUM(game_player_stats.sac_flies) as sac_flies'),
                DB::raw('SUM(game_player_stats.double_plays) as double_plays'),
                DB::raw('SUM(game_player_stats.errors) as errors'),
                DB::raw('SUM(game_player_stats.runs_scored) as runs_scored')
            )
            ->join('games', 'game_player_stats.game_id', '=', 'games.id')
            ->join('players', 'game_player_stats.player_id', '=', 'players.id') // 選手のteam_idを取得するため
            // PostgreSQL向けに YEAR() から EXTRACT(YEAR FROM ...) に修正
            ->groupBy(DB::raw('EXTRACT(YEAR FROM games.game_date)'), 'game_player_stats.player_id', 'players.team_id') // team_idもグループ化に追加
            ->havingRaw('SUM(plate_appearances) > 0') // 打席が0の選手は除外（打撃成績がない選手）
            ->get();

        foreach ($battingStats as $stat) {
            // 打率の計算 (打数0の場合は.000)
            $battingAverage = ($stat->at_bats > 0) ? round($stat->hits / $stat->at_bats, 3) : 0.000;

            // 出塁率の計算
            // 分母: 打数 + 四球 + 死球 + 犠飛 (At Bats + Walks + Hit by Pitch + Sacrifice Flies)
            $onBasePercentageDenominator = $stat->at_bats + $stat->walks + $stat->hit_by_pitch + $stat->sac_flies;
            $onBasePercentage = ($onBasePercentageDenominator > 0) ? round(($stat->hits + $stat->walks + $stat->hit_by_pitch) / $onBasePercentageDenominator, 3) : 0.000;

            // 長打率の計算 (塁打数 / 打数)
            // 塁打数 = 単打 + 2*二塁打 + 3*三塁打 + 4*本塁打
            $totalBases = $stat->hits + $stat->doubles + (2 * $stat->triples) + (3 * $stat->home_runs);
            $sluggingPercentage = ($stat->at_bats > 0) ? round($totalBases / $stat->at_bats, 3) : 0.000;

            // OPSの計算 (出塁率 + 長打率)
            $ops = round($onBasePercentage + $sluggingPercentage, 3);

            // OPS+とwRC+はリーグ平均が必要なので、ここではnullまたはデフォルト値
            // より高度な集計時に計算することを想定
            $opsPlus = null;
            $wrcPlus = null;

            YearlyBattingStat::create([
                'player_id'         => $stat->player_id,
                'year'              => $stat->year,
                'team_id'           => $stat->team_id,
                'games'             => $stat->games,
                'plate_appearances' => $stat->plate_appearances,
                'at_bats'           => $stat->at_bats,
                'hits'              => $stat->hits,
                'doubles'           => $stat->doubles,
                'triples'           => $stat->triples,
                'home_runs'         => $stat->home_runs,
                'rbi'               => $stat->rbi,
                'stolen_bases'      => $stat->stolen_bases,
                'caught_stealing'   => $stat->caught_stealing,
                'strikeouts'        => $stat->strikeouts,
                'walks'             => $stat->walks,
                'hit_by_pitch'      => $stat->hit_by_pitch,
                'sac_bunts'         => $stat->sac_bunts,
                'sac_flies'         => $stat->sac_flies,
                'double_plays'      => $stat->double_plays,
                'errors'            => $stat->errors,
                'runs_scored'       => $stat->runs_scored,
                'batting_average'   => $battingAverage,
                'on_base_percentage'=> $onBasePercentage,
                'slugging_percentage'=> $sluggingPercentage,
                'ops'               => $ops,
                'ops_plus'          => $opsPlus,
                'wrc_plus'          => $wrcPlus,
            ]);
        }
        $this->command->info('年度別打撃成績データの生成が完了しました。');
    }
}
