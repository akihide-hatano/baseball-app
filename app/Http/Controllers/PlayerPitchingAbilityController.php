<?php

namespace App\Http\Controllers;

use App\Models\Player; // Playerモデルをuseする
use App\Models\PlayerPitchingAbility; // PlayerPitchingAbilityモデルをuseする
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlayerPitchingAbilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // このルートはexcept(['index', 'show'])で除外されているため、通常は使用しません。
        return view('player_pitching_abilities.index');
        return "投手能力一覧ページ (このルートは使用されない想定)";
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Player $player) // Playerモデルをルートモデルバインディングで受け取る
    {
       // フォームで選択できる年度のリストを作成 (例: 現在の年-5年から現在の年+1年まで)
        $currentYear = Carbon::now()->year;
        $years = range( $currentYear -5, $currentYear +1 );

        $allPitchTypes = ['カーブ', 'スライダー', 'フォーク', 'チェンジアップ',
                'シュート', 'カットボール', 'シンカー'];
        return view( 'player_pitching_abilities.create',compact('player','years','allPitchTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Player $player) // Playerモデルをルートモデルバインディングで受け取る
    {
        // 実際にはここでバリデーションと保存処理を行います
        // 例: return redirect()->route('players.show', $player->id)->with('success', '投手能力を追加しました。');
        return "選手ID: {$player->id} に新しい投手能力を保存する処理";
    }

    /**
     * Display the specified resource.
     */
    public function show(PlayerPitchingAbility $playerPitchingAbility)
    {
        // このルートはexcept(['index', 'show'])で除外されているため、通常は使用しません。
        return view('player_pitching_abilities.show', compact('playerPitchingAbility'));
        return "投手能力詳細ページ (このルートは使用されない想定)";
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Player $player, PlayerPitchingAbility $playerPitchingAbility) // PlayerモデルとPlayerPitchingAbilityモデルをルートモデルバインディングで受け取る
    {
        // return view('player_pitching_abilities.edit', compact('player', 'playerPitchingAbility'));
        return "選手ID: {$player->id} の投手能力ID: {$playerPitchingAbility->id} の編集フォーム";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Player $player, PlayerPitchingAbility $playerPitchingAbility) // PlayerモデルとPlayerPitchingAbilityモデルをルートモデルバインディングで受け取る
    {
        // 実際にはここでバリデーションと更新処理を行います
        例: return redirect()->route('players.show', $player->id)->with('success', '投手能力を更新しました。');
        return "選手ID: {$player->id} の投手能力ID: {$playerPitchingAbility->id} を更新する処理";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Player $player, PlayerPitchingAbility $playerPitchingAbility) // PlayerモデルとPlayerPitchingAbilityモデルをルートモデルバインディングで受け取る
    {
        // 実際にはここで削除処理を行います
        例: $playerPitchingAbility->delete(); return redirect()->route('players.show', $player->id)->with('success', '投手能力を削除しました。');
        return "選手ID: {$player->id} の投手能力ID: {$playerPitchingAbility->id} を削除する処理";
    }
}