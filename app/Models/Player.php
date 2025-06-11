<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'name',
        'jersey_number',
        'date_of_birth',
        'height',
        'weight',
        'specialty',
        'description', // ★ここを追加★
        'hometown',
    ];

    /**
     * この選手が現在所属するチームを取得
     */
    public function currentTeam()
    {
        return $this->belongsTo(Team::class, 'team_id');
    }

    /**
     * この選手が持つポジションを取得（多対多）
     */
    public function positions()
    {
        return $this->belongsToMany(Position::class, 'player_position');
    }

    /**
     * この選手の年度別成績を取得
     */
    public function playerStats()
    {
        return $this->hasMany(PlayerStat::class);
    }

    /**
     * この選手の年度別打撃能力を取得
     */
    public function battingAbilities()
    {
        return $this->hasMany(PlayerBattingAbility::class);
    }

    /**
     * この選手の年度別投球能力を取得
     */
    public function pitchingAbilities()
    {
        return $this->hasMany(PlayerPitchingAbility::class);
    }

    /**
     * この選手が打者として出場した試合ごとの成績を取得
     */
    public function gamePlayerStatsAsBatter()
    {
        return $this->hasMany(GamePlayerStat::class, 'player_id');
    }

    /**
     * この選手が打者として関わった打席結果を取得
     */
    public function plateAppearancesAsBatter()
    {
        return $this->hasMany(PlateAppearance::class, 'batter_id');
    }

    /**
     * この選手が投手として関わった打席結果を取得
     */
    public function plateAppearancesAsPitcher()
    {
        return $this->hasMany(PlateAppearance::class, 'pitcher_id');
    }
}