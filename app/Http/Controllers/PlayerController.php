<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Team;
use App\Models\YearlyBattingStat;
use App\Models\YearlyPitchingStat;
use App\Models\PlayerBattingAbility; // PlayerBattingAbility も使われているのでuseに追加
use App\Models\PlayerPitchingAbility; // PlayerPitchingAbility も使われているのでuseに追加
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // エラーログのために追加

class PlayerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $teams = Team::all();

        $query = Player::with('team');

        if ($request->filled('team_id') && $request->team_id != '') {
            $query->where('team_id', $request->team_id);
        }

        $players = $query->paginate(12);
        return view('players.index', compact('players', 'teams'));
    }

    /**
     * Show the form for creating a new resource.
     * 新しい選手作成フォームを表示します。
     */
    public function create()
    {
        $teams = Team::all(); // 所属チーム選択用
        $playerRoles = [      // 役割選択用
            '野手' => '野手',
            '投手' => '投手',
        ];
        // フォームで表示する現在の年度を設定
        $currentYear = date('Y');

        // ★★★ createメソッドでのdd確認 ★★★
        // ビューに渡されるデータが正しいかを確認
        // dd(compact('teams', 'playerRoles', 'currentYear'));

        return view('players.create', compact('teams', 'playerRoles', 'currentYear'));
    }

    /**
     * Store a newly created resource in storage.
     * フォームから送信されたデータを受け取り、バリデーションとデータベースへの保存を行います。
     */
    public function store(Request $request)
    {
        // 共通のバリデーションルール
        $rules = [
            'team_id' => 'required|exists:teams,id',
            'player_name' => 'required|string|max:255',
            'role' => 'required|in:野手,投手',
            'jersey_number' => 'nullable|integer|min:0|unique:players,jersey_number,NULL,id,team_id,' . $request->input('team_id'),
            'birth_date' => 'nullable|date',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1), // 成績の年度
        ];

        // 役割に応じた追加のバリデーションルール
        if ($request->input('role') === '野手') {
            $rules = array_merge($rules, [
                'batting_games' => 'nullable|integer|min:0',
                'at_bats' => 'nullable|integer|min:0',
                'runs' => 'nullable|integer|min:0',
                'hits' => 'nullable|integer|min:0',
                'doubles' => 'nullable|integer|min:0',
                'triples' => 'nullable|integer|min:0',
                'home_runs' => 'nullable|integer|min:0',
                'rbi' => 'nullable|integer|min:0',
                'stolen_bases' => 'nullable|integer|min:0',
                'caught_stealing' => 'nullable|integer|min:0',
                'walks' => 'nullable|integer|min:0',
                'strikeouts' => 'nullable|integer|min:0',
                'sacrifice_hits' => 'nullable|integer|min:0',
                'sacrifice_flies' => 'nullable|integer|min:0',
                'batting_average' => 'nullable|numeric|min:0|max:1', // 打率 0.000から1.000
            ]);
        } elseif ($request->input('role') === '投手') {
            $rules = array_merge($rules, [
                'pitching_games' => 'nullable|integer|min:0',
                'wins' => 'nullable|integer|min:0',
                'losses' => 'nullable|integer|min:0',
                'saves' => 'nullable|integer|min:0',
                'holds' => 'nullable|integer|min:0',
                'innings_pitched_out' => 'nullable|integer|min:0',
                'hits_given' => 'nullable|integer|min:0',
                'home_runs_given' => 'nullable|integer|min:0',
                'walks_given' => 'nullable|integer|min:0',
                'strikeouts_thrown' => 'nullable|integer|min:0',
                'earned_run_average' => 'nullable|numeric|min:0', // 防御率
            ]);
        }

        $validatedData = $request->validate($rules);

        // ★★★ storeメソッドでのdd確認 ★★★
        // フォームから送信されたデータが正しくバリデーションされているか確認
        // dd($validatedData, $request->all());

        // トランザクションを開始 (選手と成績をまとめて保存するため)
        DB::beginTransaction();
        try {
            // 選手データを保存
            $player = Player::create([
                'team_id' => $validatedData['team_id'],
                'player_name' => $validatedData['player_name'],
                'role' => $validatedData['role'],
                'jersey_number' => $validatedData['jersey_number'],
                'birth_date' => $validatedData['birth_date'],
            ]);

            // 役割に応じて成績データを保存
            if ($player->role === '野手') {
                YearlyBattingStat::create([
                    'player_id' => $player->id,
                    'year' => $validatedData['year'],
                    'games' => $validatedData['batting_games'] ?? null,
                    'at_bats' => $validatedData['at_bats'] ?? null,
                    'runs' => $validatedData['runs'] ?? null,
                    'hits' => $validatedData['hits'] ?? null,
                    'doubles' => $validatedData['doubles'] ?? null,
                    'triples' => $validatedData['triples'] ?? null,
                    'home_runs' => $validatedData['home_runs'] ?? null,
                    'rbi' => $validatedData['rbi'] ?? null,
                    'stolen_bases' => $validatedData['stolen_bases'] ?? null,
                    'caught_stealing' => $validatedData['caught_stealing'] ?? null,
                    'walks' => $validatedData['walks'] ?? null,
                    'strikeouts' => $validatedData['strikeouts'] ?? null,
                    'sacrifice_hits' => $validatedData['sacrifice_hits'] ?? null,
                    'sacrifice_flies' => $validatedData['sacrifice_flies'] ?? null,
                    'batting_average' => $validatedData['batting_average'] ?? null,
                ]);
            } elseif ($player->role === '投手') {
                YearlyPitchingStat::create([
                    'player_id' => $player->id,
                    'year' => $validatedData['year'],
                    'games' => $validatedData['pitching_games'] ?? null,
                    'wins' => $validatedData['wins'] ?? null,
                    'losses' => $validatedData['losses'] ?? null,
                    'saves' => $validatedData['saves'] ?? null,
                    'holds' => $validatedData['holds'] ?? null,
                    'innings_pitched_out' => $validatedData['innings_pitched_out'] ?? null,
                    'hits_given' => $validatedData['hits_given'] ?? null,
                    'home_runs_given' => $validatedData['home_runs_given'] ?? null,
                    'walks_given' => $validatedData['walks_given'] ?? null,
                    'strikeouts_thrown' => $validatedData['strikeouts_thrown'] ?? null,
                    'earned_run_average' => $validatedData['earned_run_average'] ?? null,
                ]);
            }

            DB::commit(); // 全ての処理が成功したらコミット
            return redirect()->route('players.index')->with('success', '新しい選手と成績が正常に登録されました！');

        } catch (\Exception $e) {
            DB::rollBack(); // エラーが発生したらロールバック
            Log::error("選手登録エラー: " . $e->getMessage()); // エラーログ出力
            return back()->withInput()->withErrors(['error' => '選手の登録中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     * Route Model Binding を使用して Player インスタンスを直接受け取ります。
     */
    public function show(Player $player)
    {
        // $player は既に自動的に取得されているため、Player::find($id) は不要です。
        // 関連するデータをEager Loadします
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

        // ★★★ showメソッドでのdd確認 ★★★
        // Route Model Bindingで取得された選手オブジェクトと、Eager Loadingされた関連データを確認
        // dd($player);

        // その他のデータ整形ロジックは既存のものを維持
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
            $averageNonPitcherOverallRank = DB::table('player_batting_abilities')
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

        $playerPitchingAbilitiesData = null;
        $playerPitchingFundamentalAbilitiesData = null;
        $playerPitchingVelocityData = null;
        $playerPitchingVelocityComparisonData = null;
        $playerPitchingOverallRankData = null;
        $latestPitchingAbility = $player->pitchingAbilities->first();
        if ($latestPitchingAbility) {
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
            if (!empty($pitchLabels)) {
                $playerPitchingAbilitiesData = [
                    'labels' => $pitchLabels,
                    'data' => $pitchData,
                ];
            }
            $playerPitchingFundamentalAbilitiesData = [
                'labels' => ['スタミナ', 'コントロール'],
                'data' => [
                    $latestPitchingAbility->pitch_stamina,
                    $latestPitchingAbility->pitch_control
                ],
            ];
            $playerPitchingVelocityData = [
                'labels' => ['球速'],
                'data' => [(float)$latestPitchingAbility->average_velocity],
            ];

            $playerVelocity = (float)$latestPitchingAbility->average_velocity;
            $averagePitcherOverallVelocity = DB::table('player_pitching_abilities')
                ->join('players', 'player_pitching_abilities.player_id', '=', 'players.id')
                ->where('players.role', '=', '投手')
                ->avg('average_velocity');
            $averagePitcherOverallVelocity = round($averagePitcherOverallVelocity ?? 0, 1);

            $averageTeamPitcherVelocity = 0;
            if ($player->team_id) {
                $rawTeamPitcherVelocities = DB::table('player_pitching_abilities')
                    ->join('players', 'player_pitching_abilities.player_id', '=', 'players.id')
                    ->where('players.team_id', '=', $player->team_id)
                    ->where('players.role', '=', '投手')
                    ->pluck('average_velocity');

                if ($rawTeamPitcherVelocities->isNotEmpty()) {
                    $averageTeamPitcherVelocity = $rawTeamPitcherVelocities->map(function($velocity) {
                        return (float)$velocity;
                    })->avg();
                    $averageTeamPitcherVelocity = round($averageTeamPitcherVelocity, 1);
                }
            }
            $playerPitchingVelocityComparisonData = [
                'labels' => ['あなたの球速', 'チーム内投手平均', '投手全体平均'],
                'data' => [
                    $playerVelocity,
                    $averageTeamPitcherVelocity,
                    $averagePitcherOverallVelocity
                ],
            ];

            $pitchingOverallRank = $latestPitchingAbility->overall_rank ?? 75;
            $averagePitcherOverallRank = DB::table('player_pitching_abilities')
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
     * Route Model Binding を使用して Player インスタンスを直接受け取ります。
     */
    public function edit(Player $player)
    {
        $teams = Team::all(); // 所属チーム選択用
        $playerRoles = [      // 役割選択用
            '野手' => '野手',
            '投手' => '投手',
        ];
        // 編集対象の選手とその関連データ（最新の成績）をロード
        $player->load([
            'yearlyBattingStats' => function ($query) {
                $query->orderBy('year', 'desc');
            },
            'yearlyPitchingStats' => function ($query) {
                $query->orderBy('year', 'desc');
            }
        ]);
        $currentYear = date('Y'); // 現在の年度をフォームのデフォルトとして渡す

        // ★★★ editメソッドでのdd確認 ★★★
        // 編集対象の選手オブジェクトと、ビューに渡されるデータを確認
        // dd(compact('player', 'teams', 'playerRoles', 'currentYear'));

        return view('players.edit', compact('player', 'teams', 'playerRoles', 'currentYear'));
    }

    /**
     * Update the specified resource in storage.
     * Route Model Binding を使用して Player インスタンスを直接受け取ります。
     */
    public function update(Request $request, Player $player)
    {
        // 共通のバリデーションルール
        $rules = [
            'team_id' => 'required|exists:teams,id',
            'player_name' => 'required|string|max:255',
            'role' => 'required|in:野手,投手',
            // 更新時のユニークルールは、自分自身のIDを除外する必要がある
            'jersey_number' => 'nullable|integer|min:0|unique:players,jersey_number,' . $player->id . ',id,team_id,' . $request->input('team_id'),
            'birth_date' => 'nullable|date',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1), // 成績の年度
        ];

        // 役割に応じた追加のバリデーションルール
        if ($request->input('role') === '野手') {
            $rules = array_merge($rules, [
                'batting_games' => 'nullable|integer|min:0',
                'at_bats' => 'nullable|integer|min:0',
                'runs' => 'nullable|integer|min:0',
                'hits' => 'nullable|integer|min:0',
                'doubles' => 'nullable|integer|min:0',
                'triples' => 'nullable|integer|min:0',
                'home_runs' => 'nullable|integer|min:0',
                'rbi' => 'nullable|integer|min:0',
                'stolen_bases' => 'nullable|integer|min:0',
                'caught_stealing' => 'nullable|integer|min:0',
                'walks' => 'nullable|integer|min:0',
                'strikeouts' => 'nullable|integer|min:0',
                'sacrifice_hits' => 'nullable|integer|min:0',
                'sacrifice_flies' => 'nullable|integer|min:0',
                'batting_average' => 'nullable|numeric|min:0|max:1',
            ]);
        } elseif ($request->input('role') === '投手') {
            $rules = array_merge($rules, [
                'pitching_games' => 'nullable|integer|min:0',
                'wins' => 'nullable|integer|min:0',
                'losses' => 'nullable|integer|min:0',
                'saves' => 'nullable|integer|min:0',
                'holds' => 'nullable|integer|min:0',
                'innings_pitched_out' => 'nullable|integer|min:0',
                'hits_given' => 'nullable|integer|min:0',
                'home_runs_given' => 'nullable|integer|min:0',
                'walks_given' => 'nullable|integer|min:0',
                'strikeouts_thrown' => 'nullable|integer|min:0',
                'earned_run_average' => 'nullable|numeric|min:0',
            ]);
        }

        $validatedData = $request->validate($rules);

        // ★★★ updateメソッドでのdd確認 ★★★
        // 更新対象の選手オブジェクト、バリデーション後のデータ、元のリクエストデータを確認
        // dd($player, $validatedData, $request->all());

        DB::beginTransaction();
        try {
            // 選手データを更新
            $player->update([
                'team_id' => $validatedData['team_id'],
                'player_name' => $validatedData['player_name'],
                'role' => $validatedData['role'],
                'jersey_number' => $validatedData['jersey_number'],
                'birth_date' => $validatedData['birth_date'],
            ]);

            // 年度の成績を更新または新規作成
            if ($player->role === '野手') {
                YearlyBattingStat::updateOrCreate(
                    ['player_id' => $player->id, 'year' => $validatedData['year']],
                    [
                        'games' => $validatedData['batting_games'] ?? null,
                        'at_bats' => $validatedData['at_bats'] ?? null,
                        'runs' => $validatedData['runs'] ?? null,
                        'hits' => $validatedData['hits'] ?? null,
                        'doubles' => $validatedData['doubles'] ?? null,
                        'triples' => $validatedData['triples'] ?? null,
                        'home_runs' => $validatedData['home_runs'] ?? null,
                        'rbi' => $validatedData['rbi'] ?? null,
                        'stolen_bases' => $validatedData['stolen_bases'] ?? null,
                        'caught_stealing' => $validatedData['caught_stealing'] ?? null,
                        'walks' => $validatedData['walks'] ?? null,
                        'strikeouts' => $validatedData['strikeouts'] ?? null,
                        'sacrifice_hits' => $validatedData['sacrifice_hits'] ?? null,
                        'sacrifice_flies' => $validatedData['sacrifice_flies'] ?? null,
                        'batting_average' => $validatedData['batting_average'] ?? null,
                    ]
                );
                // もし役割が投手から野手に変わった場合、古い投球成績は削除（オプション）
                YearlyPitchingStat::where('player_id', $player->id)
                                  ->where('year', $validatedData['year'])
                                  ->delete();
            } elseif ($player->role === '投手') {
                YearlyPitchingStat::updateOrCreate(
                    ['player_id' => $player->id, 'year' => $validatedData['year']],
                    [
                        'games' => $validatedData['pitching_games'] ?? null,
                        'wins' => $validatedData['wins'] ?? null,
                        'losses' => $validatedData['losses'] ?? null,
                        'saves' => $validatedData['saves'] ?? null,
                        'holds' => $validatedData['holds'] ?? null,
                        'innings_pitched_out' => $validatedData['innings_pitched_out'] ?? null,
                        'hits_given' => $validatedData['hits_given'] ?? null,
                        'home_runs_given' => $validatedData['home_runs_given'] ?? null,
                        'walks_given' => $validatedData['walks_given'] ?? null,
                        'strikeouts_thrown' => $validatedData['strikeouts_thrown'] ?? null,
                        'earned_run_average' => $validatedData['earned_run_average'] ?? null,
                    ]
                );
                // もし役割が野手から投手に変わった場合、古い打撃成績は削除（オプション）
                YearlyBattingStat::where('player_id', $player->id)
                                 ->where('year', $validatedData['year'])
                                 ->delete();
            }

            DB::commit();
            return redirect()->route('players.index')->with('success', '選手情報が正常に更新されました！');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("選手更新エラー: " . $e->getMessage()); // エラーログ出力
            return back()->withInput()->withErrors(['error' => '選手の更新中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * Route Model Binding を使用して Player インスタンスを直接受け取ります。
     */
    public function destroy(Player $player)
    {
        // ★★★ destroyメソッドでのdd確認 ★★★
        // 削除対象の選手オブジェクトが正しく取得されているか確認 (削除前に実行される)
        // dd($player);

        DB::beginTransaction();
        try {
            $player->delete(); // 関連する yearly_batting_stats/yearly_pitching_stats も cascade で削除されます

            DB::commit();
            return redirect()->route('players.index')->with('success', '選手が正常に削除されました！');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("選手削除エラー: " . $e->getMessage()); // エラーログ出力
            return back()->withErrors(['error' => '選手の削除中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }
}
