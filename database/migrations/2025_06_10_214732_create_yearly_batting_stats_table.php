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
            $table->integer('year')->unique(); // 年度 (ユニーク制約で各年度1レコードのみ)
            $table->integer('games')->default(0); // 総試合数 (プロ野球全体)
            $table->integer('at_bats')->default(0); // 総打数
            $table->integer('hits')->default(0); // 総安打数
            $table->integer('doubles')->default(0); // 総二塁打数
            $table->integer('triples')->default(0); // 総三塁打数
            $table->integer('home_runs')->default(0); // 総本塁打数
            $table->integer('rbi')->default(0); // 総打点
            $table->integer('stolen_bases')->default(0); // 総盗塁数
            $table->integer('strikeouts')->default(0); // 総三振数
            $table->integer('walks')->default(0); // 総四球数
            $table->integer('hit_by_pitch')->default(0); // 総死球数
            $table->integer('sac_bunts')->default(0); // 総犠打数
            $table->integer('sac_flies')->default(0); // 総犠飛数
            $table->integer('double_plays')->default(0); // 総併殺打数
            $table->decimal('batting_average', 4, 3)->default(0.000); // 全体打率
            $table->decimal('on_base_percentage', 4, 3)->default(0.000); // 全体出塁率
            $table->decimal('slugging_percentage', 4, 3)->default(0.000); // 全体長打率
            $table->decimal('ops', 4, 3)->default(0.000); // 全体OPS

            $table->timestamps();
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