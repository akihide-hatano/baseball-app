<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Position extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'is_pitcher', // マイグレーションにこれがあれば
    ];

    /**
     * このポジションを持つ選手たちを取得（多対多）
     */
    public function players()
    {
        return $this->belongsToMany(Player::class, 'player_position');
    }
}