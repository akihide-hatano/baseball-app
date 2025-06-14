<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Team;
use App\Models\PlayerBattingAbility; // PlayerBattingAbilityモデルの平均値取得のために使用

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
            // ★修正: $request->team_id を使用
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
        return "選手作成フォーム";
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
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

        $player->load([
            'team',
            'yearlyBattingStats' => function ($query) {
                $query->orderBy('year', 'desc');
            },
            'yearlyPitchingStats' => function ($query) {
                $query->orderBy('year', 'desc');
            },
            // Playerモデルで定義されている battingAbilities と pitchingAbilities リレーションをロード
            'battingAbilities' => function ($query) {
                $query->orderBy('year', 'desc'); // 最新のデータを取得
            },
            'pitchingAbilities' => function ($query) {
                $query->orderBy('year', 'desc'); // 最新のデータを取得
            }
        ]);

        // 最新の打撃能力データを取得し、グラフ用に整形
        $playerBattingAbilitiesData = null;
        $playerOverallRankData = null; // 総合ランク用データ

        // battingAbilitiesリレーションから最新のPlayerBattingAbilityモデルを取得
        $latestBattingAbility = $player->battingAbilities->first(); 
        
        if ($latestBattingAbility) {
            // 純粋な打撃能力データ
            $playerBattingAbilitiesData = [
                'labels' => ['ミート', 'パワー', '走力', '守備力', '肩力', '反応'],
                'data' => [
                    $latestBattingAbility->contact_power,
                    $latestBattingAbility->power, // PlayerBattingAbilityモデルのpowerカラム
                    $latestBattingAbility->speed,
                    $latestBattingAbility->fielding,
                    $latestBattingAbility->throwing,
                    $latestBattingAbility->reaction,
                ]
            ];

            // ★ここを修正：roleが「投手」以外の選手の総合能力ランク平均値を取得★
            $averageNonPitcherOverallRank = PlayerBattingAbility::query()
                ->join('players', 'player_batting_abilities.player_id', '=', 'players.id')
                ->where('players.role', '!=', '投手') // roleが投手ではない選手を対象
                ->avg('overall_rank');

            // avg()は結果がなければnullを返すので、0をデフォルトとする
            $averageNonPitcherOverallRank = $averageNonPitcherOverallRank ?? 0;

            $playerOverallRankData = [
                'labels' => ['あなたのランク', '投手以外の平均ランク'], // ラベルを修正
                'data' => [
                    $latestBattingAbility->overall_rank,
                    round($averageNonPitcherOverallRank) // 平均値を四捨五入して表示
                ]
            ];
        }

        // 投球能力データの整形ロジック
        $playerPitchingAbilitiesData = null;
        // pitchingAbilitiesリレーションから最新のPlayerPitchingAbilityモデルを取得
        $latestPitchingAbility = $player->pitchingAbilities->first();

        if ($latestPitchingAbility) {
            $pitchLabels = [];
            $pitchData = [];
            
            // PlayerPitchingAbilityモデルのカラム名とデータ形式に合わせて調整
            // 例: pitch_type_1 から pitch_type_7 まで
            for ($i = 1; $i <= 7; $i++) {
                $pitchTypeField = 'pitch_type_' . $i;
                $pitchInfo = $latestPitchingAbility->$pitchTypeField;

                if ($pitchInfo) {
                    $parts = explode(':', $pitchInfo);
                    if (count($parts) === 2) {
                        $pitchName = trim($parts[0]);
                        $pitchLevel = (int)trim($parts[1]);

                        if (!empty($pitchName) && $pitchLevel >= 0) {
                            $pitchLabels[] = $pitchName;
                            $pitchData[] = $pitchLevel;
                        }
                    }
                }
            }

            if (!empty($pitchLabels)) {
                $playerPitchingAbilitiesData = [
                    'labels' => $pitchLabels,
                    'data' => $pitchData,
                ];
            }
        }
        
        // ビューにデータを渡す
        return view('players.show', compact('player', 'playerBattingAbilitiesData', 'playerPitchingAbilitiesData', 'playerOverallRankData'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Player $player)
    {
        return "選手編集フォーム (ID: {$player->id})";
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Player $player)
    {
        return "選手 (ID: {$player->id}) を更新しました";
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Player $player)
    {
        $player->delete();
        return redirect()->route('players.index')->with('success', '選手が削除されました！');
    }
}