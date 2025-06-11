<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerPitchingAbility extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'year',
        'velocity',         // 球速
        'control',          // コントロール
        'stamina',          // スタミナ
        'pitch_type_1',     // 変化球タイプ1
        'pitch_level_1',    // 変化球レベル1
        'pitch_type_2',
        'pitch_level_2',
        'pitch_type_3',
        'pitch_level_3',
        'pitch_type_4',
        'pitch_level_4',
        'pitch_type_5',
        'pitch_level_5',
        'pitching_style',   // 投球スタイル
        'overall_rank',     // 総合能力ランク
    ];

    /**
     * この能力が紐付く選手を取得
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}