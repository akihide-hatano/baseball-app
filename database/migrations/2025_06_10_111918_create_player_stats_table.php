<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('player_stats', function (Blueprint $table) { // ★テーブル名が 'player_stats' であることを確認！★
            $table->id();
            // ★追加: 選手ID (playersテーブルへの外部キー)
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            // ★追加: 年度
            $table->integer('year');
            // ★追加: その年の所属チームID (teamsテーブルへの外部キー、NULL許容)
            $table->foreignId('team_id')->nullable()->constrained('teams')->onDelete('set null');

            // 打撃成績
            $table->integer('games')->default(0); // 試合
            $table->integer('plate_appearances')->default(0); // 打席
            $table->integer('at_bats')->default(0); // 打数
            $table->integer('hits')->default(0); // 安打
            $table->integer('doubles')->default(0); // 二塁打
            $table->integer('triples')->default(0); // 三塁打
            $table->integer('home_runs')->default(0); // 本塁打
            $table->integer('rbi')->default(0); // 打点
            $table->integer('stolen_bases')->default(0); // 盗塁
            $table->integer('caught_stealing')->default(0); // 盗塁死
            $table->integer('strikeouts')->default(0); // 三振 (打者)
            $table->integer('walks')->default(0); // 四球
            $table->integer('hit_by_pitch')->default(0); // 死球
            $table->integer('sac_bunts')->default(0); // 犠打
            $table->integer('sac_flies')->default(0); // 犠飛
            $table->integer('double_plays')->default(0); // 併殺打
            $table->decimal('batting_average', 4, 3)->default(0.000); // 打率
            $table->decimal('on_base_percentage', 4, 3)->default(0.000); // 出塁率
            $table->decimal('slugging_percentage', 4, 3)->default(0.000); // 長打率
            $table->decimal('ops', 4, 3)->default(0.000); // OPS
            $table->decimal('ops_plus', 5, 1)->nullable(); // OPS+ (Nullable)
            $table->decimal('wrc_plus', 5, 1)->nullable(); // wRC+ (Nullable)
            $table->integer('errors')->default(0); // 失策 (守備)

            // 投球成績
            $table->decimal('innings_pitched', 4, 1)->default(0.0); // 投球回
            $table->integer('wins')->default(0); // 勝利
            $table->integer('losses')->default(0); // 敗北
            $table->integer('saves')->default(0); // セーブ
            $table->integer('holds')->default(0); // ホールド
            $table->integer('strikeouts_pitching')->default(0); // 奪三振 (投手)
            $table->integer('walks_allowed')->default(0); // 与四球
            $table->integer('hit_by_pitch_allowed')->default(0); // 与死球
            $table->integer('earned_runs')->default(0); // 自責点
            $table->integer('runs_allowed')->default(0); // 失点
            $table->integer('hits_allowed')->default(0); // 被安打
            $table->decimal('earned_run_average', 4, 2)->default(0.00); // 防御率
            $table->decimal('whip', 4, 2)->default(0.00); // WHIP (Nullable)

            $table->timestamps();

            // 同じ選手が同じ年に複数レコードを持たないようにユニーク制約
            $table->unique(['player_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_stats');
    }
};