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
        Schema::create('players', function (Blueprint $table) {
            $table->id();
            // ★追加: 所属チームID (teamsテーブルへの外部キー, 移籍やフリーのためnullable)
            $table->foreignId('team_id')->nullable()->constrained()->onDelete('set null');
            // ★追加: 選手名
            $table->string('name');
            // ★追加: 背番号 (NULL許容)
            $table->integer('jersey_number')->nullable();
            // ★追加: 生年月日
            $table->date('date_of_birth')->nullable();
            // ★追加: 身長 (cm)
            $table->integer('height')->nullable();
            // ★追加: 体重 (kg)
            $table->integer('weight')->nullable();
            // ★追加: 特技/特徴（例: 広角打法, スプリットなど）
            $table->string('specialty')->nullable();
            // ★追加: 出身地
            $table->string('hometown')->nullable();
            $table->timestamps();

            // チーム内での背番号のユニーク制約（同じチーム内で同じ背番号は不可）
            $table->unique(['team_id', 'jersey_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('players');
    }
};