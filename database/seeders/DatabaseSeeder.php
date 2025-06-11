<?php

namespace Database\Seeders;

use App\Models\Team;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 必須データのシーダーを先に実行
        $this->call(LeagueSeeder::class); // リーグデータ (チームに必要)
        $this->call(TeamSeeder::class);   // チームデータ (選手、年度別チーム成績に必要)
        $this->call(PositionSeeder::class); // ポジションデータ (選手に必要)

        // 選手関連のシーダー
        $this->call(PlayerSeeder::class);
        $this->call(PlayerPositionSeeder::class);
        $this->call(PlayerBattingAbilitySeeder::class);
        $this->call(PlayerPitchingAbilitySeeder::class);

        // 試合関連のシーダー
        $this->call(GameSeeder::class);
        $this->call(GamePlayerStatSeeder::class);

        // 年度別統計データのシーダー
        $this->call(YearlyBattingStatsSeeder::class);
        $this->call(YearlyPitchingStatsSeeder::class);
        $this->call(YearlyTeamStatSeeder::class); // ★ここを追加！順番が重要★

        // もしUserSeederなど他の認証関連のシーダーがあればここに追加
        User::factory(10)->create(); // サンプルユーザーの作成など
        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}