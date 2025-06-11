<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearlyTeamStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id',
        'year',
        'wins',
        'losses',
        'draws',
        'winning_percentage',
        'games_behind',
        'rank',
        'league_result',
        'postseason_result',
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}