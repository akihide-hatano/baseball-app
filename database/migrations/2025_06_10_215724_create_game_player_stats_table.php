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
        Schema::create('game_player_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('game_id')->constrained()->onDelete('cascade'); // 試合ID
            $table->foreignId('player_id')->constrained()->onDelete('cascade'); // 選手ID
            $table->foreignId('team_id')->constrained()->onDelete('cascade'); // その試合で所属していたチーム (冗長だが取得効率のため)

            // 試合における役割
            $table->boolean('is_starter')->default(false); // 先発出場か (野手・投手共通)
            $table->integer('batting_order')->nullable(); // 打順 (1-9など, 投手はnull)
            $table->foreignId('position_id')->nullable()->constrained()->onDelete('set null'); // その試合での主守備位置 (投手も含む)

            // 打撃成績
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
            $table->integer('errors')->default(0); // 失策
            $table->integer('runs_scored')->default(0); // 得点 (その選手がホームに帰還した回数)


            // 投球成績 (野手の場合はnullを許容)
            $table->decimal('innings_pitched', 4, 1)->nullable(); // ★nullable()を追加★
            $table->integer('earned_runs')->nullable(); // ★nullable()を追加★
            $table->integer('runs_allowed')->nullable(); // ★nullable()を追加★
            $table->integer('hits_allowed')->nullable(); // ★nullable()を追加★
            $table->integer('home_runs_allowed')->nullable(); // ★nullable()を追加★
            $table->integer('walks_allowed')->nullable(); // ★nullable()を追加★
            $table->integer('strikeouts_pitched')->nullable(); // ★nullable()を追加★
            $table->integer('pitches_thrown')->nullable(); // ★nullable()を追加★

            // 投手の勝敗セーブホールド (野手の場合はnullを許容)
            $table->boolean('is_winner_pitcher')->nullable(); // ★nullable()を追加★
            $table->boolean('is_loser_pitcher')->nullable(); // ★nullable()を追加★
            $table->boolean('is_save_pitcher')->nullable(); // ★nullable()を追加★
            $table->boolean('is_hold_pitcher')->nullable(); // ★nullable()を追加★

            $table->timestamps();

            // 同じ試合で同じ選手が複数レコードを持たないようにユニーク制約
            $table->unique(['game_id', 'player_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('game_player_stats');
    }
};