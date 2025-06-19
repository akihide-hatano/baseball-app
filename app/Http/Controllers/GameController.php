<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Game;
use App\Models\Team;
use App\Models\Player; // 選手モデルを追加 (MVPや勝利投手選択用など)
use App\Models\GamePlayerStat; // GamePlayerStat モデルも必要に応じて
use Carbon\Carbon; // Carbon をuseに追加
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


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

        // ★★★ 実施月によるフィルタリングを追加 (search_dateの代わり) ★★★
        if ($request->filled('search_month')) {
            $searchMonth = $request->input('search_month');
            // whereMonthを使ってgame_dateカラムの月部分をフィルタリング
            $query->whereMonth('game_date', $searchMonth);
        }

        $games = $query->paginate(10);

        // 取得した試合を日付でグループ化
        // ここは日付でグループ化するままでOKです。
        // 検索は月で行われますが、表示は日付ごとになります。
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
        $teams = Team::all();
        $players = Player::all();
        $stadiums = Game::select('stadium')->distinct()->pluck('stadium');
        // dd($stadiums);
        $currentDate = Carbon::now()->format('Y-m-d'); // 今日の日付をデフォルト値として
        $currentTime = Carbon::now()->format('H:i'); // 現在時刻をデフォルト値として

        // dd($teams,$players,$currentDate,$currentTime);

        return view('games.create', compact('teams', 'players', 'currentDate', 'currentTime','stadiums'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
                // バリデーションルールを定義
        $validatedData = $request->validate([
            'home_team_id' => 'required|exists:teams,id',
            'away_team_id' => 'required|exists:teams,id|different:home_team_id', // ホームとアウェイは異なるチーム
            'game_date' => 'required|date',
            'game_time' => 'required|date_format:H:i', // HH:MM形式で必須
            'stadium' => 'required|string|max:255', // 球場名を必須に（文字列として受け取る）
            'home_score' => 'required|integer|min:0', // requiredに変更
            'away_score' => 'required|integer|min:0', // requiredに変更
        ]);

        // ★デバッグポイント1: バリデーション直後のデータを確認★
        // dump('Debug Point 1: Validated Data', $validatedData);

        DB::beginTransaction(); // トランザクション開始
        try {
            // スコアが入力されている場合に結果を決定、そうでなければnull
            $gameResult = null;
            if (isset($validatedData['home_score']) && isset($validatedData['away_score'])) {
                if ($validatedData['home_score'] > $validatedData['away_score']) {
                    $gameResult = 'Home Win'; // ホームチーム目線で「勝ち」
                } elseif ($validatedData['home_score'] < $validatedData['away_score']) {
                    $gameResult = 'Away Win'; // ホームチーム目線で「負け」
                } else {
                    $gameResult = 'Draw'; // 引き分け
                }
            }

            // データベースに試合データを保存
            Game::create([
                'home_team_id' => $validatedData['home_team_id'],
                'away_team_id' => $validatedData['away_team_id'],
                'game_date' => $validatedData['game_date'],
                'game_time' => $validatedData['game_time'],
                'stadium' => $validatedData['stadium'],
                'home_score' => $validatedData['home_score'],
                'away_score' => $validatedData['away_score'],
                'game_result' => $gameResult,
            ]);
            DB::commit(); // トランザクションコミット
            return redirect()->route('games.index')->with('success', '新しい試合が正常に登録されました！');

        } catch (\Exception $e) {
            DB::rollBack(); // エラー時はロールバック
            Log::error("試合登録エラー: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => '試合の登録中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Game $game)
    {

            $game->load([ // リレーションのロードは必要に応じてロード()メソッドで実施
            'homeTeam',
            'awayTeam',
            'gamePlayerStats' => function ($query) {
                $query->with(['player', 'position'])
                    ->orderBy('is_starter', 'desc')
                    ->orderBy('batting_order', 'asc')
                    ->orderBy('player_id', 'asc');
            }
        ]);

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
        $teams = Team::all();
        $players = Player::all();
        $stadiums = Game::select('stadium')->distinct()->pluck('stadium');
        return view('games.edit', compact('game', 'teams', 'players','stadiums'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Game $game)
    {
// バリデーションルールを定義
        $validatedData = $request->validate([
            'home_team_id' => 'required|exists:teams,id',
            'away_team_id' => 'required|exists:teams,id|different:home_team_id',
            'game_date' => 'required|date',
            'game_time' => 'required|date_format:H:i',
            'stadium' => 'required|string|max:255',
            'home_score' => 'nullable|integer|min:0',
            'away_score' => 'nullable|integer|min:0',
            'mvp_player_id' => 'nullable|exists:players,id',
            'pitcher_of_record_id' => 'nullable|exists:players,id',
        ]);

        DB::beginTransaction();
        try {
            // スコアが入力されている場合に結果を決定、そうでなければnull
            $gameResult = null;
            if (isset($validatedData['home_score']) && isset($validatedData['away_score'])) {
                if ($validatedData['home_score'] > $validatedData['away_score']) {
                    $gameResult = 'Home Win';
                } elseif ($validatedData['home_score'] < $validatedData['away_score']) {
                    $gameResult = 'Away Win';
                } else {
                    $gameResult = 'Draw';
                }
            }

            $game->update([
                'home_team_id' => $validatedData['home_team_id'],
                'away_team_id' => $validatedData['away_team_id'],
                'game_date' => $validatedData['game_date'],
                'game_time' => $validatedData['game_time'],
                'stadium' => $validatedData['stadium'],
                'home_score' => $validatedData['home_score'],
                'away_score' => $validatedData['away_score'],
                'game_result' => $gameResult, // ここを game_result に
                'mvp_player_id' => $validatedData['mvp_player_id'] ?? null,
                'pitcher_of_record_id' => $validatedData['pitcher_of_record_id'] ?? null,
            ]);

            DB::commit();
            return redirect()->route('games.show', $game->id)
                            ->with('success', '試合情報が更新されました！');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("試合更新エラー: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => '試合の更新中にエラーが発生しました: ' . $e->getMessage()]);
        }
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