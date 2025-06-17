<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Player;
use App\Models\PlayerBattingAbility;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PlayerBattingAbilityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Player $player)
    {
        // フォームで選択できる年度のリストを作成 (例: 現在の年-5年から現在の年+1年まで)
        $currentYear = Carbon::now()->year;
        $years = range( $currentYear -5, $currentYear +1 );

        // dump() で渡されるデータを確認 (デバッグ用。後で削除またはコメントアウト)
        // dump('Debug: Player for Ability Creation', $player);
        // dump('Debug: Available Years', $years);

        return view( 'player_batting_abilities.create',compact('player','years'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Player $player,PlayerBattingAbility $playerBattingAbility)
    {
        //validationのruleを定義
        $validatedData = $request->validate([
            'year'=> 'required|integer|min:1900|max:2100|unique:player_batting_abilities,year,NULL,id,player_id,' . $player->id,
            'contact_power' => 'nullable|integer|min:0|max:100',
            'power' => 'nullable|integer|min:0|max:100',
            'speed' => 'nullable|integer|min:0|max:100',
            'fielding' => 'nullable|integer|min:0|max:100',
            'throwing' => 'nullable|integer|min:0|max:100',
            'reaction' => 'nullable|integer|min:0|max:100',
            'overall_rank' => 'nullable|integer|min:0|max:100',
            'special_skills' => 'nullable|string|max:500',
        ]);
        // dump() でバリデーション後のデータを確認 (デバッグ用。後で削除またはコメントアウト)
        dump('Debug: Validated Ability Data for Update', $validatedData);

        DB::beginTransaction();
        try{
            // PlayerBattingAbilityモデルを使用してデータを更新
            $playerBattingAbility->update($validatedData);
            DB::commit(); // トランザクションコミット
            // 成功メッセージと共に選手詳細ページへリダイレクト
            return redirect()->route('players.show', $player->id)->with('success', '打撃能力データが正常に更新されました！');
        }catch (\Exception $e) {
            DB::rollBack(); // エラー時はロールバック
            Log::error("打撃能力更新エラー: " . $e->getMessage(), ['exception' => $e, 'request' => $request->all()]);
            // エラーメッセージと共にフォームへ戻す
            return back()->withInput()->withErrors(['error' => '打撃能力データの更新中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Player $player,PlayerBattingAbility $playerBattingAbility,)
    {
        //フォームで選択できる年度のリストを作成
        $currentYear = Carbon::now()->year;
        $years = range($currentYear - 5,$currentYear +1);
        // dump() で渡されるデータを確認 (デバッグ用。後で削除またはコメントアウト)
        // dump('Debug: Player for Ability Edit', $player);
        // dump('Debug: PlayerBattingAbility to Edit', $playerBattingAbility);
        // dump('Debug: Available Years', $years);
        return view('player_batting_abilities.edit', compact('player', 'playerBattingAbility', 'years'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Player $player, PlayerBattingAbility $playerBattingAbility)
    {
         // バリデーションルールを定義
        $validatedData = $request->validate([
            // year は、更新対象のIDを除外してユニークネスをチェック
            'year' => 'required|integer|min:1900|max:2100|unique:player_batting_abilities,year,' . $playerBattingAbility->id . ',id,player_id,' . $player->id,
            'contact_power' => 'nullable|integer|min:0|max:100',
            'power' => 'nullable|integer|min:0|max:100',
            'speed' => 'nullable|integer|min:0|max:100',
            'fielding' => 'nullable|integer|min:0|max:100',
            'throwing' => 'nullable|integer|min:0|max:100',
            'reaction' => 'nullable|integer|min:0|max:100',
            'overall_rank' => 'nullable|integer|min:0|max:100',
            'special_skills' => 'nullable|string|max:500',
        ]);

        // dump() でバリデーション後のデータを確認 (デバッグ用。後で削除またはコメントアウト)
        // dump('Debug: Validated Ability Data for Update', $validatedData);

        DB::beginTransaction(); // トランザクション開始
        try {
            // PlayerBattingAbilityモデルを使用してデータを更新
            $playerBattingAbility->update($validatedData);

            DB::commit(); // トランザクションコミット

            // 成功メッセージと共に選手詳細ページへリダイレクト
            return redirect()->route('players.show', $player->id)->with('success', '打撃能力データが正常に更新されました！');

        } catch (\Exception $e) {
            DB::rollBack(); // エラー時はロールバック
            Log::error("打撃能力更新エラー: " . $e->getMessage(), ['exception' => $e, 'request' => $request->all()]);
            // エラーメッセージと共にフォームへ戻す
            return back()->withInput()->withErrors(['error' => '打撃能力データの更新中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Player $player, PlayerBattingAbility $playerBattingAbility)
    {
        DB::beginTransaction(); // トランザクション開始
        try {
            $playerBattingAbility->delete(); // データを削除

            DB::commit(); // トランザクションコミット

            // 成功メッセージと共に選手詳細ページへリダイレクト
            return redirect()->route('players.show', $player->id)->with('success', '打撃能力データが正常に削除されました！');

        } catch (\Exception $e) {
            DB::rollBack(); // エラー時はロールバック
            Log::error("打撃能力削除エラー: " . $e->getMessage(), ['exception' => $e]);
            // エラーメッセージと共にリダイレクト
            return back()->withErrors(['error' => '打撃能力データの削除中にエラーが発生しました: ' . $e->getMessage()]);
        }
    }
}
