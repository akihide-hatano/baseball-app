<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Player;
use App\Models\Position;
use Illuminate\Support\Facades\DB; // DBファサードを使用

class PlayerPositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $players = Player::all();
        $positions = Position::all();

        if ($players->isEmpty() || $positions->isEmpty()) {
            $this->command->warn('選手またはポジションデータがありません。先にPlayerSeederとPositionSeederを実行してください。');
            return;
        }

        // 既存の関連付けをクリア
        // player_position テーブルが空であることを確認するために truncate を追加
        DB::table('player_position')->truncate();

        // 投手ポジションのIDを取得 (確実に存在すると仮定)
        $pitcherPosition = Position::where('name', '投手')->first();
        if (!$pitcherPosition) {
             $this->command->error('エラー: ポジションデータに「投手」がありません。PositionSeederを確認してください。');
             return;
        }
        $pitcherPositionId = $pitcherPosition->id;


        // 全選手に対してループ
        foreach ($players as $player) {
            $assignedPositionIds = [];

            // 1. まず、選手にランダムな数のポジションを割り当てる (最低1つ、最大3つ程度)
            $numberOfPositions = rand(1, 3);
            $randomPositions = $positions->random($numberOfPositions);

            foreach ($randomPositions as $pos) {
                $assignedPositionIds[] = $pos->id;
            }

            // 2. 投手となる選手を一定数確保するため、player_position に pitcherPositionId を必ず追加する
            //    ここでは、全選手の約30%を投手として設定する
            if (rand(1, 100) <= 30) { // 30%の確率でその選手を投手にする
                // もし既に投手ポジションが割り当てられていなければ追加
                if (!in_array($pitcherPositionId, $assignedPositionIds)) {
                    $assignedPositionIds[] = $pitcherPositionId;
                }
            }

            // 中間テーブルに挿入
            foreach ($assignedPositionIds as $posId) { // ★この行を修正★
                DB::table('player_position')->insert([
                    'player_id' => $player->id,
                    'position_id' => $posId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        $this->command->info('選手とポジションの関連付けデータを作成しました。');
    }
}