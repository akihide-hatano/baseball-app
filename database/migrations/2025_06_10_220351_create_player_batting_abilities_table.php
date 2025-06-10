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
        Schema::create('player_batting_abilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->integer('year'); // 能力の年度 (例: 2024年の能力)

            // パワプロ風能力値（Integer型で1-100など、またはS/A/Bなどの数値表現）
            $table->integer('contact_power')->default(0); // ミート力 (例: 1-100)
            $table->integer('power')->default(0); // パワー (例: 1-100)
            $table->integer('speed')->default(0); // 走力 (例: 1-100)
            $table->integer('fielding')->default(0); // 守備力 (例: 1-100)
            $table->integer('throwing')->default(0); // 肩力 (例: 1-100, 送球の強さ)
            $table->integer('reaction')->default(0); // 捕球/反応力 (例: 1-100, 守備の素早さ、捕球エラー率など)

            $table->integer('overall_rank')->nullable(); // 総合能力ランク (例: 70A, 80Sなど表示用)
            $table->text('special_skills')->nullable(); // 特殊能力（カンマ区切り文字列またはJSONなど、例: 'アベレージヒッター, 広角打法'）

            $table->timestamps();

            $table->unique(['player_id', 'year']); // 同じ選手の同じ年度の能力は1つ
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('player_batting_abilities');
    }
};