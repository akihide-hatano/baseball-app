<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\Player;
use App\Models\Position;
use App\Models\GamePlayerStat;
use Illuminate\Support\Facades\DB;

class GamePlayerStatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('試合別選手成績データの生成を開始します...');

        GamePlayerStat::truncate(); // 既存のデータをクリア

        $games = Game::all();
        $players = Player::all();

        $pitcherPosition = Position::where('name', '投手')->first();
        if (!$pitcherPosition) {
            $this->command->warn('ポジションデータに「投手」がありません。PositionSeederを確認してください。');
            return;
        }
        $pitcherPositionId = $pitcherPosition->id;

        if ($games->isEmpty()) {
            $this->command->warn('試合データがありません。先にGameSeederを実行してください。');
            return;
        }
        if ($players->isEmpty()) {
            $this->command->warn('選手データがありません。先にPlayerSeederを実行してください。');
            return;
        }

        $faker = \Faker\Factory::create('ja_JP');

        foreach ($games as $game) {
            $homeTeamPlayers = $players->where('team_id', $game->home_team_id);
            $awayTeamPlayers = $players->where('team_id', $game->away_team_id);

            $playersInGame = collect([]);
            foreach ($homeTeamPlayers->merge($awayTeamPlayers) as $player) {
                if ($player->role === '投手') {
                    if ($faker->boolean(70)) { // 70%の確率で登板（試合に出る）
                        $playersInGame->push($player);
                    }
                } elseif ($player->role === 'レギュラー野手') {
                    if ($faker->boolean(95)) { // 95%の確率で出場
                        $playersInGame->push($player);
                    }
                } elseif ($player->role === '控え野手') {
                    if ($faker->boolean(40)) { // 40%の確率で出場
                        $playersInGame->push($player);
                    }
                }
            }

            foreach ($playersInGame as $player) {
                $isPitcher = ($player->role === '投手');

                // 投手の成績
                if ($isPitcher) {
                    $inningsPitched = $faker->randomFloat(1, 0.1, 9.0);
                    $earnedRuns = $faker->numberBetween(0, (int)($inningsPitched / 3) + 1);
                    $runsAllowed = $earnedRuns + $faker->numberBetween(0, 2);
                    $strikeoutsPitched = $faker->numberBetween(0, (int)($inningsPitched * 1.5));
                    $walksAllowed = $faker->numberBetween(0, (int)($inningsPitched / 2));
                    $hitsAllowed = $faker->numberBetween(0, (int)($inningsPitched * 2));
                    $homeRunsAllowed = $faker->numberBetween(0, (int)($inningsPitched / 5));
                    $pitchesThrown = $faker->numberBetween(10, 150);

                    GamePlayerStat::create([
                        'game_id'               => $game->id,
                        'player_id'             => $player->id,
                        'team_id'               => $player->team_id,
                        'is_starter'            => $faker->boolean(30),
                        'batting_order'         => null,
                        'position_id'           => $pitcherPositionId,

                        // 投手の打撃成績は0にする
                        'plate_appearances'     => 0,
                        'at_bats'               => 0,
                        'hits'                  => 0,
                        'doubles'               => 0,
                        'triples'               => 0,
                        'home_runs'             => 0,
                        'rbi'                   => 0,
                        'stolen_bases'          => 0,
                        'caught_stealing'       => 0,
                        'strikeouts'            => 0,
                        'walks'                 => 0,
                        'hit_by_pitch'          => 0,
                        'sac_bunts'             => 0,
                        'sac_flies'             => 0,
                        'double_plays'          => 0,
                        'errors'                => 0,
                        'runs_scored'           => 0,


                        'innings_pitched'       => $inningsPitched,
                        'earned_runs'           => $earnedRuns,
                        'runs_allowed'          => $runsAllowed,
                        'hits_allowed'          => $hitsAllowed,
                        'home_runs_allowed'     => $homeRunsAllowed,
                        'walks_allowed'         => $walksAllowed,
                        'strikeouts_pitched'    => $strikeoutsPitched,
                        'pitches_thrown'        => $pitchesThrown,
                        'is_winner_pitcher'     => ($game->home_score > $game->away_score && $player->team_id == $game->home_team_id) ? $faker->boolean(20) : false,
                        'is_loser_pitcher'      => ($game->away_score > $game->home_score && $player->team_id == $game->home_team_id) ? $faker->boolean(20) : false,
                        'is_save_pitcher'       => $faker->boolean(10),
                        'is_hold_pitcher'       => $faker->boolean(10),

                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ]);
                } else { // 野手の打撃成績（レギュラー野手、控え野手）
                    // ★修正点: 変数をここで初期化し、常に定義されているようにする★
                    $plateAppearances = 0;
                    $atBats = 0;
                    $hits = 0;
                    $doubles = 0;
                    $triples = 0;
                    $homeRuns = 0;
                    $rbi = 0;
                    $stolenBases = 0;
                    $caughtStealing = 0;
                    $strikeouts = 0;
                    $walks = 0;
                    $hitByPitch = 0;
                    $sacBunts = 0;
                    $sacFlies = 0;
                    $doublePlays = 0;
                    $errors = 0;
                    $runsScored = 0;
                    $positionId = null; // ★ここを初期化！★


                    // 野手の場合、打席に立つかどうかの確率を role に応じて調整
                    $shouldBat = false;
                    if ($player->role === 'レギュラー野手') {
                        $shouldBat = $faker->boolean(90); // レギュラーは90%で打席に立つ
                        if ($shouldBat) {
                            $plateAppearances = $faker->numberBetween(3, 5); // 3-5打席
                        }
                    } elseif ($player->role === '控え野手') {
                        $shouldBat = $faker->boolean(40); // 控えは40%で打席に立つ
                        if ($shouldBat) {
                            $plateAppearances = $faker->numberBetween(1, 3); // 1-3打席
                        }
                    }

                    if ($shouldBat && $plateAppearances > 0) {
                        $atBats = $faker->numberBetween(max(1, $plateAppearances - 1), $plateAppearances); // 打席数以下の打数

                        if ($atBats > 0) {
                            for ($ab = 0; $ab < $atBats; $ab++) {
                                $randRoll = $faker->numberBetween(1, 100); // 100分の1の確率
                                if ($player->role === 'レギュラー野手') {
                                    // 打率 .280-.320 くらいを狙う (28-32% chance of a hit per at-bat)
                                    if ($randRoll <= 30) { // 30% chance of a hit (adjust this value)
                                        $hits++;
                                        // ヒットを打った場合、その種類を決定
                                        $randHitType = $faker->numberBetween(1, 100);
                                        if ($randHitType <= 10) { // ★調整：10% chance of HR among hits for Regular (目標年間20-40本)★
                                            $homeRuns++;
                                        } elseif ($randHitType <= 25) { // 15% chance of Double (合計25%)
                                            $doubles++;
                                        } elseif ($randHitType <= 30) { // 5% chance of Triple (合計30%)
                                            $triples++;
                                        }
                                    } else {
                                        // ヒット以外の場合
                                        // 四球・三振などの判定もここで行う
                                        if ($randRoll <= 30 + 10) { // 30%ヒット + 10%四球
                                            $walks++;
                                        } elseif ($randRoll <= 30 + 10 + 20) { // 30%ヒット + 10%四球 + 20%三振
                                            $strikeouts++;
                                        }
                                    }
                                } elseif ($player->role === '控え野手') {
                                    // 打率 .200-.250 くらいを狙う (20-25% chance of a hit per at-bat)
                                    if ($randRoll <= 22) { // 22% chance of a hit (adjust this value)
                                        $hits++;
                                        $randHitType = $faker->numberBetween(1, 100);
                                        if ($randHitType <= 5) { // ★調整：5% chance of HR among hits for Subs (目標年間数本)
                                            $homeRuns++;
                                        }
                                    } else {
                                        // ヒット以外の場合
                                        if ($randRoll <= 22 + 8) { // 22%ヒット + 8%四球
                                            $walks++;
                                        } elseif ($randRoll <= 22 + 8 + 25) { // 22%ヒット + 8%四球 + 25%三振
                                            $strikeouts++;
                                        }
                                    }
                                }
                            }
                        }
                        // ここで確実にヒット数、HR数を超過しないように調整
                        $homeRuns = min($homeRuns, $hits);
                        $doubles = min($doubles, $hits - $homeRuns);
                        $triples = min($triples, $hits - $homeRuns - $doubles);


                        // ★調整：打点数を調整（より現実的に）★
                        // 打点目標：レギュラーMax100, 平均60。控えMax10。
                        // 1試合あたりの打点期待値は、打席数や出塁率、長打率に依存する
                        // シンプルに、HR数に比例 + ヒット数に比例 + その他（四球等）で生成
                        $rbi = $homeRuns; // HR数分は最低保証 (自身が打ったHRによる打点)
                        if ($hits > $homeRuns) { // HR以外のヒットがあれば追加打点の可能性
                            // レギュラー野手はヒット1本あたり0-1打点、控え野手は0打点
                            if ($player->role === 'レギュラー野手') {
                                // 安打数に応じた打点（HR以外）
                                $rbi += $faker->numberBetween(0, (int)($hits - $homeRuns)); // 1ヒットあたり0-1打点
                            }
                            // 控え野手は安打による打点機会をさらに減らす
                        }
                        // 四球でも稀に打点 (押し出しなど)
                        $rbi += $faker->boolean(5) ? 1 : 0; // 5%で1打点追加（非常に稀）
                        
                        // 1試合の打点上限をさらに厳しく設定
                        $rbi = min($rbi, 3); // 1試合の打点上限を3に設定（エース級でも滅多に超えない）
                        $rbi = max(0, $rbi); // 0未満にならないように


                        // その他のスタッツも微調整
                        $stolenBases = ($hits > 0 && $faker->boolean(5)) ? $faker->numberBetween(0, 1) : 0; // 盗塁確率を調整
                        $caughtStealing = ($stolenBases > 0 && $faker->boolean(35)) ? 1 : 0; // 盗塁死確率を調整

                        // 三振と四球は上記の打席内生成で設定されるため、ここでは調整不要
                        // plateAppearances - atBats - walks - hitByPitch - sacBunts - sacFlies = アウト数
                        $outs = $plateAppearances - $atBats - $walks - $hitByPitch - $sacBunts - $sacFlies;
                        $strikeouts = min($strikeouts, $outs); // 三振がアウト数を超えないように

                        $hitByPitch = $faker->boolean(1) ? 1 : 0; // 死球確率を調整 (非常に低く)
                        $sacBunts = $faker->boolean(2) ? 1 : 0; // 犠打確率を調整
                        $sacFlies = $faker->boolean(1) ? 1 : 0; // 犠飛確率を調整
                        $doublePlays = ($atBats > 0 && $faker->boolean(7)) ? 1 : 0; // 併殺確率を調整
                        $errors = $faker->boolean(1) ? 1 : 0; // エラー確率を調整

                        // 得点も打席結果に応じて調整
                        // 得点はヒット、四球、HR、盗塁、相手エラーなど複合的な要因で発生するので、簡略化
                        $runsScored = $faker->numberBetween(0, $hits + $walks + $homeRuns); 
                        $runsScored = min($runsScored, $plateAppearances); // 打席数を超えない

                        // ポジションIDの設定
                        $playerPositions = $player->positions->where('id', '!=', $pitcherPositionId);
                        // ★修正点: ここで $positionId を定義
                        $positionId = $playerPositions->isEmpty() ? null : $faker->randomElement($playerPositions)->id;
                    }


                    GamePlayerStat::create([
                        'game_id'               => $game->id,
                        'player_id'             => $player->id,
                        'team_id'               => $player->team_id,
                        'is_starter'            => ($player->role === 'レギュラー野手') ? true : $faker->boolean(30),
                        'batting_order'         => ($player->role === 'レギュラー野手') ? $faker->numberBetween(1, 9) : null,
                        'position_id'           => $positionId, // ここで使用


                        'plate_appearances'     => $plateAppearances,
                        'at_bats'               => $atBats,
                        'hits'                  => $hits,
                        'doubles'               => $doubles,
                        'triples'               => $triples,
                        'home_runs'             => $homeRuns,
                        'rbi'                   => $rbi,
                        'stolen_bases'          => $stolenBases,
                        'caught_stealing'       => $caughtStealing,
                        'strikeouts'            => $strikeouts,
                        'walks'                 => $walks,
                        'hit_by_pitch'          => $hitByPitch,
                        'sac_bunts'             => $sacBunts,
                        'sac_flies'             => $sacFlies,
                        'double_plays'          => $doublePlays,
                        'errors'                => $errors,
                        'runs_scored'           => $runsScored,

                        'innings_pitched'       => null,
                        'earned_runs'           => null,
                        'runs_allowed'          => null,
                        'strikeouts_pitched'    => null,
                        'walks_allowed'         => null,
                        'hits_allowed'          => null,
                        'home_runs_allowed'     => null,
                        'pitches_thrown'        => null,
                        'is_winner_pitcher'     => false,
                        'is_loser_pitcher'      => false,
                        'is_save_pitcher'       => false,
                        'is_hold_pitcher'       => false,

                        'created_at'            => now(),
                        'updated_at'            => now(),
                    ]);
                }
            }
        }
        $this->command->info('各試合の選手成績データを作成しました。');
    }
}
