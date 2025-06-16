<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearlyPitchingStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'year',
        'team_id',

        // ★★★ マイグレーションファイルの実際のカラム名に合わせる ★★★
        'games',              // 登板試合数
        'starts',             // 先発登板数
        'wins',               // 勝利
        'losses',             // 敗北
        'saves',              // セーブ
        'holds',              // ホールド
        'innings_pitched',    // 投球回 (小数可)
        'hits_allowed',       // 被安打
        'home_runs_allowed',  // 被本塁打
        'walks_allowed',      // 与四球
        'hit_by_pitch_allowed', // 与死球 (新規)
        'strikeouts_pitched', // 奪三振
        'runs_allowed',       // 失点 (新規)
        'earned_runs',        // 自責点 (新規)
        'pitches_thrown',     // 総投球数 (新規)

        // 計算で求める指標
        'earned_run_average', // 防御率
        'whip',               // WHIP
        'strikeout_walk_ratio', // K/BB
    ];

    /**
     * この統計が紐付く選手を取得
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * この統計が紐付くチームを取得
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}