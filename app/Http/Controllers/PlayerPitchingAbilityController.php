<?php

namespace App\Http\Controllers;

use App\Models\Player; // Playerモデルをuseする
use App\Models\PlayerPitchingAbility; // PlayerPitchingAbilityモデルをuseする
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlayerPitchingAbilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // このルートはexcept(['index', 'show'])で除外されているため、通常は使用しません。
        return view('player_pitching_abilities.index');
        return "投手能力一覧ページ (このルートは使用されない想定)";
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Player $player) // Playerモデルをルートモデルバインディングで受け取る
    {
       // フォームで選択できる年度のリストを作成 (例: 現在の年-5年から現在の年+1年まで)
        $currentYear = Carbon::now()->year;
        $years = range( $currentYear -5, $currentYear +1 );

        $allPitchTypes = ['カーブ', 'スライダー', 'フォーク', 'チェンジアップ',
                'シュート', 'カットボール', 'シンカー'];
        return view( 'player_pitching_abilities.create',compact('player','years','allPitchTypes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Player $player) // Playerモデルをルートモデルバインディングで受け取る
    {
        //バリデーションルールを定義
        $rules = [
                    'year' => 'required|integer|min:1900|max:2100|unique:player_pitching_abilities,year,NULL,id,player_id,' . $player->id,
                    'average_velocity' => 'nullable|numeric|min:50|max:200',
                    'pitch_stamina' => 'nullable|integer|min:1|max:99',
                    'pitch_control' => 'nullable|integer|min:1|max:99',
                    'overall_rank' => 'nullable|integer|min:1|max:99',
                    'special_skills' => 'nullable|string|max:500',
                ];

        // 変化球のバリデーションルールを動的に追加
        for ($i = 1; $i <= 7; $i++) {
            $rules['pitch_type_' . $i . '_name'] = 'nullable|string|max:50';
            $rules['pitch_type_' . $i . '_level'] = 'nullable|integer|min:0|max:7';
        }

        $validatedData = $request->validate($rules);

        DB::beginTransaction();
        try {
            // 変化球データを結合して保存形式に変換
            $pitchTypeDataForDb = [];
            for ($i = 1; $i <= 7; $i++) {
                $pitchName = $validatedData['pitch_type_' . $i . '_name'] ?? null;
                $pitchLevel = $validatedData['pitch_type_' . $i . '_level'] ?? null;

                if (!empty($pitchName) && !is_null($pitchLevel)) {
                    $pitchTypeDataForDb['pitch_type_' . $i] = $pitchName . ':' . $pitchLevel;
                } else {
                    $pitchTypeDataForDb['pitch_type_' . $i] = null; // 値がない場合はnull
                }
            }

            // PlayerPitchingAbilityモデルを使用してデータを保存
            PlayerPitchingAbility::create(array_merge([
                'player_id'        => $player->id,
                'year'             => $validatedData['year'],
                'average_velocity' => $validatedData['average_velocity'] ?? null,
                'pitch_stamina'    => $validatedData['pitch_stamina'] ?? null,
                'pitch_control'    => $validatedData['pitch_control'] ?? null,
                'overall_rank'     => $validatedData['overall_rank'] ?? null,
                'special_skills'   => $validatedData['special_skills'] ?? null,
            ], $pitchTypeDataForDb)); // 結合した変化球データをマージ

            DB::commit();
            return redirect()->route('players.show', $player->id)->with('success', '新しい投手能力データが正常に登録されました！');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("投手能力登録エラー: " . $e->getMessage(), ['exception' => $e, 'request' => $request->all()]);
            return back()->withInput()->withErrors(['error' => '投手能力データの登録中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(PlayerPitchingAbility $playerPitchingAbility)
    {
        // このルートはexcept(['index', 'show'])で除外されているため、通常は使用しません。
        return view('player_pitching_abilities.show', compact('playerPitchingAbility'));
        return "投手能力詳細ページ (このルートは使用されない想定)";
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Player $player, PlayerPitchingAbility $playerPitchingAbility) // PlayerモデルとPlayerPitchingAbilityモデルをルートモデルバインディングで受け取る
    {
        $currentYear = Carbon::now()->year;
        $years = range( $currentYear -5 , $currentYear +1);
        $allPitchTypes = [
            'ストレート', 'カーブ', 'スライダー', 'フォーク', 'チェンジアップ',
            'シュート', 'カットボール', 'シンカー', 'ツーシーム', 'スプリット', 'ナックル'
        ];

        return view('player_pitching_abilities.edit',compact('player','playerPitchingAbility','years','allPitchTypes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Player $player, PlayerPitchingAbility $playerPitchingAbility) // PlayerモデルとPlayerPitchingAbilityモデルをルートモデルバインディングで受け取る
    {
// バリデーションルールを定義
        $rules = [
            // year は、更新対象のIDを除外してユニークネスをチェック
            'year' => 'required|integer|min:1900|max:2100|unique:player_pitching_abilities,year,' . $playerPitchingAbility->id . ',id,player_id,' . $player->id,
            'average_velocity' => 'nullable|numeric|min:50|max:200',
            'pitch_stamina' => 'nullable|integer|min:1|max:99',
            'pitch_control' => 'nullable|integer|min:1|max:99',
            'overall_rank' => 'nullable|integer|min:1|max:99',
            'special_skills' => 'nullable|string|max:500',
        ];

        // 変化球のバリデーションルールを動的に追加
        for ($i = 1; $i <= 7; $i++) {
            $rules['pitch_type_' . $i . '_name'] = 'nullable|string|max:50';
            $rules['pitch_type_' . $i . '_level'] = 'nullable|integer|min:0|max:7';
        }

        $validatedData = $request->validate($rules);

        DB::beginTransaction();
        try {
            // 変化球データを結合して保存形式に変換
            $pitchTypeDataForDb = [];
            for ($i = 1; $i <= 7; $i++) {
                $pitchName = $validatedData['pitch_type_' . $i . '_name'] ?? null;
                $pitchLevel = $validatedData['pitch_type_' . $i . '_level'] ?? null;

                if (!empty($pitchName) && !is_null($pitchLevel)) {
                    $pitchTypeDataForDb['pitch_type_' . $i] = $pitchName . ':' . $pitchLevel;
                } else {
                    $pitchTypeDataForDb['pitch_type_' . $i] = null; // 値がない場合はnull
                }
            }

            // PlayerPitchingAbilityモデルを使用してデータを更新
            $playerPitchingAbility->update(array_merge([
                'year'             => $validatedData['year'],
                'average_velocity' => $validatedData['average_velocity'] ?? null,
                'pitch_stamina'    => $validatedData['pitch_stamina'] ?? null,
                'pitch_control'    => $validatedData['pitch_control'] ?? null,
                'overall_rank'     => $validatedData['overall_rank'] ?? null,
                'special_skills'   => $validatedData['special_skills'] ?? null,
            ], $pitchTypeDataForDb)); // 結合した変化球データをマージ

            DB::commit();
            return redirect()->route('players.show', $player->id)->with('success', '投手能力データが正常に更新されました！');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("投手能力更新エラー: " . $e->getMessage(), ['exception' => $e, 'request' => $request->all()]);
            return back()->withInput()->withErrors(['error' => '投手能力データの更新中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Player $player, PlayerPitchingAbility $playerPitchingAbility) // PlayerモデルとPlayerPitchingAbilityモデルをルートモデルバインディングで受け取る
    {
        // 実際にはここで削除処理を行います
        例: $playerPitchingAbility->delete(); return redirect()->route('players.show', $player->id)->with('success', '投手能力を削除しました。');
        return "選手ID: {$player->id} の投手能力ID: {$playerPitchingAbility->id} を削除する処理";
    }
}