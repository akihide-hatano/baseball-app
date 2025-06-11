<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Game;
use App\Models\Player;
use App\Models\Position;
use App\Models\GamePlayerStat;

class GamePlayerStatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
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

            $homeTeamPlayersInGame = $homeTeamPlayers->shuffle()->take($faker->numberBetween(15, 25));
            $awayTeamPlayersInGame = $awayTeamPlayers->shuffle()->take($faker->numberBetween(15, 25));

            $playersInGame = $homeTeamPlayersInGame->merge($awayTeamPlayersInGame);

            foreach ($playersInGame as $player) {
                $isPitcher = $player->positions->contains('id', $pitcherPositionId);

                // 投手の成績 (isPitcher && 70%の確率で登板)
                if ($isPitcher && $faker->boolean(70)) {
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
                        'batting_order'         => null, // 投手は打順なし
                        'position_id'           => $pitcherPositionId,

                        // 打撃成績は0にする（nullは許容されないため）
                        'plate_appearances'     => 0,
                        'at_bats'               => 0,
                        'hits'                  => 0, // ★ここを0に修正★
                        'doubles'               => 0, // ★ここを0に修正★
                        'triples'               => 0, // ★ここを0に修正★
                        'home_runs'             => 0, // ★ここを0に修正★
                        'rbi'                   => 0, // ★ここを0に修正★
                        'stolen_bases'          => 0, // ★ここを0に修正★
                        'caught_stealing'       => 0, // ★ここを0に修正★
                        'strikeouts'            => 0, // ★ここを0に修正★
                        'walks'                 => 0, // ★ここを0に修正★
                        'hit_by_pitch'          => 0, // ★ここを0に修正★
                        'sac_bunts'             => 0, // ★ここを0に修正★
                        'sac_flies'             => 0, // ★ここを0に修正★
                        'double_plays'          => 0, // ★ここを0に修正★
                        'errors'                => 0, // ★ここを0に修正★
                        'runs_scored'           => 0, // ★ここを0に修正★


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
                } else { // 野手または登板しなかった投手の打撃成績
                    $plateAppearances = $faker->numberBetween(0, 6);
                    $atBats = $plateAppearances - $faker->numberBetween(0, (int)($plateAppearances / 2));
                    $hits = 0;
                    if ($atBats > 0) {
                        $hits = $faker->numberBetween(0, $atBats);
                    }
                    $doubles = $faker->numberBetween(0, (int)($hits / 2));
                    $triples = $faker->numberBetween(0, (int)($hits / 5));
                    $homeRuns = 0;
                    if ($hits > 0 && $faker->boolean(8)) {
                        $homeRuns = $faker->numberBetween(0, 1);
                    }
                    $rbi = $faker->numberBetween(0, 5);

                    $stolenBases = $faker->boolean(15) ? $faker->numberBetween(0, 2) : 0;
                    $caughtStealing = ($stolenBases > 0 && $faker->boolean(20)) ? 1 : 0;
                    $strikeouts = $faker->numberBetween(0, $plateAppearances);
                    $walks = $faker->numberBetween(0, (int)($plateAppearances / 3));
                    $hitByPitch = $faker->boolean(5) ? 1 : 0;
                    $sacBunts = $faker->boolean(10) ? 1 : 0;
                    $sacFlies = $faker->boolean(5) ? 1 : 0;
                    $doublePlays = ($atBats > 0 && $faker->boolean(15)) ? 1 : 0;
                    $errors = $faker->boolean(5) ? 1 : 0;
                    $runsScored = $faker->numberBetween(0, 3);

                    $playerPositions = $player->positions->where('id', '!=', $pitcherPositionId);
                    $positionId = $playerPositions->isEmpty() ? null : $faker->randomElement($playerPositions)->id;


                    GamePlayerStat::create([
                        'game_id'               => $game->id,
                        'player_id'             => $player->id,
                        'team_id'               => $player->team_id,
                        'is_starter'            => $faker->boolean(80),
                        'batting_order'         => $faker->numberBetween(1, 9),
                        'position_id'           => $positionId,

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