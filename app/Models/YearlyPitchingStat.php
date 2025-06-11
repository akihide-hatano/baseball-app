<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearlyPitchingStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'league_id', // どのリーグの統計か
        'team_id',   // どのチームの統計か (null許容でリーグ全体も表現可能)

        'total_games_played_pitching',
        'total_wins',
        'total_losses',
        'total_holds',
        'total_saves',
        'total_innings_pitched',
        'total_earned_runs',
        'total_pitching_strikeouts',
        'total_pitching_walks',
        'avg_era', // 平均防御率
        'avg_whip', // 平均WHIP
    ];

    /**
     * この統計が紐付くリーグを取得
     */
    public function league()
    {
        return $this->belongsTo(League::class);
    }

    /**
     * この統計が紐付くチームを取得 (リーグ全体の場合null)
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}