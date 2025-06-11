<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlateAppearance extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'inning',
        'batter_id',    // 打者
        'pitcher_id',   // 投手
        'team_id',      // 打者のチーム
        'result_type',  // 結果の種類 (例: 'single', 'double', 'home_run', 'strikeout', 'walk', 'fly_out' など)
        'runs_scored',  // この打席で入った得点 (0, 1, 2, 3, 4)
        'rbi',          // 打点
        'base_runner_1_id', // 1塁走者ID (特定の選手が走っていた場合)
        'base_runner_2_id', // 2塁走者ID
        'base_runner_3_id', // 3塁走者ID
        'out_count_before_pa', // この打席に入る前のアウトカウント
        'runners_on_bases_before_pa', // この打席に入る前の塁上の走者状況 (例: '100', '110' など)
        'is_at_bat',    // 打数にカウントされるか (犠打、犠飛、四球などはカウントされない)
    ];

    /**
     * この打席が行われた試合を取得
     */
    public function game()
    {
        return $this->belongsTo(Game::class);
    }

    /**
     * この打席の打者を取得
     */
    public function batter()
    {
        return $this->belongsTo(Player::class, 'batter_id');
    }

    /**
     * この打席の投手を取得
     */
    public function pitcher()
    {
        return $this->belongsTo(Player::class, 'pitcher_id');
    }

    /**
     * この打席を行った打者の所属チームを取得
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // 塁上の走者情報もPlayerモデルに紐付ける場合
    public function baseRunner1()
    {
        return $this->belongsTo(Player::class, 'base_runner_1_id');
    }

    public function baseRunner2()
    {
        return $this->belongsTo(Player::class, 'base_runner_2_id');
    }

    public function baseRunner3()
    {
        return $this->belongsTo(Player::class, 'base_runner_3_id');
    }
}