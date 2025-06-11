<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlayerPosition extends Model
{
    use HasFactory;

    protected $fillable =[
        'name',
        'is_pitcher'
    ];

    public function players(){
        return $this ->belongsToMany(Player::class,'player_position');
    }
}
