<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * teamsテーブルに日本シリーズ優勝回数とリーグ優勝回数のカラムを追加します。
     */
    public function up(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->integer('japan_series_titles')->default(0)->after('founded_at'); // 日本シリーズ優勝回数
            $table->integer('league_titles')->default(0)->after('japan_series_titles'); // リーグ優勝回数
        });
    }

    /**
     * Reverse the migrations.
     * teamsテーブルから日本シリーズ優勝回数とリーグ優勝回数のカラムを削除します。
     */
    public function down(): void
    {
        Schema::table('teams', function (Blueprint $table) {
            $table->dropColumn('japan_series_titles');
            $table->dropColumn('league_titles');
        });
    }
};