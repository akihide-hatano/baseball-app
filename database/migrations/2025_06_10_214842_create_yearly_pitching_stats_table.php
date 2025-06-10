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
            $table->integer('year')->unique(); // 年度 (ユニーク制約で各年度1レコードのみ)
            $table->integer('games')->default(0); // 総登板試合数 (プロ野球全体)
            $table->decimal('innings_pitched', 4, 1)->default(0.0); // 総投球回
            $table->integer('wins')->default(0); // 総勝利数
            $table->integer('losses')->default(0); // 総敗北数
            $table->integer('saves')->default(0); // 総セーブ数
            $table->integer('holds')->default(0); // 総ホールド数
            $table->integer('strikeouts')->default(0); // 総奪三振数
            $table->integer('walks_allowed')->default(0); // 総与四球数
            $table->integer('hit_by_pitch_allowed')->default(0); // 総与死球数
            $table->integer('earned_runs')->default(0); // 総自責点
            $table->integer('runs_allowed')->default(0); // 総失点
            $table->integer('hits_allowed')->default(0); // 総被安打数
            $table->integer('home_runs_allowed')->default(0); // 総被本塁打数
            $table->decimal('earned_run_average', 4, 2)->default(0.00); // 全体防御率
            $table->decimal('whip', 4, 2)->default(0.00); // 全体WHIP

            $table->timestamps();
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