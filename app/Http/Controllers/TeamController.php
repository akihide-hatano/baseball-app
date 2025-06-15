<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team; // Teamモデルをuseする
use App\Models\League; // Leagueモデルをuseに追加 ★追加★
use App\Models\Game; // Gameモデルは既存のshowメソッドで使われています

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // ★★★ Request $request を引数に追加 ★★★
    {
        // チームの基本クエリ
        $query = Team::with('league'); // リーグ情報をEagerロード ★変更★

        // ★★★ ここから検索条件を追加 ★★★

        // リーグIDによるフィルタリング
        if ($request->filled('league_id')) {
            $query->where('league_id', $request->input('league_id'));
        }

        // チーム名によるフリーワード検索
        if ($request->filled('search_team_name')) {
            $searchTerm = '%' . $request->input('search_team_name') . '%'; // 部分一致検索のため%を追加
            $query->where(function($q) use ($searchTerm) {
                $q->where('team_name', 'like', $searchTerm)
                  ->orWhere('team_nickname', 'like', $searchTerm); // ニックネームでも検索
            });
        }

        // ★★★ ここまで検索条件を追加 ★★★

        // フィルタリングされたチームデータを取得
        $teams = $query->orderBy('team_name')->get(); // 必要に応じてソート順を変更

        // ★★★ 全てのリーグデータを取得し、ビューに渡す ★★★
        $leagues = League::all();

        return view('teams.index', compact('teams', 'leagues')); // ★'leagues'変数をビューに渡す ★変更★
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // チーム作成フォームを表示するために、リーグ選択肢が必要な場合
        $leagues = League::all();
        return view('teams.create', compact('leagues'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // バリデーションルール
        $request->validate([
            'league_id' => 'required|exists:leagues,id', // リーグIDのバリデーションを追加
            'team_name' => 'required|string|max:255|unique:teams,team_name',
            'team_nickname' => 'required|string|max:255|unique:teams,team_nickname',
            'location' => 'required|string|max:255',
            'founded_at' => 'required|date',
        ]);

        Team::create($request->all());

        return redirect()->route('teams.index')->with('success', 'チームが正常に登録されました！');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // Teamモデルにleagueリレーションが追加されている場合、ここでleagueをEagerロードできます。
        $team = Team::with('league')->find($id); // ★'league'をEagerロードに追加★

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

        // ビューにデータを渡す
        return view('teams.show', compact('team', 'recentGames'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        // 編集フォームを表示するために、リーグ選択肢が必要な場合
        $leagues = League::all();
        return view('teams.edit', compact('team', 'leagues'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $request->validate([
            'league_id' => 'required|exists:leagues,id', // リーグIDのバリデーションを追加
            'team_name' => 'required|string|max:255|unique:teams,team_name,' . $team->id,
            'team_nickname' => 'required|string|max:255|unique:teams,team_nickname,' . $team->id,
            'location' => 'required|string|max:255',
            'founded_at' => 'required|date',
        ]);

        $team->update($request->all());

        return redirect()->route('teams.index')->with('success', 'チーム情報が更新されました！');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Team $team)
    {
        $team->delete(); // チームを削除
        return redirect()->route('teams.index')->with('success', 'チームが削除されました！');
    }
}