<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\League; // Leagueモデルをuseに追加
use App\Models\Game; // GameモデルはTeamControllerでは通常使われませんが、useに存在するため残します
use Illuminate\Support\Facades\Log;

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // チームの基本クエリ
        $query = Team::with('league');

        // リーグIDによるフィルタリング
        if ($request->filled('league_id')) {
            $query->where('league_id', $request->input('league_id'));
        }

        // チーム名によるフリーワード検索
        if ($request->filled('search_team_name')) {
            $searchTerm = '%' . $request->input('search_team_name') . '%';
            $query->where(function($q) use ($searchTerm) {
                $q->where('team_name', 'like', $searchTerm)
                  ->orWhere('team_nickname', 'like', $searchTerm);
            });
        }

        // フィルタリングされたチームデータを取得
        $teams = $query->orderBy('team_name')->get();

        // 全てのリーグデータを取得し、ビューに渡す（検索フォーム用）
        $leagues = League::all();

        return view('teams.index', compact('teams', 'leagues'));
    }

    /**
     * Show the form for creating a new resource.
     * 新しいチーム作成フォームを表示します。
     */
    public function create()
    {
        // チーム作成フォームでリーグを選択できるように、全てのリーグデータを取得してビューに渡す
        $leagues = League::all();
        return view('teams.create', compact('leagues'));
    }

    /**
     * Store a newly created resource in storage.
     * フォームから送信されたデータを受け取り、バリデーションとデータベースへの保存を行います。
     */
    public function store(Request $request)
    {
        // 1. バリデーション
        $request->validate([
            'league_id' => 'required|exists:leagues,id',
            'team_name' => 'required|string|max:255|unique:teams,team_name',
            'team_nickname' => 'required|string|max:255|unique:teams,team_nickname',
            'location' => 'required|string|max:255',
            'founded_at' => 'required|date',
        ]);

        // 2. データベースへの保存
        Team::create($request->all());

        // 3. 保存後のリダイレクトとメッセージ
        return redirect()->route('teams.index')->with('success', '新しいチームが正常に登録されました！');
    }

    /**
     * Display the specified resource.
     * Route Model Bindingにより、URLからTeamモデルのインスタンスが直接渡されます。
     */
    public function show(Team $team)
    {
        // $team は既に自動的に取得されているため、Team::find($id) は不要です。
        // リレーションをロード
        $team->load([
            'league', // showビューでもリーグ名を表示するため
            'players',
            'yearlyTeamStats' => function ($query) {
                $query->orderBy('year', 'desc');
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
            return $game->game_date . ' ' . $game->game_time;
        })->take(10);

        return view('teams.show', compact('team', 'recentGames'));
    }

    /**
     * Show the form for editing the specified resource.
     * Route Model Bindingにより、URLからTeamモデルのインスタンスが直接渡されます。
     */
    public function edit(Team $team)
    {
        $leagues = League::all();
        return view('teams.edit', compact('team', 'leagues'));
    }

    /**
     * Update the specified resource in storage.
     * Route Model Bindingにより、URLからTeamモデルのインスタンスが直接渡されます。
     */
    public function update(Request $request, Team $team)
    {
        // 1. バリデーション
        $request->validate([
            'league_id' => 'required|exists:leagues,id',
            'team_name' => 'required|string|max:255|unique:teams,team_name,' . $team->id, // 更新時は自身のIDを除外してユニークチェック
            'team_nickname' => 'required|string|max:255|unique:teams,team_nickname,' . $team->id, // 更新時は自身のIDを除外してユニークチェック
            'location' => 'required|string|max:255',
            'founded_at' => 'required|date',
        ]);

        // 2. データベースの更新
        $team->update($request->all());

        // 3. 更新後のリダイレクトとメッセージ
        return redirect()->route('teams.show', $team->id)->with('success', 'チーム情報が正常に更新されました！');
    }

    /**
     * Remove the specified resource from storage.
     * Route Model Bindingにより、URLからTeamモデルのインスタンスが直接渡されます。
     */
    public function destroy(Team $team)
    {
        // データベースからチームを削除
        $team->delete();

        // 削除後のリダイレクトとメッセージ
        return redirect()->route('teams.index')->with('success', 'チームが正常に削除されました！');
    }
}