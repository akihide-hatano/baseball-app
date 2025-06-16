<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearlyBattingStat extends Model
{
    use HasFactory;

    protected $fillable = [
        'player_id',
        'year',
        'team_id', // nullable なので必要に応じて含める。関連テーブルとして別途取得も可能
        // 'league_id', // マイグレーションにない場合は削除。あれば含める

        // ★★★ マイグレーションファイルの実際のカラム名に合わせる ★★★
        'games',
        'plate_appearances', // 新規
        'at_bats',
        'hits',
        'doubles',
        'triples',
        'home_runs',
        'rbi',
        'stolen_bases',
        'caught_stealing',
        'strikeouts',
        'walks',
        'hit_by_pitch', // 新規
        'sac_bunts',    // 新規 (フォームの sacrifice_hits に対応)
        'sac_flies',
        'double_plays', // 新規
        'errors',       // 新規
        'runs_scored',  // 新規 (フォームの runs に対応)

        'batting_average',
        'on_base_percentage', // 新規
        'slugging_percentage',// 新規
        'ops',                // 新規
        'ops_plus',           // 新規
        'wrc_plus',           // 新規
    ];

    /**
     * この成績が紐付く選手を取得
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * この統計が紐付くチームを取得 (もしyearly_batting_statsテーブルにteam_idカラムがある場合)
     */
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}
