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
        Schema::create('player_pitching_abilities', function (Blueprint $table) {
            $table->id();
            // ★追加: 選手ID (playersテーブルへの外部キー)
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            // ★追加: 能力の年度
            $table->integer('year'); // 能力の年度 (例: 2024年の能力)

            // 投手能力値（Integer型で1-100など）
            $table->integer('velocity')->default(0); // 球速 (km/h)
            $table->integer('control')->default(0); // コントロール (1-100)
            $table->integer('stamina')->default(0); // スタミナ (1-100)

            // 変化球 (最大5種類程度を想定し、それぞれタイプとレベルを持つ)
            $table->string('pitch_type_1')->nullable();
            $table->integer('pitch_level_1')->nullable();
            $table->string('pitch_type_2')->nullable();
            $table->integer('pitch_level_2')->nullable();
            $table->string('pitch_type_3')->nullable();
            $table->integer('pitch_level_3')->nullable();
            $table->string('pitch_type_4')->nullable();
            $table->integer('pitch_level_4')->nullable();
            $table->string('pitch_type_5')->nullable();
            $table->integer('pitch_level_5')->nullable();

            $table->string('pitching_style')->nullable(); // 投球スタイル（例: '本格派', '技巧派', 'クローザー'）
            $table->integer('overall_rank')->nullable(); // 総合能力ランク

            $table->timestamps();

            // 同じ選手の同じ年度の能力は1つ
            $table->unique(['player_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_pitching_abilities');
    }
};