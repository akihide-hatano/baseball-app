<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\GamePlayerStat; // GamePlayerStat モデルをuseに追加

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // 全ての試合を取得するなどのロジック（必要であれば実装）
        // 例: $games = Game::paginate(10);
        // return view('games.index', compact('games'));
        return "試合一覧ページ (未実装)"; // 現在はシンプルな表示
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

        // ★ここを追加: ddで$gameの中身を確認★
        // dd($game->gamePlayerStats->first());

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