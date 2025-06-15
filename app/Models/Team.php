<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable =[
        'league_id',
        'team_name',
        'team_nickname',
        'location',
        'founded_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'founded_at' => 'datetime', // ★この行を追加または確認★
    ];

    // リーグへのリレーション (もしLeagueモデルがある場合)
    public function league()
    {
        return $this->belongsTo(League::class);
    }

    /**
     * このチームに所属する選手を取得
     */
    public function players()
    {
        return $this->hasMany(Player::class);
    }

    /**
     * このチームがホームの試合を取得
     */
    public function homeGames()
    {
        return $this->hasMany(Game::class, 'home_team_id');
    }

    /**
     * このチームがアウェイの試合を取得
     */
    public function awayGames()
    {
        return $this->hasMany(Game::class, 'away_team_id');
    }

    /**
     * このチームの年度別成績を取得
     */
    public function playerStats()
    {
        return $this->hasMany(PlayerStat::class);
    }

    public function yearlyTeamStats(){
        return $this->hasMany(YearlyTeamStat::class);
    }

   /**
     * Get the team's nickname.
     * このアクセサにより、データベースの 'team_nickname' カラムを
     * モデルの 'nickname' 属性としてアクセスできるようになります。
     * 例: $team->nickname
     *
     * @return string|null
     */
    public function getNicknameAttribute(): ?string
    {
        // attributes配列から'team_nickname'キーの値を取得します。
        // nullの場合に備えてnull合体演算子を使用しています。
        return $this->attributes['team_nickname'] ?? null;
    }

}
