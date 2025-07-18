<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class League extends Model
{
    use HasFactory;

    protected $fillable=[
        'name',
    ];

    public function teams(){
        return $this -> hasMany(Team::class);
    }
}
