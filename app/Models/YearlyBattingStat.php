<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearlyBattingStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'year',
        'league_id', // どのリーグの統計か
        'team_id',   // どのチームの統計か (null許容でリーグ全体も表現可能)

        'total_games_played',
        'total_at_bats',
        'total_runs',
        'total_hits',
        'total_doubles',
        'total_triples',
        'total_home_runs',
        'total_runs_batted_in',
        'total_stolen_bases',
        'total_caught_stealing',
        'total_walks',
        'total_strikeouts',
        'avg_batting_average', // 平均打率
        'avg_on_base_percentage', // 平均出塁率
        'avg_slugging_percentage', // 平均長打率
        'avg_ops', // 平均OPS
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