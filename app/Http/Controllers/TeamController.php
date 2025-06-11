<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team; // Teamモデルを忘れずにuseする

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $teams = Team::all(); // 全てのチームデータを取得
        return view('teams.index', compact('teams')); // 'teams.index'ビューに'teams'変数を渡して表示
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // ここに新規作成フォームを表示するロジックを記述
        return "チーム作成フォーム";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // ここにフォームから送信されたデータを検証・保存するロジックを記述
        return "新しいチームを保存しました";
    }

    /**
     * Display the specified resource.
     */
    public function show($id) // ★引数を$idに変更★
    {
        $team = Team::find($id); // ★IDを使って手動で検索★

        if (!$team) {
            // チームが見つからなかった場合の処理 (例: 404エラーを返す)
            abort(404, 'Team not found!');
        }


        return view('teams.show', compact('team'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team) // Route Model Bindingを活用
    {
        // ここに編集フォームを表示するロジックを記述
        return "チーム編集フォーム (ID: {$team->id})";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team) // Route Model Bindingを活用
    {
        // ここにフォームから送信されたデータでチームを更新するロジックを記述
        return "チーム (ID: {$team->id}) を更新しました";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team) // Route Model Bindingを活用
    {
        $team->delete(); // チームを削除
        return redirect()->route('teams.index')->with('success', 'チームが削除されました！');
    }
}