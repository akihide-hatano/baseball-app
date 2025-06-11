<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team; // Teamモデルをuseするのを忘れずに！
use App\Models\League; // Leagueモデルをuseするのを忘れずに！
use Illuminate\Support\Facades\DB; // DBファサードはTeamモデルを使うなら不要ですが、そのまま残しても問題ありません

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * チームデータと共に日本シリーズ優勝回数とリーグ優勝回数を生成します。
     */
    public function run(): void
    {
        $this->command->info('チームデータの生成を開始します...');

        // 既存データをクリアする (Development環境でのみ推奨)
        // DB::table('teams')->truncate(); // DBファサードでtruncateする場合
        Team::truncate(); // Eloquentモデルでtruncateする場合 (こちらが推奨)

        // まずは既存のリーグIDを取得
        // LeagueSeederが先に実行されている前提
        $centralLeagueId = League::where('name', 'セントラル・リーグ')->first()->id ?? 1;
        $pacificLeagueId = League::where('name', 'パシフィック・リーグ')->first()->id ?? 2;

        $faker = \Faker\Factory::create('ja_JP'); // Fakerのインスタンスを作成

        $teamsData = [
            // セントラル・リーグ
            [
                'league_id' => $centralLeagueId,
                'team_name' => '阪神タイガース',
                'team_nickname' => '阪神',
                'location' => '兵庫県西宮市',
                'founded_at' => '1935-12-10',
                'japan_series_titles' => $faker->numberBetween(1, 3), // 日本シリーズ優勝回数を追加
                'league_titles' => $faker->numberBetween(5, 10),     // リーグ優勝回数を追加
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $centralLeagueId,
                'team_name' => '読売ジャイアンツ',
                'team_nickname' => '巨人',
                'location' => '東京都文京区',
                'founded_at' => '1934-12-26',
                'japan_series_titles' => $faker->numberBetween(15, 25),
                'league_titles' => $faker->numberBetween(35, 45),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $centralLeagueId,
                'team_name' => '横浜DeNAベイスターズ',
                'team_nickname' => 'DeNA',
                'location' => '神奈川県横浜市',
                'founded_at' => '1949-09-08',
                'japan_series_titles' => $faker->numberBetween(1, 2),
                'league_titles' => $faker->numberBetween(2, 5),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $centralLeagueId,
                'team_name' => '広島東洋カープ',
                'team_nickname' => '広島',
                'location' => '広島県広島市',
                'founded_at' => '1949-12-05',
                'japan_series_titles' => $faker->numberBetween(3, 5),
                'league_titles' => $faker->numberBetween(7, 10),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $centralLeagueId,
                'team_name' => '中日ドラゴンズ',
                'team_nickname' => '中日',
                'location' => '愛知県名古屋市',
                'founded_at' => '1936-01-15',
                'japan_series_titles' => $faker->numberBetween(1, 2),
                'league_titles' => $faker->numberBetween(8, 12),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $centralLeagueId,
                'team_name' => '東京ヤクルトスワローズ',
                'team_nickname' => 'ヤクルト',
                'location' => '東京都新宿区',
                'founded_at' => '1950-01-12',
                'japan_series_titles' => $faker->numberBetween(5, 7),
                'league_titles' => $faker->numberBetween(8, 12),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // パシフィック・リーグ
            [
                'league_id' => $pacificLeagueId,
                'team_name' => 'オリックス・バファローズ',
                'team_nickname' => 'オリックス',
                'location' => '大阪府大阪市',
                'founded_at' => '1936-01-23',
                'japan_series_titles' => $faker->numberBetween(4, 6),
                'league_titles' => $faker->numberBetween(10, 15),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $pacificLeagueId,
                'team_name' => '福岡ソフトバンクホークス',
                'team_nickname' => 'ソフトバンク',
                'location' => '福岡県福岡市',
                'founded_at' => '1938-10-09',
                'japan_series_titles' => $faker->numberBetween(10, 12),
                'league_titles' => $faker->numberBetween(18, 22),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $pacificLeagueId,
                'team_name' => '東北楽天ゴールデンイーグルス',
                'team_nickname' => '楽天',
                'location' => '宮城県仙台市',
                'founded_at' => '2004-11-02',
                'japan_series_titles' => $faker->numberBetween(0, 1),
                'league_titles' => $faker->numberBetween(0, 1),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $pacificLeagueId,
                'team_name' => '埼玉西武ライオンズ',
                'team_nickname' => '西武',
                'location' => '埼玉県所沢市',
                'founded_at' => '1949-11-26',
                'japan_series_titles' => $faker->numberBetween(10, 13),
                'league_titles' => $faker->numberBetween(20, 25),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $pacificLeagueId,
                'team_name' => '千葉ロッテマリーンズ',
                'team_nickname' => 'ロッテ',
                'location' => '千葉県千葉市',
                'founded_at' => '1949-09-21',
                'japan_series_titles' => $faker->numberBetween(3, 4),
                'league_titles' => $faker->numberBetween(5, 7),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $pacificLeagueId,
                'team_name' => '北海道日本ハムファイターズ',
                'team_nickname' => '日本ハム',
                'location' => '北海道札幌市',
                'founded_at' => '1946-01-26',
                'japan_series_titles' => $faker->numberBetween(2, 3),
                'league_titles' => $faker->numberBetween(6, 8),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // DB::table('teams')->insert($teamsData); // DBファサードでinsertする場合
        foreach ($teamsData as $data) {
            Team::create($data); // Eloquentモデルでcreateする場合 (こちらが推奨)
        }
        $this->command->info('チームデータの生成が完了しました。');
    }
}
