<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_date',
        'home_team_id',
        'away_team_id',
        'home_score',
        'away_score',
        'venue',
        'status', // 例: 'scheduled', 'in_progress', 'final', 'postponed'
        'duration', // 例: '2h30m'
        'attendance',
        'winning_pitcher_id', // 勝利投手 (nullable)
        'losing_pitcher_id',  // 敗戦投手 (nullable)
        'saving_pitcher_id',  // セーブ投手 (nullable)
        'mvp_player_id',      // MVP選手 (nullable)
    ];

    /**
     * この試合のホームチームを取得
     */
    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    /**
     * この試合のアウェイチームを取得
     */
    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    /**
     * この試合に参加した選手たちの成績を取得 (中間テーブル game_player_stats を通じて)
     */
    public function gamePlayerStats()
    {
        return $this->hasMany(GamePlayerStat::class);
    }

    /**
     * この試合で行われた打席結果を取得
     */
    public function plateAppearances()
    {
        return $this->hasMany(PlateAppearance::class);
    }

    /**
     * この試合の勝利投手を選手として取得
     */
    public function winningPitcher()
    {
        return $this->belongsTo(Player::class, 'winning_pitcher_id');
    }

    /**
     * この試合の敗戦投手を選手として取得
     */
    public function losingPitcher()
    {
        return $this->belongsTo(Player::class, 'losing_pitcher_id');
    }

    /**
     * この試合のセーブ投手を選手として取得
     */
    public function savingPitcher()
    {
        return $this->belongsTo(Player::class, 'saving_pitcher_id');
    }

    /**
     * この試合のMVP選手を選手として取得
     */
    public function mvpPlayer()
    {
        return $this->belongsTo(Player::class, 'mvp_player_id');
    }
}