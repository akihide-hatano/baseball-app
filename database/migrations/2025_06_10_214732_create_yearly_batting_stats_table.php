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
        Schema::create('yearly_batting_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade'); // 選手ID
            $table->integer('year'); // 対象年度
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('set null'); // その年の所属チーム (必要であれば)

            $table->integer('games')->default(0); // 出場試合数
            $table->integer('plate_appearances')->default(0); // 打席
            $table->integer('at_bats')->default(0); // 打数
            $table->integer('hits')->default(0); // 安打
            $table->integer('doubles')->default(0); // 二塁打
            $table->integer('triples')->default(0); // 三塁打
            $table->integer('home_runs')->default(0); // 本塁打
            $table->integer('rbi')->default(0); // 打点
            $table->integer('stolen_bases')->default(0); // 盗塁
            $table->integer('caught_stealing')->default(0); // 盗塁死
            $table->integer('strikeouts')->default(0); // 三振
            $table->integer('walks')->default(0); // 四球
            $table->integer('hit_by_pitch')->default(0); // 死球
            $table->integer('sac_bunts')->default(0); // 犠打
            $table->integer('sac_flies')->default(0); // 犠飛
            $table->integer('double_plays')->default(0); // 併殺打
            $table->integer('errors')->default(0); // 失策
            $table->integer('runs_scored')->default(0); // 得点

            // 計算で求める指標（精度を考慮してdecimal型）
            $table->decimal('batting_average', 4, 3)->default(0.000); // 打率
            $table->decimal('on_base_percentage', 4, 3)->default(0.000); // 出塁率
            $table->decimal('slugging_percentage', 4, 3)->default(0.000); // 長打率
            $table->decimal('ops', 4, 3)->default(0.000); // OPS
            $table->decimal('ops_plus', 5, 1)->nullable(); // OPS+ (nullable)
            $table->decimal('wrc_plus', 5, 1)->nullable(); // wRC+ (nullable)

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
        Schema::dropIfExists('yearly_batting_stats');
    }
};