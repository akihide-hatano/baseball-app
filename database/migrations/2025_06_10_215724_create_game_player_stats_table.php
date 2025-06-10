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
            // 複数ポジションを持つ場合、中間テーブルにするか、カンマ区切りにするか、主ポジションのみにするか検討が必要
            // 今回は主ポジションのみとして、position_id で紐付け
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


            // 投球成績
            $table->decimal('innings_pitched', 4, 1)->default(0.0); // 投球回
            $table->integer('earned_runs')->default(0); // 自責点
            $table->integer('runs_allowed')->default(0); // 失点 (投手が関与した失点)
            $table->integer('hits_allowed')->default(0); // 被安打
            $table->integer('home_runs_allowed')->default(0); // 被本塁打
            $table->integer('walks_allowed')->default(0); // 与四球
            $table->integer('strikeouts_pitched')->default(0); // 奪三振 (投手)
            $table->integer('pitches_thrown')->default(0); // 投球数

            // 投手の勝敗セーブホールド
            $table->boolean('is_winner_pitcher')->default(false); // 勝ち投手か
            $table->boolean('is_loser_pitcher')->default(false); // 負け投手か
            $table->boolean('is_save_pitcher')->default(false); // セーブ投手か
            $table->boolean('is_hold_pitcher')->default(false); // ホールド投手か

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