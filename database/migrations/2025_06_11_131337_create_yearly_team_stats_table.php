<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * データベースにyearly_team_statsテーブルを作成します。
     */
    public function up(): void
    {
        Schema::create('yearly_team_stats', function (Blueprint $table) {
            $table->id(); // 主キー (ID) を自動増分で設定
            $table->foreignId('team_id')->constrained()->onDelete('cascade'); // チームID (外部キー)
            $table->integer('year'); // 成績の年度 (例: 2023, 2024)
            $table->integer('wins')->default(0); // 勝利数
            $table->integer('losses')->default(0); // 敗戦数
            $table->integer('draws')->default(0); // 引き分け数
            $table->decimal('winning_percentage', 4, 3)->default(0.000); // 勝率 (例: 0.600)
            $table->integer('games_behind')->nullable(); // ゲーム差 (NULL許容)
            $table->integer('rank')->nullable(); // リーグ順位 (NULL許容)
            $table->string('league_result')->nullable(); // リーグ戦結果 (例: 'リーグ優勝', 'Aクラス', 'Bクラス'など)
            $table->string('postseason_result')->nullable(); // ポストシーズン結果 (例: '日本一', 'CS敗退'など)

            $table->timestamps(); // created_at と updated_at カラムを自動で追加

            // 同じチームの同じ年度の成績は1つのみというユニーク制約
            $table->unique(['team_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     * yearly_team_statsテーブルを削除します。
     */
    public function down(): void
    {
        Schema::dropIfExists('yearly_team_stats');
    }
};
