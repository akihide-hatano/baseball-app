<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $players = Player::all(); // 全ての選手データを取得
        dd($players);
        return view('players.index', compact('players')); // 'players.index'ビューに'players'変数を渡して表示
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ここに新規作成フォームを表示するロジックを記述
        return "選手作成フォーム";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ここにフォームから送信されたデータを検証・保存するロジックを記述
        return "新しい選手を保存しました";
    }

    /**
     * Display the specified resource.
     */
    public function show(Player $player) // Laravel 8以降では、IDの代わりにモデルを型ヒントとして指定すると、自動的にそのIDのモデルインスタンスが取得されます (Route Model Binding)
    {
        // ここではRoute Model Bindingを使用しているため、$playerが直接インスタンスになります
        return view('players.show', compact('player'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Player $player)
    {
        // ここに編集フォームを表示するロジックを記述
        return "選手編集フォーム (ID: {$player->id})";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Player $player)
    {
        // ここにフォームから送信されたデータで選手を更新するロジックを記述
        return "選手 (ID: {$player->id}) を更新しました";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Player $player)
    {
        $player->delete(); // 選手を削除
        return redirect()->route('players.index')->with('success', '選手が削除されました！');
    }
}