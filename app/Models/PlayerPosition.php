<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot; // ★Pivot クラスを使用

class PlayerPosition extends Pivot // ★Model ではなく Pivot を継承
{
    use HasFactory;

    // マイグレーションでidカラムがない場合は以下2行を追加
    protected $primaryKey = null; // idカラムを主キーとしない
    public $incrementing = false; // 自動インクリメントを無効化

    // `$fillable` は通常、中間テーブルでは不要ですが、
    // もし追加のカラム（例: skill_levelなど）があればここに定義します。
    // protected $fillable = [
    //     'player_id',
    //     'position_id',
    // ];

    /**
     * この中間レコードに紐付く選手を取得
     */
    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    /**
     * この中間レコードに紐付くポジションを取得
     */
    public function position()
    {
        return $this->belongsTo(Position::class);
    }
}