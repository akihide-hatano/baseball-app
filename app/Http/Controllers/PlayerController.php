<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Team;

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $teams = Team::all();

        // 選手をEager Loadingで取得
        $query = Player::with('team');

        // リクエストにteam_idが存在する場合、そのチームで選手を絞り込む
        if ($request->filled('team_id') && $request->team_id != '') {
            $query->where('team_id', $request->team_id);
        }

        // ページネーションを適用
        $players = $query->paginate(12);
        // ビューに選手データとチームデータを渡す
        return view('players.index', compact('players', 'teams'));
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
    public function show($id)
    {
        // ID を使って Player モデルを検索
        $player = Player::find($id);

        // もし $player が取得できなかった場合（nullの場合）のハンドリング
        if (!$player) {
            abort(404, '選手が見つかりませんでした。');
        }

        // ここから修正点です
        $player->load([
            'team', // チーム情報もロード
            'yearlyBattingStats' => function ($query) {
                $query->orderBy('year', 'desc'); // 最新の年を先に取得
            },
            'yearlyPitchingStats' => function ($query) { // 投球成績もロード
                $query->orderBy('year', 'desc'); // 最新の年を先に取得
            },
            // PlayerBattingAbility のリレーションをロード
            'battingAbilities' => function ($query) {
                $query->orderBy('year', 'desc'); // 最新のデータを取得するために年でソート
            }
        ]);

        // 最新の打撃能力データを取得し、グラフ用に整形
        $playerBattingAbilitiesData = null;
        if ($player->battingAbilities->isNotEmpty()) {
            $latestAbility = $player->battingAbilities->first(); // 最新の年度の能力を取得
            $playerBattingAbilitiesData = [
                'labels' => ['ミート', 'パワー', '走力', '守備力', '肩力', '反応'],
                'data' => [
                    $latestAbility->contact_power,
                    $latestAbility->power,
                    $latestAbility->speed,
                    $latestAbility->fielding,
                    $latestAbility->throwing,
                    $latestAbility->reaction,
                ]
            ];
        }

        // ビューに 'playerBattingAbilitiesData' も渡す
        return view('players.show', compact('player', 'playerBattingAbilitiesData'));
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