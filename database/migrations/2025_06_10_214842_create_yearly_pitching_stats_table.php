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
        Schema::create('yearly_pitching_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade'); // 選手ID
            $table->integer('year'); // 対象年度
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('set null'); // その年の所属チーム (必要であれば)

            $table->integer('games')->default(0); // 出場試合数 (登板試合数)
            $table->integer('starts')->default(0); // 先発登板数
            $table->integer('wins')->default(0); // 勝利
            $table->integer('losses')->default(0); // 敗北
            $table->integer('saves')->default(0); // セーブ
            $table->integer('holds')->default(0); // ホールド
            $table->decimal('innings_pitched', 4, 1)->default(0.0); // 投球回
            $table->integer('hits_allowed')->default(0); // 被安打
            $table->integer('home_runs_allowed')->default(0); // 被本塁打
            $table->integer('walks_allowed')->default(0); // 与四球
            $table->integer('hit_by_pitch_allowed')->default(0); // 与死球
            $table->integer('strikeouts_pitched')->default(0); // 奪三振
            $table->integer('runs_allowed')->default(0); // 失点
            $table->integer('earned_runs')->default(0); // 自責点
            $table->integer('pitches_thrown')->default(0); // 総投球数

            // 計算で求める指標
            $table->decimal('earned_run_average', 4, 2)->default(0.00); // 防御率
            $table->decimal('whip', 4, 2)->default(0.00); // WHIP
            $table->decimal('strikeout_walk_ratio', 4, 2)->default(0.00); // K/BB

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
        Schema::dropIfExists('yearly_pitching_stats');
    }
};