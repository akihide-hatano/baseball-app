<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PlayerStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'year',
        'team_id', // 所属チーム（その年の）

        // 打撃成績
        'games_played_batting', // 出場試合数
        'at_bats',              // 打席数
        'plate_appearances',    // 打席
        'runs',                 // 得点
        'hits',                 // 安打
        'doubles',              // 二塁打
        'triples',              // 三塁打
        'home_runs',            // 本塁打
        'runs_batted_in',       // 打点
        'stolen_bases',         // 盗塁
        'caught_stealing',      // 盗塁死
        'walks',                // 四球
        'strikeouts',           // 三振
        'batting_average',      // 打率
        'on_base_percentage',   // 出塁率
        'slugging_percentage',  // 長打率
        'ops',                  // OPS

        // 投球成績 (player_pitching_abilities と重複しますが、年度別の記録として区別)
        'games_played_pitching',// 登板試合数
        'wins',                 // 勝利
        'losses',               // 敗戦
        'holds',                // ホールド
        'saves',                // セーブ
        'innings_pitched',      // 投球回
        'earned_runs',          // 自責点
        'pitching_strikeouts',  // 奪三振
        'pitching_walks',       // 与四球
        'era',                  // 防御率
        'whip',                 // WHIP
    ];

    /**
     * この成績が紐付く選手を取得
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * この年の所属チームを取得
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
