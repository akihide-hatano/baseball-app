<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerBattingAbility extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'year',
        'contact_power',    // ミート力
        'power',            // パワー
        'speed',            // 走力
        'fielding',         // 守備力
        'throwing',         // 肩力
        'reaction',         // 捕球/反応力
        'overall_rank',     // 総合能力ランク
        'special_skills',   // 特殊能力
    ];

    /**
     * この能力が紐付く選手を取得
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}