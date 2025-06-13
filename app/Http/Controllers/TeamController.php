<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team; // Teamモデルを忘れずにuseする
use App\Models\Game;

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
        $team = Team::find($id);

        // もし $team が取得できなかった場合（nullの場合）のハンドリング
        if (!$team) {
            abort(404, 'チームが見つかりませんでした。');
        }

        // チームに所属する選手、年度別成績、そして試合データをEagerロード
        $team->load([
            'players',
            'yearlyTeamStats' => function ($query) {
                $query->orderBy('year', 'desc'); // 最新の年を先に取得
            },
            'homeGames' => function ($query) {
                $query->with(['homeTeam', 'awayTeam'])->orderBy('game_date', 'desc')->orderBy('game_time', 'desc');
            },
            'awayGames' => function ($query) {
                $query->with(['homeTeam', 'awayTeam'])->orderBy('game_date', 'desc')->orderBy('game_time', 'desc');
            }
        ]);

        // ホーム試合とアウェイ試合を結合し、日付と時刻でソートして直近10件を取得
        $allTeamGames = $team->homeGames->merge($team->awayGames);
        $recentGames = $allTeamGames->sortByDesc(function ($game) {
            return $game->game_date . ' ' . $game->game_time; // 日付と時刻で複合ソート
        })->take(10); // 直近10試合

        // ★ここを追加: ddで$recentGamesの中身を確認★
        // dd($recentGames);

        // ビューにデータを渡す
        return view('teams.show', compact('team', 'recentGames'));
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