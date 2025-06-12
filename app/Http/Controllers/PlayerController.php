<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\Team;

class PlayerController extends Controller
{
    // PlayerPitchingAbilitySeederで定義されている定数に合わせてここにも定義する
    // これらはPlayerPitchingAbilitySeederが参照するためではなく、
    // 必要に応じてController内で同様のスケール変換を行う場合の参考値です。
    // 今回はpitchingAbilitiesのデータ整形では使わないため、削除するか、
    // 関連する処理の場所に移動することも検討できますが、今回は変更しません。
    // const MIN_VELOCITY = 100.0; // 仮の最小球速 (km/h)
    // const MAX_VELOCITY = 160.0; // 仮の最大球速 (km/h)

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
            },
            // PlayerPitchingAbility のリレーションをロード（以前の修正で追加済み）
            'pitchingAbilities' => function ($query) {
                $query->orderBy('year', 'desc');
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

        // 投球能力データの整形ロジックを修正
        $playerPitchingAbilitiesData = null;
        if ($player->pitchingAbilities->isNotEmpty()) {
            $latestPitchingAbility = $player->pitchingAbilities->first();

            $pitchLabels = [];
            $pitchData = [];

            // すべての変化球の種類を定義（シーダーと一致させる）
            $allPitchTypes = [
                'カーブ', 'スライダー', 'フォーク', 'チェンジアップ',
                'シュート', 'カットボール', 'シンカー'
            ];

            // データベースのカラム pitch_type_1 から pitch_type_7 までをループ
            // ここを修正: ループの条件を5から7に変更
            for ($i = 1; $i <= count($allPitchTypes); $i++) { // allPitchTypesの数だけループ
                $pitchTypeField = 'pitch_type_' . $i;
                $pitchInfo = $latestPitchingAbility->$pitchTypeField; // 例: 'カーブ:4'

                if ($pitchInfo) {
                    $parts = explode(':', $pitchInfo);
                    if (count($parts) === 2) {
                        $pitchName = trim($parts[0]);
                        $pitchLevel = (int)trim($parts[1]);

                        $pitchLabels[] = $pitchName;
                        $pitchData[] = $pitchLevel;
                    } else {
                        // データ形式が不正な場合は、球種名とレベル0をセット
                        $pitchName = $allPitchTypes[$i - 1] ?? '不明'; // 該当する球種名を取得
                        $pitchLabels[] = $pitchName;
                        $pitchData[] = 0;
                    }
                } else {
                    // DBカラムがnullの場合、対応する球種名をラベルとし、レベルを0とする
                    $pitchName = $allPitchTypes[$i - 1] ?? '不明'; // 該当する球種名を取得
                    $pitchLabels[] = $pitchName;
                    $pitchData[] = 0;
                }
            }

            // 変化球データが存在する場合のみ、データを設定
            if (!empty($pitchLabels)) {
                $playerPitchingAbilitiesData = [
                    'labels' => $pitchLabels,
                    'data' => $pitchData,
                ];
            }
        }
        // ここまで投球能力データの整形ロジックを修正

        // ビューにデータを渡す
        return view('players.show', compact('player', 'playerBattingAbilitiesData', 'playerPitchingAbilitiesData'));
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