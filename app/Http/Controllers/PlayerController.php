<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Team;
use App\Models\YearlyBattingStat;
use App\Models\YearlyPitchingStat;
use App\Models\PlayerBattingAbility;
use App\Models\PlayerPitchingAbility;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
        $currentYear = date('Y');

        return view('players.create', compact('teams', 'playerRoles', 'currentYear'));
    }

    /**
     * Store a newly created resource in storage.
     * フォームから送信されたデータを受け取り、バリデーションとデータベースへの保存を行います。
     */
    public function store(Request $request)
    {
        // dd($request->all()); // ★データ確認用。問題解決後コメントアウトまたは削除★

        // 共通のバリデーションルール
        $rules = [
            'team_id' => 'required|exists:teams,id',
            'player_name' => 'required|string|max:255',
            'role' => 'required|in:野手,投手',
            'jersey_number' => 'nullable|integer|min:0|unique:players,jersey_number,NULL,id,team_id,' . $request->input('team_id'),
            'date_of_birth' => 'nullable|date',
            'height' => 'nullable|integer|min:0',
            'weight' => 'nullable|integer|min:0',
            'specialty' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'hometown' => 'nullable|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1), // 成績の年度
        ];

        // 役割に応じた追加のバリデーションルール
        if ($request->input('role') === '野手') {
            $rules = array_merge($rules, [
                // Bladeとコントローラーのname属性に合わせる
                'games' => 'nullable|integer|min:0',
                'plate_appearances' => 'nullable|integer|min:0',
                'at_bats' => 'nullable|integer|min:0',
                'hits' => 'nullable|integer|min:0',
                'doubles' => 'nullable|integer|min:0',
                'triples' => 'nullable|integer|min:0',
                'home_runs' => 'nullable|integer|min:0',
                'rbi' => 'nullable|integer|min:0',
                'stolen_bases' => 'nullable|integer|min:0',
                'caught_stealing' => 'nullable|integer|min:0',
                'strikeouts' => 'nullable|integer|min:0',
                'walks' => 'nullable|integer|min:0',
                'hit_by_pitch' => 'nullable|integer|min:0',
                'sac_bunts' => 'nullable|integer|min:0',
                'sac_flies' => 'nullable|integer|min:0',
                'double_plays' => 'nullable|integer|min:0',
                'errors' => 'nullable|integer|min:0',
                'runs_scored' => 'nullable|integer|min:0',
                // 計算で入れるので、これらのバリデーションは不要または簡易的にする
                // 'batting_average' => 'nullable|numeric|min:0|max:1',
                // 'on_base_percentage' => 'nullable|numeric|min:0|max:1',
                // 'slugging_percentage' => 'nullable|numeric|min:0|max:1',
                // 'ops' => 'nullable|numeric|min:0|max:2',
                'ops_plus' => 'nullable|numeric',
                'wrc_plus' => 'nullable|numeric',
            ]);
        } elseif ($request->input('role') === '投手') {
            $rules = array_merge($rules, [
                // Bladeのname属性に合わせる
                'games' => 'nullable|integer|min:0', // 'pitching_games' から 'games' に変更
                'starts' => 'nullable|integer|min:0', // 新規
                'wins' => 'nullable|integer|min:0',
                'losses' => 'nullable|integer|min:0',
                'saves' => 'nullable|integer|min:0',
                'holds' => 'nullable|integer|min:0',
                'innings_pitched' => 'nullable|numeric|min:0', // 'innings_pitched_out' から 'innings_pitched' に変更
                'hits_allowed' => 'nullable|integer|min:0', // 'hits_given' から 'hits_allowed' に変更
                'home_runs_allowed' => 'nullable|integer|min:0', // 'home_runs_given' から 'home_runs_allowed' に変更
                'walks_allowed' => 'nullable|integer|min:0', // 'walks_given' から 'walks_allowed' に変更
                'hit_by_pitch_allowed' => 'nullable|integer|min:0', // 新規
                'strikeouts_pitched' => 'nullable|integer|min:0', // 'strikeouts_thrown' から 'strikeouts_pitched' に変更
                'runs_allowed' => 'nullable|integer|min:0', // 新規
                'earned_runs' => 'nullable|integer|min:0', // 新規
                'pitches_thrown' => 'nullable|integer|min:0', // 新規
                // 計算で入れるので、これらのバリデーションは不要
                // 'earned_run_average' => 'nullable|numeric|min:0',
                // 'whip' => 'nullable|numeric|min:0',
                // 'strikeout_walk_ratio' => 'nullable|numeric|min:0',
            ]);
        }

        $validatedData = $request->validate($rules);

        DB::beginTransaction();
        try {
            // 選手データを保存
            $player = Player::create([
                'team_id' => $validatedData['team_id'],
                'name' => $validatedData['player_name'],
                'role' => $validatedData['role'],
                'jersey_number' => $validatedData['jersey_number'],
                'date_of_birth' => $validatedData['date_of_birth'],
                'height' => $validatedData['height'] ?? null,
                'weight' => $validatedData['weight'] ?? null,
                'specialty' => $validatedData['specialty'] ?? null,
                'description' => $validatedData['description'] ?? null,
                'hometown' => $validatedData['hometown'] ?? null,
            ]);

            // 役割に応じて成績データを保存
            if ($player->role === '野手') {
                // ... 野手成績の保存ロジック (前回の修正と変更なし) ...
                $hits = $validatedData['hits'] ?? 0;
                $at_bats = $validatedData['at_bats'] ?? 0;
                $walks = $validatedData['walks'] ?? 0;
                $hit_by_pitch = $validatedData['hit_by_pitch'] ?? 0;
                $sac_flies = $validatedData['sac_flies'] ?? 0;
                $doubles = $validatedData['doubles'] ?? 0;
                $triples = $validatedData['triples'] ?? 0;
                $home_runs = $validatedData['home_runs'] ?? 0;

                $batting_average = ($at_bats > 0) ? round($hits / $at_bats, 3) : 0.000;
                $obp_denominator = $at_bats + $walks + $hit_by_pitch + $sac_flies;
                $on_base_percentage = ($obp_denominator > 0) ? round(($hits + $walks + $hit_by_pitch) / $obp_denominator, 3) : 0.000;
                $singles = $hits - $doubles - $triples - $home_runs;
                $total_bases = $singles + (2 * $doubles) + (3 * $triples) + (4 * $home_runs);
                $slugging_percentage = ($at_bats > 0) ? round($total_bases / $at_bats, 3) : 0.000;
                $ops = $on_base_percentage + $slugging_percentage;

                YearlyBattingStat::create([
                    'player_id' => $player->id,
                    'year' => $validatedData['year'],
                    'team_id' => $validatedData['team_id'],
                    'games' => $validatedData['games'] ?? 0,
                    'plate_appearances' => $validatedData['plate_appearances'] ?? 0,
                    'at_bats' => $at_bats,
                    'hits' => $hits,
                    'doubles' => $doubles,
                    'triples' => $triples,
                    'home_runs' => $home_runs,
                    'rbi' => $validatedData['rbi'] ?? 0,
                    'stolen_bases' => $validatedData['stolen_bases'] ?? 0,
                    'caught_stealing' => $validatedData['caught_stealing'] ?? 0,
                    'strikeouts' => $validatedData['strikeouts'] ?? 0,
                    'walks' => $walks,
                    'hit_by_pitch' => $hit_by_pitch,
                    'sac_bunts' => $validatedData['sac_bunts'] ?? 0,
                    'sac_flies' => $sac_flies,
                    'double_plays' => $validatedData['double_plays'] ?? 0,
                    'errors' => $validatedData['errors'] ?? 0,
                    'runs_scored' => $validatedData['runs_scored'] ?? 0,
                    'batting_average' => $batting_average,
                    'on_base_percentage' => $on_base_percentage,
                    'slugging_percentage' => $slugging_percentage,
                    'ops' => $ops,
                    'ops_plus' => $validatedData['ops_plus'] ?? null,
                    'wrc_plus' => $validatedData['wrc_plus'] ?? null,
                ]);
                YearlyPitchingStat::where('player_id', $player->id)
                                  ->where('year', $validatedData['year'])
                                  ->delete();
            } elseif ($player->role === '投手') {
                // 基本的な投球成績を取得（nullの場合は0に）
                $innings_pitched = $validatedData['innings_pitched'] ?? 0.0;
                $walks_allowed = $validatedData['walks_allowed'] ?? 0;
                $hits_allowed = $validatedData['hits_allowed'] ?? 0;
                $strikeouts_pitched = $validatedData['strikeouts_pitched'] ?? 0;
                $earned_runs = $validatedData['earned_runs'] ?? 0;

                // ★★★ 投球指標の計算 ★★★
                // 防御率 (ERA)
                // 9イニングあたりの自責点: (自責点 * 9) / 投球回
                $earned_run_average = ($innings_pitched > 0) ? round(($earned_runs * 9) / $innings_pitched, 2) : 0.00;

                // WHIP (Walks + Hits per Inning Pitched)
                // (与四球 + 被安打) / 投球回
                $whip = ($innings_pitched > 0) ? round(($walks_allowed + $hits_allowed) / $innings_pitched, 2) : 0.00;

                // K/BB (Strikeout to Walk Ratio)
                // 奪三振 / 与四球
                $strikeout_walk_ratio = ($walks_allowed > 0) ? round($strikeouts_pitched / $walks_allowed, 2) : 0.00;


                YearlyPitchingStat::create([
                    'player_id' => $player->id,
                    'year' => $validatedData['year'],
                    'team_id' => $validatedData['team_id'],

                    // ★★★ ここをDBカラム名に正確にマッピングする ★★★
                    'games' => $validatedData['games'] ?? 0, // form: games
                    'starts' => $validatedData['starts'] ?? 0, // form: starts
                    'wins' => $validatedData['wins'] ?? 0,
                    'losses' => $validatedData['losses'] ?? 0,
                    'saves' => $validatedData['saves'] ?? 0,
                    'holds' => $validatedData['holds'] ?? 0,
                    'innings_pitched' => $innings_pitched, // form: innings_pitched
                    'hits_allowed' => $hits_allowed, // form: hits_allowed
                    'home_runs_allowed' => $validatedData['home_runs_allowed'] ?? 0, // form: home_runs_allowed
                    'walks_allowed' => $walks_allowed, // form: walks_allowed
                    'hit_by_pitch_allowed' => $validatedData['hit_by_pitch_allowed'] ?? 0, // form: hit_by_pitch_allowed
                    'strikeouts_pitched' => $strikeouts_pitched, // form: strikeouts_pitched
                    'runs_allowed' => $validatedData['runs_allowed'] ?? 0, // form: runs_allowed
                    'earned_runs' => $earned_runs, // form: earned_runs
                    'pitches_thrown' => $validatedData['pitches_thrown'] ?? 0, // form: pitches_thrown

                    // 計算結果を保存
                    'earned_run_average' => $earned_run_average,
                    'whip' => $whip,
                    'strikeout_walk_ratio' => $strikeout_walk_ratio,
                ]);
                YearlyBattingStat::where('player_id', $player->id)
                                 ->where('year', $validatedData['year'])
                                 ->delete();
            }

            DB::commit();
            return redirect()->route('players.index')->with('success', '新しい選手と成績が正常に登録されました！');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("選手登録エラー: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => '選手の登録中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Player $player)
    {
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
            $averagePitcherOverallVelocity = $averagePitcherOverallVelocity ?? 0;

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
        $player->load([
            'yearlyBattingStats' => function ($query) {
                $query->orderBy('year', 'desc');
            },
            'yearlyPitchingStats' => function ($query) {
                $query->orderBy('year', 'desc');
            }
        ]);
        $currentYear = date('Y');

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
            'jersey_number' => 'nullable|integer|min:0|unique:players,jersey_number,' . $player->id . ',id,team_id,' . $request->input('team_id'),
            'date_of_birth' => 'nullable|date',
            'height' => 'nullable|integer|min:0',
            'weight' => 'nullable|integer|min:0',
            'specialty' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:1000',
            'hometown' => 'nullable|string|max:255',
            'year' => 'required|integer|min:1900|max:' . (date('Y') + 1),
        ];

        if ($request->input('role') === '野手') {
            $rules = array_merge($rules, [
                'games' => 'nullable|integer|min:0',
                'plate_appearances' => 'nullable|integer|min:0',
                'at_bats' => 'nullable|integer|min:0',
                'hits' => 'nullable|integer|min:0',
                'doubles' => 'nullable|integer|min:0',
                'triples' => 'nullable|integer|min:0',
                'home_runs' => 'nullable|integer|min:0',
                'rbi' => 'nullable|integer|min:0',
                'stolen_bases' => 'nullable|integer|min:0',
                'caught_stealing' => 'nullable|integer|min:0',
                'strikeouts' => 'nullable|integer|min:0',
                'walks' => 'nullable|integer|min:0',
                'hit_by_pitch' => 'nullable|integer|min:0',
                'sac_bunts' => 'nullable|integer|min:0',
                'sac_flies' => 'nullable|integer|min:0',
                'double_plays' => 'nullable|integer|min:0',
                'errors' => 'nullable|integer|min:0',
                'runs_scored' => 'nullable|integer|min:0',
                'ops_plus' => 'nullable|numeric',
                'wrc_plus' => 'nullable|numeric',
            ]);
        } elseif ($request->input('role') === '投手') {
            $rules = array_merge($rules, [
                'games' => 'nullable|integer|min:0', // 'pitching_games' から 'games' に変更
                'starts' => 'nullable|integer|min:0', // 新規
                'wins' => 'nullable|integer|min:0',
                'losses' => 'nullable|integer|min:0',
                'saves' => 'nullable|integer|min:0',
                'holds' => 'nullable|integer|min:0',
                'innings_pitched' => 'nullable|numeric|min:0', // 'innings_pitched_out' から 'innings_pitched' に変更
                'hits_allowed' => 'nullable|integer|min:0', // 'hits_given' から 'hits_allowed' に変更
                'home_runs_allowed' => 'nullable|integer|min:0', // 'home_runs_given' から 'home_runs_allowed' に変更
                'walks_allowed' => 'nullable|integer|min:0', // 'walks_given' から 'walks_allowed' に変更
                'hit_by_pitch_allowed' => 'nullable|integer|min:0', // 新規
                'strikeouts_pitched' => 'nullable|integer|min:0', // 'strikeouts_thrown' から 'strikeouts_pitched' に変更
                'runs_allowed' => 'nullable|integer|min:0', // 新規
                'earned_runs' => 'nullable|integer|min:0', // 新規
                'pitches_thrown' => 'nullable|integer|min:0', // 新規
            ]);
        }

        $validatedData = $request->validate($rules);

        DB::beginTransaction();
        try {
            $player->update([
                'team_id' => $validatedData['team_id'],
                'name' => $validatedData['player_name'],
                'role' => $validatedData['role'],
                'jersey_number' => $validatedData['jersey_number'],
                'date_of_birth' => $validatedData['date_of_birth'],
                'height' => $validatedData['height'] ?? $player->height,
                'weight' => $validatedData['weight'] ?? $player->weight,
                'specialty' => $validatedData['specialty'] ?? $player->specialty,
                'description' => $validatedData['description'] ?? $player->description,
                'hometown' => $validatedData['hometown'] ?? $player->hometown,
            ]);

            if ($player->role === '野手') {
                $hits = $validatedData['hits'] ?? 0;
                $at_bats = $validatedData['at_bats'] ?? 0;
                $walks = $validatedData['walks'] ?? 0;
                $hit_by_pitch = $validatedData['hit_by_pitch'] ?? 0;
                $sac_flies = $validatedData['sac_flies'] ?? 0;
                $doubles = $validatedData['doubles'] ?? 0;
                $triples = $validatedData['triples'] ?? 0;
                $home_runs = $validatedData['home_runs'] ?? 0;

                $batting_average = ($at_bats > 0) ? round($hits / $at_bats, 3) : 0.000;
                $obp_denominator = $at_bats + $walks + $hit_by_pitch + $sac_flies;
                $on_base_percentage = ($obp_denominator > 0) ? round(($hits + $walks + $hit_by_pitch) / $obp_denominator, 3) : 0.000;
                $singles = $hits - $doubles - $triples - $home_runs;
                $total_bases = $singles + (2 * $doubles) + (3 * $triples) + (4 * $home_runs);
                $slugging_percentage = ($at_bats > 0) ? round($total_bases / $at_bats, 3) : 0.000;
                $ops = $on_base_percentage + $slugging_percentage;

                YearlyBattingStat::updateOrCreate(
                    ['player_id' => $player->id, 'year' => $validatedData['year']],
                    [
                        'team_id' => $validatedData['team_id'],
                        'games' => $validatedData['games'] ?? 0,
                        'plate_appearances' => $validatedData['plate_appearances'] ?? 0,
                        'at_bats' => $at_bats,
                        'hits' => $hits,
                        'doubles' => $doubles,
                        'triples' => $triples,
                        'home_runs' => $home_runs,
                        'rbi' => $validatedData['rbi'] ?? 0,
                        'stolen_bases' => $validatedData['stolen_bases'] ?? 0,
                        'caught_stealing' => $validatedData['caught_stealing'] ?? 0,
                        'strikeouts' => $validatedData['strikeouts'] ?? 0,
                        'walks' => $walks,
                        'hit_by_pitch' => $hit_by_pitch,
                        'sac_bunts' => $validatedData['sac_bunts'] ?? 0,
                        'sac_flies' => $sac_flies,
                        'double_plays' => $validatedData['double_plays'] ?? 0,
                        'errors' => $validatedData['errors'] ?? 0,
                        'runs_scored' => $validatedData['runs_scored'] ?? 0,
                        'batting_average' => $batting_average,
                        'on_base_percentage' => $on_base_percentage,
                        'slugging_percentage' => $slugging_percentage,
                        'ops' => $ops,
                        'ops_plus' => $validatedData['ops_plus'] ?? null,
                        'wrc_plus' => $validatedData['wrc_plus'] ?? null,
                    ]
                );
                YearlyPitchingStat::where('player_id', $player->id)
                                  ->where('year', $validatedData['year'])
                                  ->delete();
            } elseif ($player->role === '投手') {
                $innings_pitched = $validatedData['innings_pitched'] ?? 0.0;
                $walks_allowed = $validatedData['walks_allowed'] ?? 0;
                $hits_allowed = $validatedData['hits_allowed'] ?? 0;
                $strikeouts_pitched = $validatedData['strikeouts_pitched'] ?? 0;
                $earned_runs = $validatedData['earned_runs'] ?? 0;

                // ★★★ 投球指標の計算 ★★★
                $earned_run_average = ($innings_pitched > 0) ? round(($earned_runs * 9) / $innings_pitched, 2) : 0.00;
                $whip = ($innings_pitched > 0) ? round(($walks_allowed + $hits_allowed) / $innings_pitched, 2) : 0.00;
                $strikeout_walk_ratio = ($walks_allowed > 0) ? round($strikeouts_pitched / $walks_allowed, 2) : 0.00;

                YearlyPitchingStat::updateOrCreate(
                    ['player_id' => $player->id, 'year' => $validatedData['year']],
                    [
                        'team_id' => $validatedData['team_id'],
                        // ★★★ ここをDBカラム名に正確にマッピングする ★★★
                        'games' => $validatedData['games'] ?? 0,
                        'starts' => $validatedData['starts'] ?? 0,
                        'wins' => $validatedData['wins'] ?? 0,
                        'losses' => $validatedData['losses'] ?? 0,
                        'saves' => $validatedData['saves'] ?? 0,
                        'holds' => $validatedData['holds'] ?? 0,
                        'innings_pitched' => $innings_pitched,
                        'hits_allowed' => $hits_allowed,
                        'home_runs_allowed' => $validatedData['home_runs_allowed'] ?? 0,
                        'walks_allowed' => $walks_allowed,
                        'hit_by_pitch_allowed' => $validatedData['hit_by_pitch_allowed'] ?? 0,
                        'strikeouts_pitched' => $strikeouts_pitched,
                        'runs_allowed' => $validatedData['runs_allowed'] ?? 0,
                        'earned_runs' => $earned_runs,
                        'pitches_thrown' => $validatedData['pitches_thrown'] ?? 0,

                        // 計算結果を保存
                        'earned_run_average' => $earned_run_average,
                        'whip' => $whip,
                        'strikeout_walk_ratio' => $strikeout_walk_ratio,
                    ]
                );
                YearlyBattingStat::where('player_id', $player->id)
                                 ->where('year', $validatedData['year'])
                                 ->delete();
            }

            DB::commit();
            return redirect()->route('players.index')->with('success', '選手情報が正常に更新されました！');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("選手更新エラー: " . $e->getMessage());
            return back()->withInput()->withErrors(['error' => '選手の更新中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Player $player)
    {
        DB::beginTransaction();
        try {
            $player->delete();

            DB::commit();
            return redirect()->route('players.index')->with('success', '選手が正常に削除されました！');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("選手削除エラー: " . $e->getMessage());
            return back()->withErrors(['error' => '選手の削除中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }
}