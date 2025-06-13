<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamePlayerStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'player_id',
        'team_id',
        'is_starter',
        'batting_order',
        'position_id',

        // 打撃成績（GamePlayerStatSeederに合わせて修正）
        'plate_appearances', // Seederにはあるがモデルになかったので追加
        'at_bats',
        'hits',
        'doubles',
        'triples',
        'home_runs',
        'rbi',                   // ★rbi に修正★
        'stolen_bases',
        'caught_stealing',
        'strikeouts',
        'walks',
        'hit_by_pitch',
        'sac_bunts',             // ★sac_bunts に修正★
        'sac_flies',             // ★sac_flies に修正★
        'double_plays',          // ★double_plays に修正★ (gidpから)
        'errors',                // Seederにはあるがモデルになかったので追加
        'runs_scored',           // ★runs_scored に修正★ (runsから)


        // 投球成績（GamePlayerStatSeederに合わせて修正）
        'innings_pitched',
        'earned_runs',
        'runs_allowed',          // Seederにはあるがモデルになかったので追加
        'hits_allowed',          // Seederにはあるがモデルになかったので追加
        'home_runs_allowed',
        'walks_allowed',         // ★walks_allowed に修正★ (pitching_walksから)
        'strikeouts_pitched',    // ★strikeouts_pitched に修正★ (pitching_strikeoutsから)
        'pitches_thrown',
        'is_winner_pitcher',     // ★is_winner_pitcher に修正★ (is_winnerから)
        'is_loser_pitcher',      // ★is_loser_pitcher に修正★ (is_loserから)
        'is_save_pitcher',       // ★is_save_pitcher に修正★ (is_saverから)
        'is_hold_pitcher',       // ★is_hold_pitcher に修正★ (is_holderから)
    ];

    /**
     * この成績が紐付く試合を取得
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * この成績が紐付く選手を取得
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * この試合で所属していたチームを取得
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * この試合で守っていたポジションを取得
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}
