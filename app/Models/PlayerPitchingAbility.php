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
        'pitch_control',    // 変化球の制球力
        'pitch_stamina',    // スタミナ
        'average_velocity', // 平均球速
        'pitch_type_1',     // ★この行と以下4行を追加（またはコメントアウト解除）★
        'pitch_type_2',
        'pitch_type_3',
        'pitch_type_4',
        'pitch_type_5',
        'pitch_type_6', // ★追加★
        'pitch_type_7', // ★追加★
        'overall_rank',
        'special_skills',
    ];

    public function player()
    {
        return $this->belongsTo(Player::class);
    }
}