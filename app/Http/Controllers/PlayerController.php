<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Team;
use App\Models\PlayerBattingAbility;
use App\Models\PlayerPitchingAbility;
use Illuminate\Support\Facades\DB; // DBファサードを追加

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
            'battingAbilities' => function ($query) {
                $query->orderBy('year', 'desc');
            },
            'pitchingAbilities' => function ($query) {
                $query->orderBy('year', 'desc');
            }
        ]);

        // 最新の打撃能力データを取得し、グラフ用に整形
        $playerBattingAbilitiesData = null;
        $playerOverallRankData = null;

        $latestBattingAbility = $player->battingAbilities->first();
        if ($latestBattingAbility) {
            $playerBattingAbilitiesData = [
                'labels' => ['ミート', 'パワー', '走力', '守備力', '肩力', '反応'],
                'data' => [
                    $latestBattingAbility->contact_power,
                    $latestBattingAbility->power,
                    $latestBattingAbility->speed,
                    $latestBattingAbility->fielding,
                    $latestBattingAbility->throwing,
                    $latestBattingAbility->reaction,
                ]
            ];

            // 投手以外の総合能力ランク平均値を取得
            $averageNonPitcherOverallRank = PlayerBattingAbility::query()
                ->join('players', 'player_batting_abilities.player_id', '=', 'players.id')
                ->where('players.role', '!=', '投手')
                ->avg('overall_rank');

            $averageNonPitcherOverallRank = $averageNonPitcherOverallRank ?? 0;

            $playerOverallRankData = [
                'labels' => ['あなたのランク', '投手以外の平均ランク'],
                'data' => [
                    $latestBattingAbility->overall_rank,
                    round($averageNonPitcherOverallRank)
                ]
            ];
        }

        // 投球能力データの整形ロジックを修正・分割
        $playerPitchingAbilitiesData = null; // 変化球用
        $playerPitchingFundamentalAbilitiesData = null; // スタミナ・コントロール用
        $playerPitchingVelocityData = null; // 球速（単体）用 ※今回は使用しないが互換性のため残す
        $playerPitchingVelocityComparisonData = null; // 球速比較用
        $playerPitchingOverallRankData = null;

        $latestPitchingAbility = $player->pitchingAbilities->first();

        if ($latestPitchingAbility) {
            // 変化球データ
            $pitchLabels = [];
            $pitchData = [];

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

            // 変化球データが1つ以上あればセット
            if (!empty($pitchLabels)) {
                $playerPitchingAbilitiesData = [
                    'labels' => $pitchLabels,
                    'data' => $pitchData,
                ];
            }

            // スタミナ・コントロールのデータ
            $playerPitchingFundamentalAbilitiesData = [
                'labels' => ['スタミナ', 'コントロール'],
                'data' => [
                    $latestPitchingAbility->pitch_stamina,
                    $latestPitchingAbility->pitch_control
                ],
            ];

            // 球速データ（単体表示用）- 現状使用しないが、データは渡しておく
            $playerPitchingVelocityData = [
                'labels' => ['球速'],
                'data' => [(float)$latestPitchingAbility->average_velocity],
            ];

            // 球速比較データ（チーム内平均と投手全体平均）
            $playerVelocity = (float)$latestPitchingAbility->average_velocity;

            // 投手全体の平均球速
            $averagePitcherOverallVelocity = PlayerPitchingAbility::query()
                ->join('players', 'player_pitching_abilities.player_id', '=', 'players.id')
                ->where('players.role', '=', '投手')
                ->avg('average_velocity');
            $averagePitcherOverallVelocity = round($averagePitcherOverallVelocity ?? 0, 1);

            // 所属チーム内の投手平均球速
            $averageTeamPitcherVelocity = 0;
            if ($player->team_id) {
                $rawTeamPitcherVelocities = PlayerPitchingAbility::query()
                    ->join('players', 'player_pitching_abilities.player_id', '=', 'players.id')
                    ->where('players.team_id', '=', $player->team_id)
                    ->where('players.role', '=', '投手')
                    ->pluck('average_velocity');

                if ($rawTeamPitcherVelocities->isNotEmpty()) {
                    // 各値をfloatにキャストしてから平均を計算
                    $averageTeamPitcherVelocity = $rawTeamPitcherVelocities->map(function($velocity) {
                        return (float)$velocity;
                    })->avg();
                    $averageTeamPitcherVelocity = round($averageTeamPitcherVelocity, 1);
                }
            }

            // ★ここにdd()を追加して、各変数の値とクエリの結果を確認★
            // dd([
            //     'player_id' => $player->id,
            //     'player_role' => $player->role,
            //     'player_team_id' => $player->team_id,
            //     'player_velocity' => $playerVelocity,
            //     'overall_pitchers_velocity' => $averagePitcherOverallVelocity,
            //     'overall_pitchers_count' => PlayerPitchingAbility::query()
            //                                     ->join('players', 'player_pitching_abilities.player_id', '=', 'players.id')
            //                                     ->where('players.role', '=', '投手')
            //                                     ->count(),
            //     'raw_team_pitcher_velocities' => $rawTeamPitcherVelocities ?? [],
            //     'team_pitchers_velocity' => $averageTeamPitcherVelocity,
            //     'team_pitchers_count' => PlayerPitchingAbility::query()
            //                                     ->join('players', 'player_pitching_abilities.player_id', '=', 'players.id')
            //                                     ->where('players.team_id', '=', $player->team_id)
            //                                     ->where('players.role', '=', '投手')
            //                                     ->count(),
            //     'filtered_team_pitchers_data' => PlayerPitchingAbility::query()
            //                                     ->join('players', 'player_pitching_abilities.player_id', '=', 'players.id')
            //                                     ->where('players.team_id', '=', $player->team_id)
            //                                     ->where('players.role', '=', '投手')
            //                                     ->select('players.id as player_id', 'players.name as player_name', 'player_pitching_abilities.average_velocity', 'players.team_id', 'players.role')
            //                                     ->get(),
            // ]);

            $playerPitchingVelocityComparisonData = [
                'labels' => ['あなたの球速', 'チーム内投手平均', '投手全体平均'],
                'data' => [
                    $playerVelocity,
                    $averageTeamPitcherVelocity,
                    $averagePitcherOverallVelocity
                ],
            ];

            // 投手の総合ランクデータと平均値
            // PlayerPitchingAbilityにもoverall_rankカラムがあると仮定
            $pitchingOverallRank = $latestPitchingAbility->overall_rank ?? 75;

            $averagePitcherOverallRank = PlayerPitchingAbility::query()
                ->join('players', 'player_pitching_abilities.player_id', '=', 'players.id')
                ->where('players.role', '=', '投手')
                ->avg('overall_rank');

            $averagePitcherOverallRank = $averagePitcherOverallRank ?? 0;

            $playerPitchingOverallRankData = [
                'labels' => ['あなたのランク', '投手の平均ランク'],
                'data' => [
                    $pitchingOverallRank,
                    round($averagePitcherOverallRank)
                ]
            ];
        }
        return view('players.show', compact('player', 'playerBattingAbilitiesData', 'playerPitchingAbilitiesData', 'playerPitchingFundamentalAbilitiesData', 'playerPitchingVelocityData', 'playerPitchingVelocityComparisonData', 'playerOverallRankData', 'playerPitchingOverallRankData'));
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