<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use Carbon\Carbon;
use App\Models\GamePlayerStat; // GamePlayerStat モデルをuseに追加

class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
         // 最新の試合を日付と時刻で降順に取得
        $games = Game::with(['homeTeam', 'awayTeam'])
                      ->orderBy('game_date', 'desc') // 日付で降順ソート
                      ->orderBy('game_time', 'desc') // 時刻で降順ソート (同日内の順序のため)
                      ->get(); // まず全ての、または十分な件数の試合を取得

        // ★ここを追加: rawな$gamesの中身を確認★
        // dd($games);

        // 取得した試合を日付でグループ化
        // 日付をキーとして、その日付の試合のコレクションがネストされる
        // Carbon::parse()->format('Y年m月d日') を使って、表示用の日付文字列をキーにする
        $groupedGames = $games->groupBy(function($game) {
            return Carbon::parse($game->game_date)->format('Y年m月d日');
        });

        // オプション: グループ化された日付の中で、最新の10グループのみ表示したい場合
        // $groupedGames = $groupedGames->take(10); // 上位10日付のグループのみを取得

        // または、「直近10試合」が「合計10試合」という意味で、
        // かつ日付でグループ化したい場合は、以下のようなロジックになることもあります。
        // $recentGames = Game::with(['homeTeam', 'awayTeam'])
        //                      ->orderBy('game_date', 'desc')
        //                      ->orderBy('game_time', 'desc')
        //                      ->take(10)
        //                      ->get();
        // $groupedGames = $recentGames->groupBy(function($game) {
        //     return Carbon::parse($game->game_date)->format('Y年m月d日');
        // });

        // ★ここを追加: $groupedGamesの中身を確認★
        // dd($groupedGames);

        // 取得したグループ化された試合データをビューに渡す
        return view('games.index', compact('groupedGames'));
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
        // dd($game);
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