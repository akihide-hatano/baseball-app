<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\League; // Leagueモデルを使用

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // まずは既存のリーグIDを取得
        $centralLeagueId = League::where('name', 'セントラル・リーグ')->first()->id;
        $pacificLeagueId = League::where('name', 'パシフィック・リーグ')->first()->id;

        DB::table('teams')->insert([
            // セントラル・リーグ
            [
                'league_id' => $centralLeagueId,
                'team_name' => '阪神タイガース',
                'team_nickname' => '阪神',
                'location' => '兵庫県西宮市',
                'founded_at' => '1935-12-10',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $centralLeagueId,
                'team_name' => '読売ジャイアンツ',
                'team_nickname' => '巨人',
                'location' => '東京都文京区',
                'founded_at' => '1934-12-26',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $centralLeagueId,
                'team_name' => '横浜DeNAベイスターズ',
                'team_nickname' => 'DeNA',
                'location' => '神奈川県横浜市',
                'founded_at' => '1949-09-08',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $centralLeagueId,
                'team_name' => '広島東洋カープ',
                'team_nickname' => '広島',
                'location' => '広島県広島市',
                'founded_at' => '1949-12-05',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $centralLeagueId,
                'team_name' => '中日ドラゴンズ',
                'team_nickname' => '中日',
                'location' => '愛知県名古屋市',
                'founded_at' => '1936-01-15',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $centralLeagueId,
                'team_name' => '東京ヤクルトスワローズ',
                'team_nickname' => 'ヤクルト',
                'location' => '東京都新宿区',
                'founded_at' => '1950-01-12',
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
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $pacificLeagueId,
                'team_name' => '福岡ソフトバンクホークス',
                'team_nickname' => 'ソフトバンク',
                'location' => '福岡県福岡市',
                'founded_at' => '1938-10-09',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $pacificLeagueId,
                'team_name' => '東北楽天ゴールデンイーグルス',
                'team_nickname' => '楽天',
                'location' => '宮城県仙台市',
                'founded_at' => '2004-11-02',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $pacificLeagueId,
                'team_name' => '埼玉西武ライオンズ',
                'team_nickname' => '西武',
                'location' => '埼玉県所沢市',
                'founded_at' => '1949-11-26',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $pacificLeagueId,
                'team_name' => '千葉ロッテマリーンズ',
                'team_nickname' => 'ロッテ',
                'location' => '千葉県千葉市',
                'founded_at' => '1949-09-21',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'league_id' => $pacificLeagueId,
                'team_name' => '北海道日本ハムファイターズ',
                'team_nickname' => '日本ハム',
                'location' => '北海道札幌市',
                'founded_at' => '1946-01-26',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}