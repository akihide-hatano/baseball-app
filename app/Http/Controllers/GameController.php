<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request; // Illuminate\Http\Request をuseに追加
use App\Models\Game;
use App\Models\Team;
use Carbon\Carbon;
use App\Models\GamePlayerStat;

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) // ★★★ ここに「Request $request」を追加しました ★★★
    {
        // まず、全てのチームデータを取得し、ビューに渡せるように準備
        $teams = Team::all(); // 検索フォームのチーム選択用に使用

        // 試合の基本クエリ
        $query = Game::with(['homeTeam', 'awayTeam'])
                    ->orderBy('game_date', 'desc')
                    ->orderBy('game_time', 'desc');

        // チームIDによるフィルタリング
        if ($request->filled('team_id')) {
            $teamId = $request->input('team_id');
            $query->where(function($q) use ($teamId) {
                $q->where('home_team_id', $teamId)
                ->orWhere('away_team_id', $teamId);
            });
        }

        // 実施日によるフィルタリング
        if ($request->filled('search_date')) {
            $searchDate = Carbon::parse($request->input('search_date'))->format('Y-m-d');
            $query->whereDate('game_date', $searchDate);
        }

        $games = $query->get(); // フィルタリングされた試合データを取得

        // 取得した試合を日付でグループ化
        $groupedGames = $games->groupBy(function($game) {
            return Carbon::parse($game->game_date)->format('Y年m月d日');
        });

        // 取得したグループ化された試合データと全てのチームデータをビューに渡す
        return view('games.index', compact('groupedGames', 'teams'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return "試合作成フォーム (未実装)";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return "新しい試合を保存しました (未実装)";
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        // 試合IDを使って試合を取得し、関連データをEagerロード
        $game = Game::with([
            'homeTeam', // ホームチーム情報をロード
            'awayTeam', // アウェイチーム情報をロード
            'gamePlayerStats' => function ($query) {
                // gamePlayerStats リレーション内で、選手とポジションもEagerロード
                $query->with(['player', 'position'])
                      ->orderBy('is_starter', 'desc') // スタメンを優先
                      ->orderBy('batting_order', 'asc') // 打順でソート
                      ->orderBy('player_id', 'asc'); // 同じ打順の選手はplayer_idでソート
            }
        ])->find($id);

        // もし試合が見つからなかった場合
        if (!$game) {
            abort(404, '指定された試合が見つかりませんでした。');
        }

        // ビューに試合データと選手成績データを渡す
        return view('games.show', compact('game'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Game $game)
    {
        return "試合編集フォーム (ID: {$game->id}) (未実装)";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Game $game)
    {
        return "試合 (ID: {$game->id}) を更新しました (未実装)";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Game $game)
    {
        $game->delete();
        return redirect()->route('games.index')->with('success', '試合が削除されました！');
    }
}