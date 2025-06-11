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
        'team_id',      // その試合で所属していたチーム
        'position_id',  // その試合で守っていたポジション

        // 打撃成績
        'at_bats',
        'runs',
        'hits',
        'doubles',
        'triples',
        'home_runs',
        'runs_batted_in',
        'stolen_bases',
        'caught_stealing',
        'walks',
        'strikeouts',
        'sacrifice_bunts',
        'sacrifice_flies',
        'hit_by_pitch',
        'gidp', // Double Play Grounded Into Double Play (併殺打)

        // 投球成績 (このテーブルで直接管理する場合)
        'innings_pitched',
        'earned_runs',
        'pitching_strikeouts',
        'pitching_walks',
        'home_runs_allowed',
        'pitches_thrown',
        'is_starter', // 先発投手か
        'is_winner',  // 勝利投手か
        'is_loser',   // 敗戦投手か
        'is_saver',   // セーブ投手か
        'is_holder',  // ホールド投手か
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