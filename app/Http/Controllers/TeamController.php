<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Team;
use App\Models\League;
use App\Models\Game; // GameモデルはTeamControllerでは通常使われませんが、useに存在するため残します

class TeamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // チームの基本クエリ
        // リーグ情報もEagerロードしておくと、Bladeでのleague.nameアクセスが効率的
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

        // フィルタリングされたチームデータを取得し、チーム名でソート
        // ここではリーグによるグループ化は行わない
        $teams = $query->orderBy('team_name')->get(); // ★★★ ここでget()するだけ ★★★

        // 全てのリーグデータを取得し、ビューに渡す（検索フォーム用）
        $leagues = League::all();

        // ★★★ $teams（グループ化されていないコレクション）と $leagues を渡す ★★★
        return view('teams.index', compact('teams', 'leagues'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $leagues = League::all();
        return view('teams.create', compact('leagues'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'league_id' => 'required|exists:leagues,id',
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
        $team = Team::with('league')->find($id);

        if (!$team) {
            abort(404, 'チームが見つかりませんでした。');
        }

        $team->load([
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

        $allTeamGames = $team->homeGames->merge($team->awayGames);
        $recentGames = $allTeamGames->sortByDesc(function ($game) {
            return $game->game_date . ' ' . $game->game_time;
        })->take(10);

        return view('teams.show', compact('team', 'recentGames'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Team $team)
    {
        $leagues = League::all();
        return view('teams.edit', compact('team', 'leagues'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Team $team)
    {
        $request->validate([
            'league_id' => 'required|exists:leagues,id',
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
        $team->delete();
        return redirect()->route('teams.index')->with('success', 'チームが削除されました！');
    }
}